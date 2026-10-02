<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $client,
        ?string $locale = null,
    ) {
        $this->locale = $locale ?? 'fr';
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Nouveau compte client créé — ' . $this->client->name,
            'en' => 'New client account created — ' . $this->client->name,
            'es' => 'Nueva cuenta de cliente creada — ' . $this->client->name,
            'pl' => 'Nowe konto klienta utworzone — ' . $this->client->name,
            'bg' => 'Създаден е нов клиентски профил — ' . $this->client->name,
            'hu' => 'Új ügyfélfiók létrehozva — ' . $this->client->name,
            'it' => 'Nuovo account cliente creato — ' . $this->client->name,
            'de' => 'Neues Kundenkonto erstellt — ' . $this->client->name,
            'lt' => 'Sukurta nauja kliento paskyra — ' . $this->client->name,
            'ro' => 'Cont nou de client creat — ' . $this->client->name,
            'lv' => 'Izveidots jauns klienta konts — ' . $this->client->name,
            'nl' => 'Nieuw klantaccount aangemaakt — ' . $this->client->name,
            'pt' => 'Nova conta de cliente criada — ' . $this->client->name,
        ];

        return new Envelope(
            subject: $subjects[$this->locale] ?? $subjects['fr'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-account',
            with: ['locale' => $this->locale],
        );
    }
}
