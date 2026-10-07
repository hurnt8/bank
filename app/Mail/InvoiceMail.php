<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Services\InvoicePdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        $ref    = $this->invoice->reference;
        $locale = $this->invoice->client->locale ?? 'fr';

        $subjects = [
            'fr' => 'Facture ' . $ref . ' — ' . site_name(),
            'en' => 'Invoice ' . $ref . ' — ' . site_name(),
            'es' => 'Factura ' . $ref . ' — ' . site_name(),
            'pl' => 'Faktura ' . $ref . ' — ' . site_name(),
            'bg' => 'Фактура ' . $ref . ' — ' . site_name(),
            'hu' => 'Számla ' . $ref . ' — ' . site_name(),
            'it' => 'Fattura ' . $ref . ' — ' . site_name(),
            'de' => 'Rechnung ' . $ref . ' — ' . site_name(),
            'lt' => 'Sąskaita faktūra ' . $ref . ' — ' . site_name(),
            'ro' => 'Factura ' . $ref . ' — ' . site_name(),
            'lv' => 'Rēķins ' . $ref . ' — ' . site_name(),
            'nl' => 'Factuur ' . $ref . ' — ' . site_name(),
            'pt' => 'Fatura ' . $ref . ' — ' . site_name(),
        ];

        return new Envelope(subject: $subjects[$locale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'locale'  => $this->invoice->client->locale ?? 'fr',
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => InvoicePdf::render($this->invoice), InvoicePdf::filename($this->invoice))
                ->withMime('application/pdf'),
        ];
    }
}
