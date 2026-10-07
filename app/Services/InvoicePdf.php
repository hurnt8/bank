<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

/** Génère le PDF d'une facture (avec les coordonnées de règlement : IBAN / BIC) dans la langue du client. */
class InvoicePdf
{
    public static function render(Invoice $invoice): string
    {
        $invoice->loadMissing(['client', 'admin']);

        $previous = app()->getLocale();
        app()->setLocale($invoice->client->locale ?: 'fr');

        try {
            return Pdf::loadView('pdf.invoice', ['invoice' => $invoice])->setPaper('a4')->output();
        } finally {
            app()->setLocale($previous);
        }
    }

    public static function filename(Invoice $invoice): string
    {
        return 'Invoice-' . $invoice->reference . '.pdf';
    }
}
