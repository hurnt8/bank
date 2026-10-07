<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\CardRequest;
use App\Models\ClientNotification;
use App\Models\Invoice;
use App\Models\SiteContact;
use App\Services\BankingProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CardRequestController extends Controller
{
    public function index()
    {
        $requests = CardRequest::with(['user', 'invoice'])->latest()->paginate(20);

        return view('admin.card-requests.index', compact('requests'));
    }

    /**
     * Fixe les frais de la carte : une facture (avec l'IBAN de règlement paramétré) est créée et envoyée au client,
     * qui la retrouve dans son espace Cartes avec la référence à indiquer pour le paiement.
     */
    public function invoice(Request $request, CardRequest $cardRequest)
    {
        abort_unless($cardRequest->isOpen(), 422, 'Demande déjà traitée.');

        $request->merge([
            'payment_iban' => strtoupper(preg_replace('/[\s\x{00A0}]+/u', '', (string) $request->input('payment_iban'))) ?: null,
            'payment_bic'  => strtoupper(preg_replace('/[[:space:]-]+/', '', (string) $request->input('payment_bic'))) ?: null,
        ]);

        $data = $request->validate([
            'fee_amount'     => 'required|numeric|min:0.01|max:9999999',
            'payment_iban'   => ['nullable', 'string', 'max:40', new \App\Rules\ValidIban()],
            'payment_bic'    => ['nullable', 'string', 'regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/'],
            'payment_holder' => ['nullable', 'string', 'max:100'],
            'payment_type'   => ['nullable', 'in:sepa,instant'],
        ]);

        if (empty($data['payment_iban']) && ! SiteContact::current()->payment_iban) {
            return back()->withErrors(['payment_iban' => 'Renseignez l\'IBAN sur lequel le client réglera les frais de carte.'])->withInput()->with('open_card', $cardRequest->id);
        }

        $client   = $cardRequest->user;
        $currency = $client->currency ?: 'EUR';

        $invoice = DB::transaction(function () use ($cardRequest, $data, $client, $currency) {
            // Une facture déjà émise pour cette demande est annulée : les nouveaux frais la remplacent.
            if ($cardRequest->invoice && $cardRequest->invoice->status !== Invoice::STATUS_PAID) {
                $cardRequest->invoice->update(['status' => Invoice::STATUS_CANCELLED]);
            }

            $typeLabel = $cardRequest->isPhysical() ? 'carte physique' : 'carte virtuelle';

            $invoice = Invoice::create([
                'reference'      => Invoice::generateReference(),
                'admin_id'       => Auth::id(),
                'client_id'      => $client->id,
                'currency'       => $currency,
                'subtotal'       => $data['fee_amount'],
                'tax_rate'       => 0,
                'tax_amount'     => 0,
                'total'          => $data['fee_amount'],
                'payment_iban'   => $data['payment_iban'] ?? null,
                'payment_bic'    => $data['payment_bic'] ?? null,
                'payment_holder' => $data['payment_holder'] ?? null,
                'payment_type'   => $data['payment_type'] ?? null,
                'status'         => Invoice::STATUS_SENT,
                'issue_date'     => now()->toDateString(),
                'description'    => 'Frais de carte Visa (' . $typeLabel . ' — ' . $cardRequest->holder_name . ')',
                'items'          => [[
                    'kind'        => 'card_fee',          // libellé recomposé dans la langue du lecteur
                    'description' => 'Frais de carte Visa (' . $typeLabel . ')',
                    'quantity'    => 1,
                    'unit_price'  => $data['fee_amount'],
                    'total'       => $data['fee_amount'],
                ]],
                'sent_at'        => now(),
            ]);

            $cardRequest->update([
                'status'     => CardRequest::STATUS_AWAITING_PAYMENT,
                'fee_amount' => $data['fee_amount'],
                'invoice_id' => $invoice->id,
                'handled_by' => Auth::id(),
            ]);

            return $invoice;
        });

        $invoice->load(['client', 'admin']);

        ClientNotification::notifyUser(
            $client, 'system', 'cards.notif_fee', 'cards.notif_fee_body',
            [
                'amount'   => number_format($data['fee_amount'], 2, ',', ' '),
                'currency' => $currency,
                'type'     => __('cards.type_' . $cardRequest->card_type, [], $client->locale ?: 'fr'),
            ],
            ['invoice_id' => $invoice->id]
        );

        try {
            Mail::to($client->email)->locale($client->locale ?? 'fr')->send(new InvoiceMail($invoice));
        } catch (\Throwable $e) {
            Log::error('Card fee invoice mail failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Frais de carte facturés à ' . $client->name . ' (facture ' . $invoice->reference . ').');
    }

    /** Émet la carte (paiement des frais constaté, ou sans frais) : la facture éventuelle passe en « payée ». */
    public function approve(CardRequest $cardRequest, BankingProvisioner $provisioner)
    {
        abort_unless($cardRequest->isOpen(), 422, 'Demande déjà traitée.');

        $client = $cardRequest->user;
        $provisioner->issueCard($client, Auth::id(), $cardRequest->card_type ?: 'virtual', $cardRequest->holder_name);

        if ($cardRequest->invoice && $cardRequest->invoice->status === Invoice::STATUS_SENT) {
            $cardRequest->invoice->update(['status' => Invoice::STATUS_PAID, 'paid_at' => now()]);
        }

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
        abort_unless($cardRequest->isOpen(), 422, 'Demande déjà traitée.');

        $data = $request->validate(['reason' => 'nullable|string|max:500']);

        if ($cardRequest->invoice && $cardRequest->invoice->status === Invoice::STATUS_SENT) {
            $cardRequest->invoice->update(['status' => Invoice::STATUS_CANCELLED]);
        }

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
