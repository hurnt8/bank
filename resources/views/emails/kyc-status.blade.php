@php
$texts = [
    'approved' => [
        'fr' => ['title'=>'Identité vérifiée','sub'=>'Vérification KYC','intro'=>'Bonne nouvelle : votre <strong>vérification d\'identité</strong> a été approuvée.','body'=>'Vous avez désormais accès à l\'ensemble des fonctionnalités de votre espace client.','btn'=>'Accéder à mon espace','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
        'en' => ['title'=>'Identity verified','sub'=>'KYC verification','intro'=>'Good news: your <strong>identity verification</strong> has been approved.','body'=>'You now have access to all the features of your client space.','btn'=>'Access my space','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
        'es' => ['title'=>'Identidad verificada','sub'=>'Verificación KYC','intro'=>'Buenas noticias: su <strong>verificación de identidad</strong> ha sido aprobada.','body'=>'Ahora tiene acceso a todas las funciones de su espacio de cliente.','btn'=>'Acceder a mi espacio','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
        'pl' => ['title'=>'Tożsamość zweryfikowana','sub'=>'Weryfikacja KYC','intro'=>'Dobra wiadomość: Twoja <strong>weryfikacja tożsamości</strong> została zatwierdzona.','body'=>'Masz teraz dostęp do wszystkich funkcji swojego obszaru klienta.','btn'=>'Przejdź do mojego obszaru','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
        'bg' => ['title'=>'Самоличността е проверена','sub'=>'KYC проверка','intro'=>'Добра новина: вашата <strong>проверка на самоличността</strong> беше одобрена.','body'=>'Вече имате достъп до всички функции на вашето клиентско пространство.','btn'=>'Достъп до пространството ми','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
        'hu' => ['title'=>'Személyazonosság ellenőrizve','sub'=>'KYC ellenőrzés','intro'=>'Jó hír: <strong>személyazonosság-ellenőrzését</strong> jóváhagytuk.','body'=>'Mostantól hozzáfér ügyfélfiókja összes funkciójához.','btn'=>'Belépés a fiókomba','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
        'it' => ['title'=>'Identità verificata','sub'=>'Verifica KYC','intro'=>'Buone notizie: la tua <strong>verifica dell\'identità</strong> è stata approvata.','body'=>'Ora hai accesso a tutte le funzionalità del tuo spazio cliente.','btn'=>'Accedi al mio spazio','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
        'de' => ['title'=>'Identität verifiziert','sub'=>'KYC-Überprüfung','intro'=>'Gute Nachrichten: Ihre <strong>Identitätsprüfung</strong> wurde genehmigt.','body'=>'Sie haben nun Zugriff auf alle Funktionen Ihres Kundenbereichs.','btn'=>'Zu meinem Bereich','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
        'lt' => ['title'=>'Tapatybė patvirtinta','sub'=>'KYC patikrinimas','intro'=>'Geros naujienos: jūsų <strong>tapatybės patvirtinimas</strong> buvo patvirtintas.','body'=>'Dabar turite prieigą prie visų savo kliento srities funkcijų.','btn'=>'Eiti į mano sritį','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
        'ro' => ['title'=>'Identitate verificată','sub'=>'Verificare KYC','intro'=>'Vești bune: <strong>verificarea identității</strong> dumneavoastră a fost aprobată.','body'=>'Aveți acum acces la toate funcționalitățile spațiului dumneavoastră de client.','btn'=>'Accesează spațiul meu','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
        'lv' => ['title'=>'Identitāte pārbaudīta','sub'=>'KYC pārbaude','intro'=>'Labas ziņas: jūsu <strong>identitātes pārbaude</strong> ir apstiprināta.','body'=>'Tagad jums ir pieeja visām sava klienta zonas funkcijām.','btn'=>'Atvērt manu zonu','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
        'nl' => ['title'=>'Identiteit geverifieerd','sub'=>'KYC-verificatie','intro'=>'Goed nieuws: uw <strong>identiteitsverificatie</strong> is goedgekeurd.','body'=>'U heeft nu toegang tot alle functies van uw klantomgeving.','btn'=>'Naar mijn omgeving','closing'=>'Met vriendelijke groet,','team'=>'Het team van ' . site_name()],
        'pt' => ['title'=>'Identidade verificada','sub'=>'Verificação KYC','intro'=>'Boa notícia: a sua <strong>verificação de identidade</strong> foi aprovada.','body'=>'Já tem acesso a todas as funcionalidades do seu espaço de cliente.','btn'=>'Aceder ao meu espaço','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
    ],
    'rejected' => [
        'fr' => ['title'=>'Vérification rejetée','sub'=>'Vérification KYC','intro'=>'Votre <strong>vérification d\'identité</strong> n\'a pas pu être validée.','body'=>'Motif : ' . e($kyc->rejection_reason) . '. Vous pouvez soumettre à nouveau vos documents depuis votre espace client.','btn'=>'Soumettre à nouveau','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
        'en' => ['title'=>'Verification rejected','sub'=>'KYC verification','intro'=>'Your <strong>identity verification</strong> could not be validated.','body'=>'Reason: ' . e($kyc->rejection_reason) . '. You can resubmit your documents from your client space.','btn'=>'Resubmit','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
        'es' => ['title'=>'Verificación rechazada','sub'=>'Verificación KYC','intro'=>'Su <strong>verificación de identidad</strong> no pudo ser validada.','body'=>'Motivo: ' . e($kyc->rejection_reason) . '. Puede volver a enviar sus documentos desde su espacio de cliente.','btn'=>'Reenviar','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
        'pl' => ['title'=>'Weryfikacja odrzucona','sub'=>'Weryfikacja KYC','intro'=>'Twoja <strong>weryfikacja tożsamości</strong> nie mogła zostać zatwierdzona.','body'=>'Powód: ' . e($kyc->rejection_reason) . '. Możesz ponownie przesłać dokumenty ze swojego obszaru klienta.','btn'=>'Prześlij ponownie','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
        'bg' => ['title'=>'Проверката отхвърлена','sub'=>'KYC проверка','intro'=>'Вашата <strong>проверка на самоличността</strong> не можа да бъде одобрена.','body'=>'Причина: ' . e($kyc->rejection_reason) . '. Можете да подадете отново документите си от клиентското си пространство.','btn'=>'Подай отново','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
        'hu' => ['title'=>'Ellenőrzés elutasítva','sub'=>'KYC ellenőrzés','intro'=>'<strong>Személyazonosság-ellenőrzését</strong> nem sikerült jóváhagyni.','body'=>'Indok: ' . e($kyc->rejection_reason) . '. Dokumentumait újra benyújthatja ügyfélfiókjából.','btn'=>'Újra beküldés','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
        'it' => ['title'=>'Verifica rifiutata','sub'=>'Verifica KYC','intro'=>'La tua <strong>verifica dell\'identità</strong> non ha potuto essere convalidata.','body'=>'Motivo: ' . e($kyc->rejection_reason) . '. Puoi inviare nuovamente i tuoi documenti dal tuo spazio cliente.','btn'=>'Invia di nuovo','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
        'de' => ['title'=>'Prüfung abgelehnt','sub'=>'KYC-Überprüfung','intro'=>'Ihre <strong>Identitätsprüfung</strong> konnte nicht bestätigt werden.','body'=>'Grund: ' . e($kyc->rejection_reason) . '. Sie können Ihre Dokumente erneut über Ihren Kundenbereich einreichen.','btn'=>'Erneut einreichen','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
        'lt' => ['title'=>'Patikrinimas atmestas','sub'=>'KYC patikrinimas','intro'=>'Jūsų <strong>tapatybės patvirtinimas</strong> negalėjo būti patvirtintas.','body'=>'Priežastis: ' . e($kyc->rejection_reason) . '. Galite pakartotinai pateikti dokumentus iš savo kliento srities.','btn'=>'Pateikti dar kartą','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
        'ro' => ['title'=>'Verificare respinsă','sub'=>'Verificare KYC','intro'=>'<strong>Verificarea identității</strong> dumneavoastră nu a putut fi validată.','body'=>'Motiv: ' . e($kyc->rejection_reason) . '. Puteți retrimite documentele din spațiul dumneavoastră de client.','btn'=>'Retrimite','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
        'lv' => ['title'=>'Pārbaude noraidīta','sub'=>'KYC pārbaude','intro'=>'Jūsu <strong>identitātes pārbaude</strong> nevarēja tikt apstiprināta.','body'=>'Iemesls: ' . e($kyc->rejection_reason) . '. Varat atkārtoti iesniegt dokumentus no sava klienta zonas.','btn'=>'Iesniegt atkārtoti','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
        'nl' => ['title'=>'Verificatie geweigerd','sub'=>'KYC-verificatie','intro'=>'Uw <strong>identiteitsverificatie</strong> kon niet worden gevalideerd.','body'=>'Reden: ' . e($kyc->rejection_reason) . '. U kunt uw documenten opnieuw indienen vanuit uw klantomgeving.','btn'=>'Opnieuw indienen','closing'=>'Met vriendelijke groet,','team'=>'Het team van ' . site_name()],
        'pt' => ['title'=>'Verificação rejeitada','sub'=>'Verificação KYC','intro'=>'A sua <strong>verificação de identidade</strong> não pôde ser validada.','body'=>'Motivo: ' . e($kyc->rejection_reason) . '. Pode reenviar os seus documentos a partir do seu espaço de cliente.','btn'=>'Reenviar','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
    ],
];
$t = $texts[$action][$locale ?? 'fr'] ?? $texts[$action]['fr'];
@endphp
<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    accent="{{ $action === 'approved' ? 'teal' : 'orange' }}"
    :locale="$locale"
>

  <p class="greeting">{!! $t['intro'] !!}</p>

  <p class="body-text">{!! $t['body'] !!}</p>

  <div class="btn-wrap">
    <a href="{{ route('client.app.kyc.show') }}" class="btn">{{ $t['btn'] }}</a>
  </div>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
