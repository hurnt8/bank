<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $activationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('onboarding.mail_subject', ['site' => site_name()], $this->user->locale ?? 'fr')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verify-account');
    }

    public function attachments(): array
    {
        return [];
    }
}
