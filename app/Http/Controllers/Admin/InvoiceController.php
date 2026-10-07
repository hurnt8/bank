<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Mail\TransferActionMail;
use App\Models\ClientNotification;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Transfer;
use App\Models\User;
use App\Rules\ValidIban;
use App\Services\InvoicePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    // ── Helpers ──────────────────────────────────────────────────────────────

    private function isSuperAdmin(): bool
    {
        return Auth::user()->hasRole('super-admin');
    }

    private function clientsQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = User::where('type', 'client');

        if (! $this->isSuperAdmin()) {
            $adminId = Auth::id();
            $query->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId);
            });
        }

        return $query->orderBy('name');
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        if ($this->isSuperAdmin()) return;
        abort_unless($invoice->admin_id === Auth::id(), 403);
    }

    private function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = Invoice::with(['client:id,name,email', 'admin:id,name']);

        if (! $this->isSuperAdmin()) {
            $query->where('admin_id', Auth::id());
        }

        return $query;
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = $this->baseQuery();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference', 'like', "%$s%")
                  ->orWhereHas('client', fn ($q2) => $q2->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
            });
        }

        $invoices = $query->latest()->paginate(15)->appends($request->query());

        $stats = [
            'total'     => $this->baseQuery()->count(),
            'draft'     => $this->baseQuery()->where('status', 'draft')->count(),
            'sent'      => $this->baseQuery()->where('status', 'sent')->count(),
            'paid'      => $this->baseQuery()->where('status', 'paid')->count(),
            'cancelled' => $this->baseQuery()->where('status', 'cancelled')->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function create()
    {
        $clients    = $this->clientsQuery()->get();
        $currencies = Currency::codes();
        $defaultPayment = ['iban' => (string) \App\Models\SiteContact::current()->payment_iban, 'bic' => (string) \App\Models\SiteContact::current()->payment_bic];
        $transfers = $this->transferChoices();
        return view('admin.invoices.create', compact('clients', 'currencies', 'defaultPayment', 'transfers'));
    }

    public function store(Request $request)
    {
        $this->normalizePayment($request);

        $data = $request->validate([
            'client_id'   => 'required|exists:users,id',
            'transfer_id' => 'nullable|exists:transfers,id',
            'issue_date'  => 'required|date',
            'due_date'    => 'nullable|date|after_or_equal:issue_date',
            'currency'    => 'required|string|in:' . implode(',', Currency::codes()),
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:1000',
            'note'        => 'nullable|string|max:500',
            'payment_iban' => ['nullable', 'string', 'max:40', new ValidIban()],
            'payment_bic'  => ['nullable', 'string', 'regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/'],
            'payment_holder' => ['nullable', 'string', 'max:100'],
            'payment_type'   => ['nullable', 'in:sepa,instant'],
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        // Build items with totals
        $items    = [];
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $lineTotal  = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
            $subtotal  += $lineTotal;
            $items[]    = [
                'description' => $item['description'],
                'quantity'    => (float) $item['quantity'],
                'unit_price'  => (float) $item['unit_price'],
                'total'       => $lineTotal,
            ];
        }

        $taxRate   = (float) ($data['tax_rate'] ?? 0);
        $taxAmount = round($subtotal * $taxRate / 100, 2);
        $total     = round($subtotal + $taxAmount, 2);

        Invoice::create([
            'reference'   => Invoice::generateReference(),
            'admin_id'    => Auth::id(),
            'client_id'   => $data['client_id'],
            'currency'    => $data['currency'],
            'subtotal'    => $subtotal,
            'tax_rate'    => $taxRate,
            'tax_amount'  => $taxAmount,
            'total'       => $total,
            'status'      => Invoice::STATUS_DRAFT,
            'issue_date'  => $data['issue_date'],
            'due_date'    => $data['due_date'] ?? null,
            'description' => $data['description'] ?? null,
            'note'        => $data['note'] ?? null,
            'payment_iban' => $data['payment_iban'] ?? null,
            'payment_bic'  => $data['payment_bic'] ?? null,
            'payment_holder' => $data['payment_holder'] ?? null,
            'payment_type'   => $data['payment_type'] ?? null,
            'transfer_id'  => $this->ownedTransferId($data),
            'items'       => $items,
        ]);

        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Facture créée en brouillon.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['client', 'admin']);
        return view('admin.invoices.show', compact('invoice'));
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function edit(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        abort_unless($invoice->isDraft() || $invoice->isSent(), 403, 'Une facture payée ou annulée ne peut plus être modifiée.');

        $clients    = $this->clientsQuery()->get();
        $currencies = Currency::codes();
        $transfers = $this->transferChoices();
        return view('admin.invoices.edit', compact('invoice', 'clients', 'currencies', 'transfers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        abort_unless($invoice->isDraft() || $invoice->isSent(), 403);

        $this->normalizePayment($request);

        $data = $request->validate([
            'client_id'   => 'required|exists:users,id',
            'transfer_id' => 'nullable|exists:transfers,id',
            'issue_date'  => 'required|date',
            'due_date'    => 'nullable|date|after_or_equal:issue_date',
            'currency'    => 'required|string|in:' . implode(',', Currency::codes()),
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:1000',
            'note'        => 'nullable|string|max:500',
            'payment_iban' => ['nullable', 'string', 'max:40', new ValidIban()],
            'payment_bic'  => ['nullable', 'string', 'regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/'],
            'payment_holder' => ['nullable', 'string', 'max:100'],
            'payment_type'   => ['nullable', 'in:sepa,instant'],
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $items    = [];
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $lineTotal  = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
            $subtotal  += $lineTotal;
            $items[]    = [
                'description' => $item['description'],
                'quantity'    => (float) $item['quantity'],
                'unit_price'  => (float) $item['unit_price'],
                'total'       => $lineTotal,
            ];
        }

        $taxRate   = (float) ($data['tax_rate'] ?? 0);
        $taxAmount = round($subtotal * $taxRate / 100, 2);
        $total     = round($subtotal + $taxAmount, 2);

        $invoice->update([
            'client_id'   => $data['client_id'],
            'currency'    => $data['currency'],
            'subtotal'    => $subtotal,
            'tax_rate'    => $taxRate,
            'tax_amount'  => $taxAmount,
            'total'       => $total,
            'issue_date'  => $data['issue_date'],
            'due_date'    => $data['due_date'] ?? null,
            'description' => $data['description'] ?? null,
            'note'        => $data['note'] ?? null,
            'payment_iban' => $data['payment_iban'] ?? null,
            'payment_bic'  => $data['payment_bic'] ?? null,
            'payment_holder' => $data['payment_holder'] ?? null,
            'payment_type'   => $data['payment_type'] ?? null,
            'transfer_id'  => $this->ownedTransferId($data),
            'items'       => $items,
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
                         ->with('success', 'Facture mise à jour.');
    }

    /** Identifiant du virement lié, seulement s'il appartient bien au client de la facture. */
    private function ownedTransferId(array $data): ?int
    {
        if (empty($data['transfer_id'])) {
            return null;
        }

        return Transfer::where('id', $data['transfer_id'])->where('user_id', $data['client_id'])->value('id');
    }

    /** Virements du client auxquels rattacher une facture de frais (en attente ou déjà facturés). */
    private function transferChoices()
    {
        return Transfer::with('user:id,name')->where('type', 'send')
            ->whereIn('status', [Transfer::STATUS_PENDING, Transfer::STATUS_FEE_REQUIRED])
            ->whereIn('user_id', $this->clientsQuery()->pluck('id'))
            ->latest()->get();
    }

    /** IBAN : espaces retirés et majuscules ; BIC en majuscules ; valeurs vides = null. */
    private function normalizePayment(Request $request): void
    {
        $request->merge([
            'payment_iban' => strtoupper(preg_replace('/[\s\x{00A0}]+/u', '', (string) $request->input('payment_iban'))) ?: null,
            'payment_bic'  => strtoupper(preg_replace('/[[:space:]-]+/', '', (string) $request->input('payment_bic'))) ?: null,
        ]);
    }

    /** Renseigne ou corrige l'IBAN de règlement directement depuis la fiche de la facture (brouillon ou envoyée). */
    public function updatePayment(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        abort_unless($invoice->isDraft() || $invoice->isSent(), 403, 'Une facture payée ou annulée ne peut plus être modifiée.');

        $this->normalizePayment($request);

        $data = $request->validate([
            'payment_iban'   => ['required', 'string', 'max:40', new ValidIban()],
            'payment_bic'    => ['nullable', 'string', 'regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/'],
            'payment_type'   => ['nullable', 'in:sepa,instant'],
            'payment_holder' => ['nullable', 'string', 'max:100'],
        ]);

        $invoice->update([
            'payment_iban'   => $data['payment_iban'],
            'payment_bic'    => $data['payment_bic'] ?? null,
            'payment_type'   => $data['payment_type'] ?? null,
            'payment_holder' => $data['payment_holder'] ?? null,
        ]);

        return back()->with('success', 'Coordonnées de règlement enregistrées.');
    }

    // ── PDF ───────────────────────────────────────────────────────────────────

    public function pdf(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        return response(InvoicePdf::render($invoice), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . InvoicePdf::filename($invoice) . '"',
        ]);
    }

    // ── Send ──────────────────────────────────────────────────────────────────

    public function send(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        abort_unless($invoice->isDraft(), 403, 'Seule une facture en brouillon peut être envoyée.');

        $invoice->load(['client', 'admin']);

        if ($invoice->paymentIban() === '') {
            return back()->withErrors(['payment_iban' => 'Renseignez l\'IBAN de règlement de la facture (modifier la facture) avant de l\'envoyer.']);
        }

        $invoice->update([
            'status'  => Invoice::STATUS_SENT,
            'sent_at' => now(),
        ]);

        // Facture de frais d'un virement : le virement passe en « frais requis » et garde la trace de la facture
        $transfer = $invoice->linkedTransfer;
        if ($transfer && in_array($transfer->status, [Transfer::STATUS_PENDING, Transfer::STATUS_FEE_REQUIRED], true)) {
            $transfer->update([
                'status'     => Transfer::STATUS_FEE_REQUIRED,
                'invoice_id' => $invoice->id,
                'admin_id'   => Auth::id(),
                'admin_note' => 'Frais requis — facture ' . $invoice->reference,
            ]);
        }

        // E-mail au client dans sa langue : pour un virement, l'e-mail « frais requis » (nom, type, référence du virement + facture PDF)
        try {
            $mail = $transfer
                ? new TransferActionMail($transfer->fresh(['invoice', 'user']), 'fee_required')
                : new InvoiceMail($invoice);
            Mail::to($invoice->client->email)->locale($invoice->client->locale ?? 'fr')->send($mail);
        } catch (\Throwable $e) {
            Log::error('Invoice mail failed for ' . $invoice->reference . ': ' . $e->getMessage());
        }

        // Notification dans l'application
        if ($transfer) {
            ClientNotification::notifyUser(
                $invoice->client, 'system', 'app.notif_fees_required', 'app.notif_fees_required_body',
                ['amount' => number_format($invoice->total, 2, ',', ' '), 'currency' => $invoice->currency, 'reference' => $transfer->reference],
                ['transfer_id' => $transfer->id, 'reference' => $transfer->reference, 'invoice_id' => $invoice->id]
            );
        } else {
            ClientNotification::notifyUser(
                $invoice->client, 'system', 'app.notif_invoice_new', 'app.notif_invoice_new_body',
                ['reference' => $invoice->reference, 'amount' => number_format($invoice->total, 2, ',', ' '), 'currency' => $invoice->currency],
                ['invoice_id' => $invoice->id, 'reference' => $invoice->reference]
            );
        }

        return redirect()->route('admin.invoices.show', $invoice)
                         ->with('success', 'Facture envoyée au client par e-mail et par notification' . ($transfer ? ' ; le virement ' . $transfer->reference . ' est passé en « frais requis ».' : '.'));
    }

    // ── Mark paid ─────────────────────────────────────────────────────────────

    public function markPaid(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        abort_unless($invoice->isSent(), 403, 'Seule une facture envoyée peut être marquée comme payée.');

        $invoice->update([
            'status'  => Invoice::STATUS_PAID,
            'paid_at' => now(),
        ]);

        // Frais d'un virement réglés : le virement n'attend plus de paiement, il repasse « en cours de traitement »
        $transfer = $invoice->linkedTransfer;
        if ($transfer && $transfer->status === Transfer::STATUS_FEE_REQUIRED) {
            $transfer->update([
                'status'     => Transfer::STATUS_PENDING,
                'admin_note' => 'Frais réglés — facture ' . $invoice->reference,
            ]);
        }

        // Notification in-app au client
        ClientNotification::notifyUser(
            $invoice->client,
            'system',
            'app.notif_invoice_paid',
            'app.notif_invoice_paid_body',
            ['reference' => $invoice->reference, 'amount' => number_format($invoice->total, 2, ',', ' '), 'currency' => $invoice->currency],
            ['invoice_id' => $invoice->id]
        );

        return redirect()->route('admin.invoices.show', $invoice)
                         ->with('success', 'Facture marquée comme payée.');
    }

    // ── Cancel ────────────────────────────────────────────────────────────────

    public function cancel(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        abort_unless(! $invoice->isPaid(), 403, 'Une facture payée ne peut pas être annulée.');

        $invoice->update(['status' => Invoice::STATUS_CANCELLED]);

        return redirect()->route('admin.invoices.show', $invoice)
                         ->with('success', 'Facture annulée.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        abort_unless($invoice->isDraft(), 403, 'Seuls les brouillons peuvent être supprimés.');

        $invoice->delete();

        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Facture supprimée.');
    }
}
