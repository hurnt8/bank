<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\CardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardRequestController extends Controller
{
    /** Le client demande sa carte Visa (virtuelle ou physique) ; l'administration fixe les frais puis l'émet. */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->card()->exists()) {
            return redirect()->route('client.app.cards');
        }

        if (CardRequest::where('user_id', $user->id)->whereIn('status', [CardRequest::STATUS_PENDING, CardRequest::STATUS_AWAITING_PAYMENT])->exists()) {
            return redirect()->route('client.app.cards')->with('success', __('cards.request_pending'));
        }

        $physical = $request->input('card_type') === 'physical';

        $data = $request->validate([
            'card_type'        => ['required', 'in:virtual,physical'],
            'holder_name'      => ['required', 'string', 'min:3', 'max:26', "regex:/^[\\pL][\\pL .'\\-]*$/u"],
            'delivery_address' => [$physical ? 'required' : 'nullable', 'string', 'max:255'],
            'delivery_zip'     => [$physical ? 'required' : 'nullable', 'string', 'max:20'],
            'delivery_city'    => [$physical ? 'required' : 'nullable', 'string', 'max:100'],
            'delivery_country' => [$physical ? 'required' : 'nullable', 'string', 'max:100'],
        ]);

        if (! $physical) {
            $data = array_merge($data, ['delivery_address' => null, 'delivery_zip' => null, 'delivery_city' => null, 'delivery_country' => null]);
        }

        $cardRequest = CardRequest::create($data + ['user_id' => $user->id]);

        foreach (AdminNotification::recipientAdminIds($user) as $adminId) {
            try {
                AdminNotification::forAdmin(
                    $adminId, 'account', 'Demande de carte',
                    $user->name . ' demande une carte Visa ' . ($cardRequest->isPhysical() ? 'physique (livraison à renseigner)' : 'virtuelle') . '.',
                    ['client_id' => $user->id]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('CardRequest notify failed', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('client.app.cards')->with('success', __('cards.request_sent'));
    }
}
