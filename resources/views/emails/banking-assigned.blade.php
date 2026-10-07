@php
$texts = [
    'fr' => ['title'=>'Coordonnées bancaires disponibles','sub'=>'Votre espace client','intro'=>'Votre IBAN vient de vous être <strong>attribué</strong>.','body'=>'Connectez-vous à votre espace client, section "Mes coordonnées bancaires", pour les consulter.','btn'=>'Voir mes coordonnées','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
    'en' => ['title'=>'Banking details available','sub'=>'Your client space','intro'=>'An IBAN has just been <strong>assigned</strong> to you.','body'=>'Log in to your client space, "My banking details" section, to view them.','btn'=>'View my details','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
    'es' => ['title'=>'Datos bancarios disponibles','sub'=>'Su espacio de cliente','intro'=>'Se le acaba de <strong>asignar</strong> un IBAN.','body'=>'Inicie sesión en su espacio de cliente, sección "Mis datos bancarios", para consultarlos.','btn'=>'Ver mis datos','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
    'pl' => ['title'=>'Dane bankowe dostępne','sub'=>'Twój obszar klienta','intro'=>'Właśnie <strong>przypisano</strong> Ci numer IBAN.','body'=>'Zaloguj się do swojego obszaru klienta, sekcja "Moje dane bankowe", aby je zobaczyć.','btn'=>'Zobacz moje dane','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
    'bg' => ['title'=>'Банкови данни налични','sub'=>'Вашето клиентско пространство','intro'=>'Току-що ви беше <strong>назначен</strong> IBAN.','body'=>'Влезте в клиентското си пространство, раздел "Моите банкови данни", за да ги видите.','btn'=>'Виж данните ми','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
    'hu' => ['title'=>'Banki adatok elérhetők','sub'=>'Ügyfélfiókja','intro'=>'Most kapott egy <strong>hozzárendelt</strong> IBAN-t.','body'=>'Jelentkezzen be ügyfélfiókjába, "Banki adataim" szekció, megtekintéshez.','btn'=>'Adataim megtekintése','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
    'it' => ['title'=>'Dati bancari disponibili','sub'=>'Il tuo spazio cliente','intro'=>'Ti è appena stato <strong>assegnato</strong> un IBAN.','body'=>'Accedi al tuo spazio cliente, sezione "I miei dati bancari", per consultarli.','btn'=>'Vedi i miei dati','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
    'de' => ['title'=>'Bankdaten verfügbar','sub'=>'Ihr Kundenbereich','intro'=>'Ihnen wurde soeben eine IBAN <strong>zugewiesen</strong>.','body'=>'Melden Sie sich in Ihrem Kundenbereich im Abschnitt "Meine Bankdaten" an, um sie einzusehen.','btn'=>'Meine Daten ansehen','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
    'lt' => ['title'=>'Banko duomenys prieinami','sub'=>'Jūsų kliento sritis','intro'=>'Jums ką tik buvo <strong>priskirtas</strong> IBAN.','body'=>'Prisijunkite prie savo kliento srities, skyriuje "Mano banko duomenys", kad juos peržiūrėtumėte.','btn'=>'Peržiūrėti duomenis','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
    'ro' => ['title'=>'Date bancare disponibile','sub'=>'Spațiul dumneavoastră de client','intro'=>'Vi s-a <strong>atribuit</strong> un IBAN.','body'=>'Conectați-vă la spațiul dumneavoastră de client, secțiunea "Datele mele bancare", pentru a le consulta.','btn'=>'Vezi datele mele','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
    'lv' => ['title'=>'Bankas dati pieejami','sub'=>'Jūsu klienta zona','intro'=>'Jums tikko tika <strong>piešķirts</strong> IBAN.','body'=>'Pierakstieties savā klienta zonā, sadaļā "Mani bankas dati", lai tos skatītu.','btn'=>'Skatīt manus datus','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
    'nl' => ['title'=>'Bankgegevens beschikbaar','sub'=>'Uw klantomgeving','intro'=>'Er is zojuist een IBAN aan u <strong>toegewezen</strong>.','body'=>'Log in op uw klantomgeving, sectie "Mijn bankgegevens", om ze te bekijken.','btn'=>'Mijn gegevens bekijken','closing'=>'Met vriendelijke groet,','team'=>'Het team van ' . site_name()],
    'pt' => ['title'=>'Dados bancários disponíveis','sub'=>'O seu espaço de cliente','intro'=>'Acabou de lhe ser <strong>atribuído</strong> um IBAN.','body'=>'Inicie sessão no seu espaço de cliente, secção "Os meus dados bancários", para os consultar.','btn'=>'Ver os meus dados','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
    'hr' => ['title'=>'Bankovni podaci dostupni','sub'=>'Vaš klijentski prostor','intro'=>'Upravo vam je <strong>dodijeljen</strong> IBAN.','body'=>'Prijavite se u svoj klijentski prostor, odjeljak "Moji bankovni podaci", kako biste ih pregledali.','btn'=>'Pogledaj moje podatke','closing'=>'S poštovanjem,','team'=>'Tim ' . site_name()],
    'sl' => ['title'=>'Bančni podatki na voljo','sub'=>'Vaš odjemalski prostor','intro'=>'Pravkar vam je bil <strong>dodeljen</strong> IBAN.','body'=>'Prijavite se v svoj odjemalski prostor, razdelek "Moji bančni podatki", da si jih ogledate.','btn'=>'Poglej moje podatke','closing'=>'Lep pozdrav,','team'=>'Ekipa ' . site_name()],
    'mt' => ['title'=>'Dettalji bankarji disponibbli','sub'=>'L-ispazju tiegħek bħala klijent','intro'=>'Għadu kemm ġie <strong>assenjat</strong> IBAN lilek.','body'=>'Idħol fl-ispazju tiegħek bħala klijent, fit-taqsima "Id-dettalji bankarji tiegħi", biex tarahom.','btn'=>'Ara d-dettalji tiegħi','closing'=>'Bir-rispett,','team'=>'It-tim ta’ ' . site_name()],
    'sk' => ['title'=>'Bankové údaje sú dostupné','sub'=>'Váš klientsky priestor','intro'=>'Práve vám bol <strong>pridelený</strong> IBAN.','body'=>'Prihláste sa do svojho klientskeho priestoru, sekcia "Moje bankové údaje", a zobrazte si ich.','btn'=>'Zobraziť moje údaje','closing'=>'S pozdravom,','team'=>'Tím ' . site_name()],
];
$t = $texts[$locale ?? 'fr'] ?? $texts['fr'];
@endphp
<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    accent="teal"
    :locale="$locale"
>

  <p class="greeting">{!! $t['intro'] !!}</p>

  <p class="body-text">{{ $t['body'] }}</p>

  <div class="btn-wrap">
    <a href="{{ route('client.app.payment-methods') }}" class="btn">{{ $t['btn'] }}</a>
  </div>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
