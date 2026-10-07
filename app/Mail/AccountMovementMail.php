<?php

namespace App\Mail;

use App\Models\AccountMovement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Services\OperationPdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** E-mail envoyé au client quand son compte est crédité ou débité par l'administration. */
class AccountMovementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $client, public AccountMovement $movement) {}

    private function clientLocale(): string
    {
        return $this->client->locale ?: 'fr';
    }

    public function envelope(): Envelope
    {
        $amount = number_format($this->movement->amount, 2, ',', ' ') . ' ' . $this->movement->currency;
        $key    = $this->movement->type === 'credit' ? 'movement.mail_credit_subject' : 'movement.mail_debit_subject';

        return new Envelope(subject: __($key, ['amount' => $amount, 'site' => site_name()], $this->clientLocale()));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-movement',
            with: ['client' => $this->client, 'movement' => $this->movement, 'locale' => $this->clientLocale()],
        );
    }

    /** Crédit ou débit : la facture / le justificatif de l'opération (PDF) est joint à l'e-mail. */
    public function attachments(): array
    {
        $movement = $this->movement;

        return [
            Attachment::fromData(fn () => OperationPdf::forMovement($movement), OperationPdf::movementFilename($movement))
                ->withMime('application/pdf'),
        ];
    }
}
