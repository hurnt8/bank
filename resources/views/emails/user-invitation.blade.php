@php
$titles = [
    'fr' => 'Activez votre compte',
    'en' => 'Activate your account',
    'es' => 'Active su cuenta',
    'pl' => 'Aktywuj swoje konto',
    'bg' => 'Активирайте профила си',
    'hu' => 'Aktiválja fiókját',
    'it' => 'Attiva il tuo account',
    'de' => 'Aktivieren Sie Ihr Konto',
    'lt' => 'Aktyvuokite savo paskyrą',
    'ro' => 'Activați-vă contul',
    'lv' => 'Aktivizējiet savu kontu',
    'nl' => 'Activeer uw account',
    'pt' => 'Ative a sua conta',
    'hr' => 'Aktivirajte svoj račun',
];
$subs = [
    'fr' => site_name() . ' — Espace client',
    'en' => site_name() . ' — Client space',
    'es' => site_name() . ' — Área de clientes',
    'pl' => site_name() . ' — Obszar klienta',
    'bg' => site_name() . ' — Клиентска зона',
    'hu' => site_name() . ' — Ügyfélfiók',
    'it' => site_name() . ' — Area cliente',
    'de' => site_name() . ' — Kundenbereich',
    'lt' => site_name() . ' — Kliento sritis',
    'ro' => site_name() . ' — Spațiul de client',
    'lv' => site_name() . ' — Klienta zona',
    'nl' => site_name() . ' — Klantomgeving',
    'pt' => site_name() . ' — Área de cliente',
    'hr' => site_name() . ' — Klijentski prostor',
];
$pwaLabels = [
    'fr' => 'Une fois votre compte activé, installez notre application sur votre téléphone pour un accès plus rapide :',
    'en' => 'Once your account is activated, install our app on your phone for faster access:',
    'es' => 'Una vez activada su cuenta, instale nuestra aplicación en su teléfono para un acceso más rápido:',
    'pl' => 'Po aktywacji konta zainstaluj naszą aplikację na telefonie, aby uzyskać szybszy dostęp:',
    'bg' => 'След активиране на профила инсталирайте нашето приложение на телефона си за по-бърз достъп:',
    'hu' => 'A fiók aktiválása után telepítse alkalmazásunkat telefonjára a gyorsabb hozzáférés érdekében:',
    'it' => 'Una volta attivato il tuo account, installa la nostra app sul tuo telefono per un accesso più rapido:',
    'de' => 'Sobald Ihr Konto aktiviert ist, installieren Sie unsere App auf Ihrem Telefon für schnelleren Zugriff:',
    'lt' => 'Aktyvavę paskyrą, įsidiekite mūsų programėlę telefone, kad galėtumėte greičiau pasiekti:',
    'ro' => 'Odată ce contul este activat, instalați aplicația noastră pe telefon pentru acces mai rapid:',
    'lv' => 'Kad jūsu konts ir aktivizēts, instalējiet mūsu lietotni savā tālrunī ātrākai piekļuvei:',
    'nl' => 'Zodra uw account is geactiveerd, installeert u onze app op uw telefoon voor snellere toegang:',
    'pt' => 'Depois de ativar a sua conta, instale a nossa aplicação no seu telefone para um acesso mais rápido:',
    'hr' => 'Nakon što aktivirate svoj račun, instalirajte našu aplikaciju na telefon za brži pristup:',
];
$pwaBtns = [
    'fr' => 'Installer l\'application',
    'en' => 'Install the app',
    'es' => 'Instalar la aplicación',
    'pl' => 'Zainstaluj aplikację',
    'bg' => 'Инсталирайте приложението',
    'hu' => 'Alkalmazás telepítése',
    'it' => 'Installa l\'app',
    'de' => 'App installieren',
    'lt' => 'Įdiegti programėlę',
    'ro' => 'Instalează aplicația',
    'lv' => 'Instalēt lietotni',
    'nl' => 'App installeren',
    'pt' => 'Instalar a aplicação',
    'hr' => 'Instaliraj aplikaciju',
];
$notices = [
    'fr' => 'Si vous n\'êtes pas à l\'origine de cette création de compte, vous pouvez ignorer cet email.',
    'en' => 'If you did not request this account creation, you can ignore this email.',
    'es' => 'Si usted no solicitó la creación de esta cuenta, puede ignorar este email.',
    'pl' => 'Jeśli nie prosiłeś/aś o utworzenie tego konta, możesz zignorować ten email.',
    'bg' => 'Ако не сте инициирали създаването на този профил, можете да игнорирате този имейл.',
    'hu' => 'Ha nem Ön kezdeményezte ennek a fióknak a létrehozását, figyelmen kívül hagyhatja ezt az e-mailt.',
    'it' => 'Se non sei tu all\'origine della creazione di questo account, puoi ignorare questa email.',
    'de' => 'Wenn Sie diese Kontoerstellung nicht veranlasst haben, können Sie diese E-Mail ignorieren.',
    'lt' => 'Jei ne jūs inicijavote šios paskyros sukūrimą, galite ignoruoti šį el. laišką.',
    'ro' => 'Dacă nu dumneavoastră ați inițiat crearea acestui cont, puteți ignora acest e-mail.',
    'lv' => 'Ja šī konta izveide nav notikusi pēc jūsu pieprasījuma, varat ignorēt šo e-pastu.',
    'nl' => 'Als u niet zelf om het aanmaken van dit account heeft gevraagd, kunt u deze e-mail negeren.',
    'pt' => 'Se não foi você que solicitou a criação desta conta, pode ignorar este email.',
    'hr' => 'Ako niste vi pokrenuli otvaranje ovog računa, možete zanemariti ovu e-poruku.',
];
$title   = $titles[$locale]    ?? $titles['fr'];
$sub     = $subs[$locale]      ?? $subs['fr'];
$notice  = $notices[$locale]   ?? $notices['fr'];
$pwaText = $pwaLabels[$locale] ?? $pwaLabels['fr'];
$pwaBtn  = $pwaBtns[$locale]   ?? $pwaBtns['fr'];
@endphp

<x-email-layout
    :title="$title"
    :subtitle="$sub"
    accent="teal"
    :footerNote="$notice"
    :locale="$locale"
>

  <p class="greeting">{{ $greeting }}</p>

  <p class="body-text">{{ $resolved['{INTRO_CORPS}'] }}</p>

  <p class="body-text">{{ $resolved['{CORPS_ACTION}'] }}</p>

  <div class="btn-wrap">
    <a href="{{ $activationUrl }}" class="btn">{{ $btnLabel }}</a>
  </div>

  <p class="url-fallback">
    @php
    $fallbacks = ['fr'=>'Si le bouton ne fonctionne pas, copiez ce lien :','en'=>'If the button does not work, copy this link:','es'=>'Si el botón no funciona, copie este enlace:','pl'=>'Jeśli przycisk nie działa, skopiuj ten link:','bg'=>'Ако бутонът не работи, копирайте тази връзка:','hu'=>'Ha a gomb nem működik, másolja be ezt a linket:','it'=>'Se il pulsante non funziona, copia questo link:','de'=>'Wenn die Schaltfläche nicht funktioniert, kopieren Sie diesen Link:','lt'=>'Jei mygtukas neveikia, nukopijuokite šią nuorodą:','ro'=>'Dacă butonul nu funcționează, copiați acest link:','lv'=>'Ja poga nedarbojas, nokopējiet šo saiti:','nl'=>'Als de knop niet werkt, kopieer dan deze link:','pt'=>'Se o botão não funcionar, copie este link:','hr'=>'Ako gumb ne radi, kopirajte ovu poveznicu:'];
    @endphp
    {{ $fallbacks[$locale] ?? $fallbacks['fr'] }}<br>
    <a href="{{ $activationUrl }}">{{ $activationUrl }}</a>
  </p>

  <p class="body-text">{{ $pwaText }}</p>

  <div class="btn-wrap">
    <a href="{{ route('client.app.home') }}" class="btn">{{ $pwaBtn }}</a>
  </div>

  <div class="alert alert-warn">
    <p>{{ $resolved['{NOTICE_PERSONNEL}'] }}</p>
  </div>

  <p class="closing">
    {{ $resolved['{FORMULE_POLITESSE}'] }},<br>
    <strong>{{ $resolved['{EQUIPE}'] }}</strong>
  </p>

</x-email-layout>
