<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardRequest;
use App\Models\ClientNotification;
use App\Services\BankingProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardRequestController extends Controller
{
    public function index()
    {
        $requests = CardRequest::with('user')->latest()->paginate(20);

        return view('admin.card-requests.index', compact('requests'));
    }

    /** Accepte la demande : une carte Visa est émise pour le client. */
    public function approve(CardRequest $cardRequest, BankingProvisioner $provisioner)
    {
        abort_unless($cardRequest->status === CardRequest::STATUS_PENDING, 422, 'Demande déjà traitée.');

        $client = $cardRequest->user;
        $provisioner->issueCard($client, Auth::id());

        $cardRequest->update([
            'status'     => CardRequest::STATUS_APPROVED,
            'handled_by' => Auth::id(),
            'handled_at' => now(),
        ]);

        ClientNotification::notifyUser($client, 'system', 'cards.notif_approved', 'cards.notif_approved_body', [], []);

        return back()->with('success', 'Carte Visa émise pour ' . $client->name . '.');
    }

    public function reject(Request $request, CardRequest $cardRequest)
    {
        abort_unless($cardRequest->status === CardRequest::STATUS_PENDING, 422, 'Demande déjà traitée.');

        $data = $request->validate(['reason' => 'nullable|string|max:500']);

        $cardRequest->update([
            'status'     => CardRequest::STATUS_REJECTED,
            'reason'     => $data['reason'] ?? null,
            'handled_by' => Auth::id(),
            'handled_at' => now(),
        ]);

        ClientNotification::notifyUser($cardRequest->user, 'system', 'cards.notif_rejected', 'cards.notif_rejected_body', [], []);

        return back()->with('success', 'Demande refusée.');
    }
}
