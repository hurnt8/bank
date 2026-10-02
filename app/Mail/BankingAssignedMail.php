<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BankingAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $client) {}

    public function envelope(): Envelope
    {
        $locale = $this->client->locale ?? 'fr';

        $subjects = [
            'fr' => 'Vos coordonnées bancaires sont disponibles',
            'en' => 'Your banking details are available',
            'es' => 'Sus datos bancarios están disponibles',
            'pl' => 'Twoje dane bankowe są dostępne',
            'bg' => 'Вашите банкови данни са налични',
            'hu' => 'Banki adatai elérhetők',
            'it' => 'I tuoi dati bancari sono disponibili',
            'de' => 'Ihre Bankdaten sind verfügbar',
            'lt' => 'Jūsų banko duomenys yra prieinami',
            'ro' => 'Datele dumneavoastră bancare sunt disponibile',
            'lv' => 'Jūsu bankas dati ir pieejami',
            'nl' => 'Uw bankgegevens zijn beschikbaar',
            'pt' => 'Os seus dados bancários estão disponíveis',
        ];

        return new Envelope(subject: $subjects[$locale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.banking-assigned',
            with: ['locale' => $this->client->locale ?? 'fr'],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
