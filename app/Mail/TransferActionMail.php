<?php

namespace App\Mail;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Services\InvoicePdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferActionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Transfer $transfer,
        public string   $action  // 'approved' | 'rejected' | 'fee_required'
    ) {}

    public function envelope(): Envelope
    {
        $locale = $this->transfer->user->locale ?? 'fr';
        $ref    = $this->transfer->reference;
        $amount = number_format($this->transfer->amount, 2, ',', ' ') . ' ' . $this->transfer->currency;

        $subjects = [
            'approved' => [
                'fr' => "Virement {$ref} ({$amount}) — Validé",
                'en' => "Transfer {$ref} ({$amount}) — Approved",
                'es' => "Transferencia {$ref} ({$amount}) — Aprobada",
                'pl' => "Przelew {$ref} ({$amount}) — Zatwierdzony",
                'bg' => "Превод {$ref} ({$amount}) — Одобрен",
                'hu' => "Átutalás {$ref} ({$amount}) — Jóváhagyva",
                'it' => "Bonifico {$ref} ({$amount}) — Approvato",
                'de' => "Überweisung {$ref} ({$amount}) — Genehmigt",
                'lt' => "Pavedimas {$ref} ({$amount}) — Patvirtintas",
                'ro' => "Transfer {$ref} ({$amount}) — Aprobat",
                'lv' => "Pārvedums {$ref} ({$amount}) — Apstiprināts",
                'nl' => "Overschrijving {$ref} ({$amount}) — Goedgekeurd",
                'pt' => "Transferência {$ref} ({$amount}) — Aprovada",
            ],
            'rejected' => [
                'fr' => "Virement {$ref} ({$amount}) — Rejeté",
                'en' => "Transfer {$ref} ({$amount}) — Rejected",
                'es' => "Transferencia {$ref} ({$amount}) — Rechazada",
                'pl' => "Przelew {$ref} ({$amount}) — Odrzucony",
                'bg' => "Превод {$ref} ({$amount}) — Отхвърлен",
                'hu' => "Átutalás {$ref} ({$amount}) — Elutasítva",
                'it' => "Bonifico {$ref} ({$amount}) — Rifiutato",
                'de' => "Überweisung {$ref} ({$amount}) — Abgelehnt",
                'lt' => "Pavedimas {$ref} ({$amount}) — Atmestas",
                'ro' => "Transfer {$ref} ({$amount}) — Respins",
                'lv' => "Pārvedums {$ref} ({$amount}) — Noraidīts",
                'nl' => "Overschrijving {$ref} ({$amount}) — Afgewezen",
                'pt' => "Transferência {$ref} ({$amount}) — Rejeitada",
            ],
            'fee_required' => [
                'fr' => "Virement {$ref} — Frais requis",
                'en' => "Transfer {$ref} — Fees required",
                'es' => "Transferencia {$ref} — Comisiones requeridas",
                'pl' => "Przelew {$ref} — Wymagane opłaty",
                'bg' => "Превод {$ref} — Изисква се такса",
                'hu' => "Átutalás {$ref} — Díj szükséges",
                'it' => "Bonifico {$ref} — Commissioni richieste",
                'de' => "Überweisung {$ref} — Gebühren erforderlich",
                'lt' => "Pavedimas {$ref} — Reikalingas mokestis",
                'ro' => "Transfer {$ref} — Comision necesar",
                'lv' => "Pārvedums {$ref} — Nepieciešama maksa",
                'nl' => "Overschrijving {$ref} — Kosten vereist",
                'pt' => "Transferência {$ref} — Comissão necessária",
            ],
        ];

        $subject = $subjects[$this->action][$locale]
                ?? $subjects[$this->action]['fr']
                ?? "Mise à jour de votre virement {$ref}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transfer-action',
            with: [
                'transfer' => $this->transfer,
                'action'   => $this->action,
                'locale'   => $this->transfer->user->locale ?? 'fr',
            ],
        );
    }

    public function attachments(): array
    {
        // Facture de frais : le PDF (avec l'IBAN de règlement) est joint à l'e-mail
        if ($this->action === 'fee_required' && $this->transfer->invoice) {
            $invoice = $this->transfer->invoice;

            return [
                Attachment::fromData(fn () => InvoicePdf::render($invoice), InvoicePdf::filename($invoice))
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
