<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\CardRequest;
use Illuminate\Support\Facades\Auth;

class CardRequestController extends Controller
{
    /** Le client demande sa carte Visa ; un administrateur la valide ensuite. */
    public function store()
    {
        $user = Auth::user();

        if ($user->card()->exists()) {
            return redirect()->route('client.app.cards');
        }

        if (CardRequest::where('user_id', $user->id)->where('status', CardRequest::STATUS_PENDING)->exists()) {
            return redirect()->route('client.app.cards')->with('success', __('cards.request_pending'));
        }

        CardRequest::create(['user_id' => $user->id]);

        foreach (AdminNotification::recipientAdminIds($user) as $adminId) {
            try {
                AdminNotification::forAdmin($adminId, 'account', 'Demande de carte', $user->name . ' demande une carte Visa.', ['client_id' => $user->id]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('CardRequest notify failed', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('client.app.cards')->with('success', __('cards.request_sent'));
    }
}
