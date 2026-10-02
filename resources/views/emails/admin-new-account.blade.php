@php
$texts = [
    'fr' => ['title'=>'Nouveau compte client','sub'=>'Administration ' . site_name(),'intro'=>'vient de créer un compte via le site public.','lbl_email'=>'Email','lbl_phone'=>'Téléphone','lbl_date'=>'Créé le','btn'=>'Voir le client','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
    'en' => ['title'=>'New client account','sub'=>site_name() . ' Administration','intro'=>'just created an account via the public website.','lbl_email'=>'Email','lbl_phone'=>'Phone','lbl_date'=>'Created on','btn'=>'View client','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
    'es' => ['title'=>'Nueva cuenta de cliente','sub'=>'Administración ' . site_name(),'intro'=>'acaba de crear una cuenta a través del sitio público.','lbl_email'=>'Email','lbl_phone'=>'Teléfono','lbl_date'=>'Creado el','btn'=>'Ver cliente','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
    'pl' => ['title'=>'Nowe konto klienta','sub'=>'Administracja ' . site_name(),'intro'=>'właśnie utworzył(a) konto za pośrednictwem strony publicznej.','lbl_email'=>'Email','lbl_phone'=>'Telefon','lbl_date'=>'Utworzono','btn'=>'Zobacz klienta','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
    'bg' => ['title'=>'Нов клиентски профил','sub'=>'Администрация ' . site_name(),'intro'=>'току-що създаде профил през публичния сайт.','lbl_email'=>'Имейл','lbl_phone'=>'Телефон','lbl_date'=>'Създаден на','btn'=>'Виж клиента','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
    'hu' => ['title'=>'Új ügyfélfiók','sub'=>site_name() . ' Adminisztráció','intro'=>'most hozott létre fiókot a nyilvános weboldalon.','lbl_email'=>'Email','lbl_phone'=>'Telefon','lbl_date'=>'Létrehozva','btn'=>'Ügyfél megtekintése','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
    'it' => ['title'=>'Nuovo account cliente','sub'=>'Amministrazione ' . site_name(),'intro'=>'ha appena creato un account tramite il sito pubblico.','lbl_email'=>'Email','lbl_phone'=>'Telefono','lbl_date'=>'Creato il','btn'=>'Vedi cliente','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
    'de' => ['title'=>'Neues Kundenkonto','sub'=>site_name() . ' Verwaltung','intro'=>'hat gerade über die öffentliche Website ein Konto erstellt.','lbl_email'=>'E-Mail','lbl_phone'=>'Telefon','lbl_date'=>'Erstellt am','btn'=>'Kunden ansehen','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
    'lt' => ['title'=>'Nauja kliento paskyra','sub'=>site_name() . ' administravimas','intro'=>'ką tik susikūrė paskyrą per viešą svetainę.','lbl_email'=>'El. paštas','lbl_phone'=>'Telefonas','lbl_date'=>'Sukurta','btn'=>'Žiūrėti klientą','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
    'ro' => ['title'=>'Cont nou de client','sub'=>'Administrare ' . site_name(),'intro'=>'tocmai a creat un cont prin site-ul public.','lbl_email'=>'Email','lbl_phone'=>'Telefon','lbl_date'=>'Creat la','btn'=>'Vezi clientul','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
    'lv' => ['title'=>'Jauns klienta konts','sub'=>site_name() . ' administrēšana','intro'=>'tikko izveidoja kontu, izmantojot publisko vietni.','lbl_email'=>'E-pasts','lbl_phone'=>'Tālrunis','lbl_date'=>'Izveidots','btn'=>'Skatīt klientu','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
    'nl' => ['title'=>'Nieuw klantaccount','sub'=>site_name() . ' Beheer','intro'=>'heeft net een account aangemaakt via de publieke website.','lbl_email'=>'E-mail','lbl_phone'=>'Telefoon','lbl_date'=>'Aangemaakt op','btn'=>'Klant bekijken','closing'=>'Met vriendelijke groet,','team'=>'Het team van ' . site_name()],
    'pt' => ['title'=>'Nova conta de cliente','sub'=>'Administração ' . site_name(),'intro'=>'acabou de criar uma conta através do site público.','lbl_email'=>'Email','lbl_phone'=>'Telefone','lbl_date'=>'Criado em','btn'=>'Ver cliente','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
];
$t = $texts[$locale ?? 'fr'] ?? $texts['fr'];
@endphp
<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    accent="teal"
    :locale="$locale"
>

  <p class="greeting">
    <strong>{{ $client->name }}</strong> {{ $t['intro'] }}
  </p>

  <div class="panel">
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_email'] }}</span>
      <span class="panel-val">{{ $client->email }}</span>
    </div>
    @if($client->phone)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_phone'] }}</span>
      <span class="panel-val">{{ $client->phone }}</span>
    </div>
    @endif
  </div>

  <div class="btn-wrap">
    <a href="{{ route('admin.users.show', $client) }}" class="btn">{{ $t['btn'] }}</a>
  </div>

  <p class="body-text">{{ $t['lbl_date'] }} {{ $client->created_at->format('d/m/Y H:i') }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
