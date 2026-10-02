<?php

namespace App\Mail;

use App\Models\KycVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KycStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public KycVerification $kyc,
        public string          $action // 'approved' | 'rejected'
    ) {}

    public function envelope(): Envelope
    {
        $locale = $this->kyc->user->locale ?? 'fr';

        $subjects = [
            'approved' => [
                'fr' => 'Votre identité a été vérifiée',
                'en' => 'Your identity has been verified',
                'es' => 'Su identidad ha sido verificada',
                'pl' => 'Twoja tożsamość została zweryfikowana',
                'bg' => 'Вашата самоличност беше проверена',
                'hu' => 'Személyazonosságát ellenőriztük',
                'it' => 'La tua identità è stata verificata',
                'de' => 'Ihre Identität wurde verifiziert',
                'lt' => 'Jūsų tapatybė patvirtinta',
                'ro' => 'Identitatea dumneavoastră a fost verificată',
                'lv' => 'Jūsu identitāte ir pārbaudīta',
                'nl' => 'Uw identiteit is geverifieerd',
                'pt' => 'A sua identidade foi verificada',
            ],
            'rejected' => [
                'fr' => 'Votre vérification d\'identité a été rejetée',
                'en' => 'Your identity verification was rejected',
                'es' => 'Su verificación de identidad fue rechazada',
                'pl' => 'Twoja weryfikacja tożsamości została odrzucona',
                'bg' => 'Проверката на самоличността ви беше отхвърлена',
                'hu' => 'Személyazonosság-ellenőrzését elutasítottuk',
                'it' => 'La verifica della tua identità è stata rifiutata',
                'de' => 'Ihre Identitätsprüfung wurde abgelehnt',
                'lt' => 'Jūsų tapatybės patvirtinimas atmestas',
                'ro' => 'Verificarea identității dumneavoastră a fost respinsă',
                'lv' => 'Jūsu identitātes pārbaude ir noraidīta',
                'nl' => 'Uw identiteitsverificatie is afgewezen',
                'pt' => 'A verificação da sua identidade foi rejeitada',
            ],
        ];

        $subject = $subjects[$this->action][$locale] ?? $subjects[$this->action]['fr'];

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.kyc-status',
            with: [
                'kyc'    => $this->kyc,
                'action' => $this->action,
                'locale' => $this->kyc->user->locale ?? 'fr',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
