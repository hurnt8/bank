<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignBankingRequest;
use App\Mail\BankingAssignedMail;
use App\Models\BankAccount;
use App\Models\Card;
use App\Models\ClientNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BankingController extends Controller
{
    public function edit(User $user)
    {
        abort_unless($user->hasRole('client'), 404);

        $user->load(['bankAccount', 'card']);

        return view('admin.users.banking', compact('user'));
    }

    public function store(AssignBankingRequest $request, User $user)
    {
        abort_unless($user->hasRole('client'), 404);

        $data = $request->validated();

        DB::transaction(function () use ($user, $data) {
            $user->bankAccount()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'assigned_by' => Auth::id(),
                    'iban'        => strtoupper(str_replace(' ', '', $data['iban'])),
                    'bic'         => $data['bic'] ?? null,
                    'status'      => BankAccount::STATUS_ACTIVE,
                ]
            );

            $user->card()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'assigned_by' => Auth::id(),
                    'holder_name' => $data['card_holder'],
                    'last_four'   => $data['card_last_four'],
                    'network'     => $data['card_network'],
                    'expires_at'  => $data['card_expires_at'],
                    'status'      => Card::STATUS_ACTIVE,
                ]
            );
        });

        $this->notifyClient($user);

        return back()->with('success', 'Coordonnées bancaires attribuées.');
    }

    public function toggleBlock(User $user)
    {
        abort_unless($user->hasRole('client'), 404);

        DB::transaction(function () use ($user) {
            if ($user->bankAccount) {
                $user->bankAccount->update([
                    'status' => $user->bankAccount->status === BankAccount::STATUS_ACTIVE
                        ? BankAccount::STATUS_BLOCKED
                        : BankAccount::STATUS_ACTIVE,
                ]);
            }
            if ($user->card) {
                $user->card->update([
                    'status' => $user->card->status === Card::STATUS_ACTIVE
                        ? Card::STATUS_BLOCKED
                        : Card::STATUS_ACTIVE,
                ]);
            }
        });

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->hasRole('client'), 404);

        DB::transaction(function () use ($user) {
            $user->bankAccount?->delete();
            $user->card?->delete();
        });

        return back()->with('success', 'Coordonnées bancaires retirées.');
    }

    private function notifyClient(User $user): void
    {
        ClientNotification::notifyUser(
            $user,
            'system',
            'banking.notif_assigned',
            'banking.notif_assigned_body',
            [],
            []
        );

        try {
            Mail::to($user->email)
                ->locale($user->locale ?? 'fr')
                ->send(new BankingAssignedMail($user));
        } catch (\Throwable $e) {
            Log::error('BankingAssignedMail failed: ' . $e->getMessage());
        }
    }
}
