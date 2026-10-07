@php
$texts = [
    'fr' => ['title'=>'Nouvelle facture','sub'=>'Espace facturation','greeting'=>'Bonjour','intro'=>'Vous avez reçu une nouvelle facture de la part de <strong>' . site_name() . '</strong>.','lbl_ref'=>'Référence','lbl_issue'=>'Date d\'émission','lbl_due'=>'Date d\'échéance','lbl_total'=>'Montant total','lbl_desc'=>'Description','lbl_qty'=>'Qté','lbl_unit'=>'Prix unit.','lbl_line_total'=>'Total','lbl_sub'=>'Sous-total','lbl_vat'=>'TVA','lbl_ttc'=>'Total TTC','lbl_note'=>'Note','body'=>'Pour toute question concernant cette facture, veuillez contacter votre conseiller ' . site_name() . '.','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
    'en' => ['title'=>'New invoice','sub'=>'Billing area','greeting'=>'Hello','intro'=>'You have received a new invoice from <strong>' . site_name() . '</strong>.','lbl_ref'=>'Reference','lbl_issue'=>'Issue date','lbl_due'=>'Due date','lbl_total'=>'Total amount','lbl_desc'=>'Description','lbl_qty'=>'Qty','lbl_unit'=>'Unit price','lbl_line_total'=>'Total','lbl_sub'=>'Subtotal','lbl_vat'=>'VAT','lbl_ttc'=>'Total incl. VAT','lbl_note'=>'Note','body'=>'For any questions about this invoice, please contact your ' . site_name() . ' advisor.','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
    'es' => ['title'=>'Nueva factura','sub'=>'Área de facturación','greeting'=>'Hola','intro'=>'Ha recibido una nueva factura de <strong>' . site_name() . '</strong>.','lbl_ref'=>'Referencia','lbl_issue'=>'Fecha de emisión','lbl_due'=>'Fecha de vencimiento','lbl_total'=>'Importe total','lbl_desc'=>'Descripción','lbl_qty'=>'Cant.','lbl_unit'=>'Precio unit.','lbl_line_total'=>'Total','lbl_sub'=>'Subtotal','lbl_vat'=>'IVA','lbl_ttc'=>'Total con IVA','lbl_note'=>'Nota','body'=>'Para cualquier consulta sobre esta factura, contacte a su asesor de ' . site_name() . '.','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
    'pl' => ['title'=>'Nowa faktura','sub'=>'Obszar rozliczeniowy','greeting'=>'Witaj','intro'=>'Otrzymałeś/aś nową fakturę od <strong>' . site_name() . '</strong>.','lbl_ref'=>'Referencja','lbl_issue'=>'Data wystawienia','lbl_due'=>'Termin płatności','lbl_total'=>'Kwota całkowita','lbl_desc'=>'Opis','lbl_qty'=>'Ilość','lbl_unit'=>'Cena jedn.','lbl_line_total'=>'Razem','lbl_sub'=>'Suma częściowa','lbl_vat'=>'VAT','lbl_ttc'=>'Razem z VAT','lbl_note'=>'Uwaga','body'=>'W razie pytań dotyczących faktury, skontaktuj się ze swoim doradcą ' . site_name() . '.','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
    'bg' => ['title'=>'Нова фактура','sub'=>'Раздел фактуриране','greeting'=>'Здравейте','intro'=>'Получихте нова фактура от <strong>' . site_name() . '</strong>.','lbl_ref'=>'Референция','lbl_issue'=>'Дата на издаване','lbl_due'=>'Срок на плащане','lbl_total'=>'Обща сума','lbl_desc'=>'Описание','lbl_qty'=>'Кол.','lbl_unit'=>'Ед. цена','lbl_line_total'=>'Общо','lbl_sub'=>'Междинна сума','lbl_vat'=>'ДДС','lbl_ttc'=>'Общо с ДДС','lbl_note'=>'Бележка','body'=>'При въпроси относно тази фактура, моля свържете се с вашия консултант от ' . site_name() . '.','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
    'hu' => ['title'=>'Új számla','sub'=>'Számlázási terület','greeting'=>'Üdvözöljük','intro'=>'Új számlát kapott a <strong>' . site_name() . '</strong>-tól.','lbl_ref'=>'Referenciaszám','lbl_issue'=>'Kiállítás dátuma','lbl_due'=>'Fizetési határidő','lbl_total'=>'Teljes összeg','lbl_desc'=>'Leírás','lbl_qty'=>'Menny.','lbl_unit'=>'Egységár','lbl_line_total'=>'Összesen','lbl_sub'=>'Részösszeg','lbl_vat'=>'ÁFA','lbl_ttc'=>'Összesen ÁFA-val','lbl_note'=>'Megjegyzés','body'=>'A számlával kapcsolatos bármilyen kérdés esetén forduljon ' . site_name() . ' tanácsadójához.','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
    'it' => ['title'=>'Nuova fattura','sub'=>'Area fatturazione','greeting'=>'Ciao','intro'=>'Hai ricevuto una nuova fattura da <strong>' . site_name() . '</strong>.','lbl_ref'=>'Riferimento','lbl_issue'=>'Data di emissione','lbl_due'=>'Data di scadenza','lbl_total'=>'Importo totale','lbl_desc'=>'Descrizione','lbl_qty'=>'Qtà','lbl_unit'=>'Prezzo unit.','lbl_line_total'=>'Totale','lbl_sub'=>'Subtotale','lbl_vat'=>'IVA','lbl_ttc'=>'Totale IVA inclusa','lbl_note'=>'Nota','body'=>'Per qualsiasi domanda relativa a questa fattura, contatta il tuo consulente ' . site_name() . '.','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
    'de' => ['title'=>'Neue Rechnung','sub'=>'Rechnungsbereich','greeting'=>'Guten Tag','intro'=>'Sie haben eine neue Rechnung von <strong>' . site_name() . '</strong> erhalten.','lbl_ref'=>'Referenz','lbl_issue'=>'Ausstellungsdatum','lbl_due'=>'Fälligkeitsdatum','lbl_total'=>'Gesamtbetrag','lbl_desc'=>'Beschreibung','lbl_qty'=>'Menge','lbl_unit'=>'Einzelpreis','lbl_line_total'=>'Summe','lbl_sub'=>'Zwischensumme','lbl_vat'=>'MwSt.','lbl_ttc'=>'Gesamtbetrag inkl. MwSt.','lbl_note'=>'Hinweis','body'=>'Bei Fragen zu dieser Rechnung wenden Sie sich bitte an Ihren ' . site_name() . '-Berater.','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
    'lt' => ['title'=>'Nauja sąskaita faktūra','sub'=>'Atsiskaitymų sritis','greeting'=>'Sveiki','intro'=>'Gavote naują sąskaitą faktūrą iš <strong>' . site_name() . '</strong>.','lbl_ref'=>'Numeris','lbl_issue'=>'Išrašymo data','lbl_due'=>'Apmokėjimo terminas','lbl_total'=>'Bendra suma','lbl_desc'=>'Aprašymas','lbl_qty'=>'Kiekis','lbl_unit'=>'Vnt. kaina','lbl_line_total'=>'Iš viso','lbl_sub'=>'Tarpinė suma','lbl_vat'=>'PVM','lbl_ttc'=>'Iš viso su PVM','lbl_note'=>'Pastaba','body'=>'Iškilus bet kokių klausimų dėl šios sąskaitos faktūros, susisiekite su savo ' . site_name() . ' konsultantu.','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
    'ro' => ['title'=>'Factură nouă','sub'=>'Secțiune facturare','greeting'=>'Bună ziua','intro'=>'Ați primit o factură nouă din partea <strong>' . site_name() . '</strong>.','lbl_ref'=>'Referință','lbl_issue'=>'Data emiterii','lbl_due'=>'Data scadenței','lbl_total'=>'Sumă totală','lbl_desc'=>'Descriere','lbl_qty'=>'Cant.','lbl_unit'=>'Preț unitar','lbl_line_total'=>'Total','lbl_sub'=>'Subtotal','lbl_vat'=>'TVA','lbl_ttc'=>'Total cu TVA','lbl_note'=>'Notă','body'=>'Pentru orice întrebare privind această factură, contactați consilierul dumneavoastră ' . site_name() . '.','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
    'lv' => ['title'=>'Jauns rēķins','sub'=>'Rēķinu sadaļa','greeting'=>'Sveiki','intro'=>'Jūs esat saņēmis jaunu rēķinu no <strong>' . site_name() . '</strong>.','lbl_ref'=>'Atsauce','lbl_issue'=>'Izrakstīšanas datums','lbl_due'=>'Apmaksas termiņš','lbl_total'=>'Kopējā summa','lbl_desc'=>'Apraksts','lbl_qty'=>'Daudz.','lbl_unit'=>'Vienības cena','lbl_line_total'=>'Kopā','lbl_sub'=>'Starpsumma','lbl_vat'=>'PVN','lbl_ttc'=>'Kopā ar PVN','lbl_note'=>'Piezīme','body'=>'Ja jums ir kādi jautājumi par šo rēķinu, sazinieties ar savu ' . site_name() . ' konsultantu.','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
    'nl' => ['title'=>'Nieuwe factuur','sub'=>'Facturatie','greeting'=>'Hallo','intro'=>'U heeft een nieuwe factuur ontvangen van <strong>' . site_name() . '</strong>.','lbl_ref'=>'Referentie','lbl_issue'=>'Factuurdatum','lbl_due'=>'Vervaldatum','lbl_total'=>'Totaalbedrag','lbl_desc'=>'Omschrijving','lbl_qty'=>'Aant.','lbl_unit'=>'Eenheidsprijs','lbl_line_total'=>'Totaal','lbl_sub'=>'Subtotaal','lbl_vat'=>'btw','lbl_ttc'=>'Totaal incl. btw','lbl_note'=>'Opmerking','body'=>'Voor vragen over deze factuur kunt u contact opnemen met uw adviseur van ' . site_name() . '.','closing'=>'Met vriendelijke groet,','team'=>'Het ' . site_name() . ' Team'],
    'pt' => ['title'=>'Nova fatura','sub'=>'Área de faturação','greeting'=>'Olá','intro'=>'Recebeu uma nova fatura da <strong>' . site_name() . '</strong>.','lbl_ref'=>'Referência','lbl_issue'=>'Data de emissão','lbl_due'=>'Data de vencimento','lbl_total'=>'Montante total','lbl_desc'=>'Descrição','lbl_qty'=>'Qtd.','lbl_unit'=>'Preço unit.','lbl_line_total'=>'Total','lbl_sub'=>'Subtotal','lbl_vat'=>'IVA','lbl_ttc'=>'Total com IVA','lbl_note'=>'Nota','body'=>'Para qualquer questão relativa a esta fatura, contacte o seu consultor ' . site_name() . '.','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
    'hr' => ['title'=>'Novi račun','sub'=>'Odjeljak za fakturiranje','greeting'=>'Pozdrav','intro'=>'Primili ste novi račun od <strong>' . site_name() . '</strong>.','lbl_ref'=>'Referenca','lbl_issue'=>'Datum izdavanja','lbl_due'=>'Datum dospijeća','lbl_total'=>'Ukupni iznos','lbl_desc'=>'Opis','lbl_qty'=>'Kol.','lbl_unit'=>'Jed. cijena','lbl_line_total'=>'Ukupno','lbl_sub'=>'Međuzbroj','lbl_vat'=>'PDV','lbl_ttc'=>'Ukupno s PDV-om','lbl_note'=>'Napomena','body'=>'Za sva pitanja vezana uz ovaj račun kontaktirajte svog savjetnika ' . site_name() . '.','closing'=>'S poštovanjem,','team'=>'Tim ' . site_name()],
];
$t = $texts[$locale] ?? $texts['fr'];
@endphp

<x-email-layout
    :title="$t['title'] . ' ' . $invoice->reference"
    :subtitle="$t['sub']"
    accent="orange"
    :locale="$locale"
>

  <p class="greeting">
    {{ $t['greeting'] }} {{ $invoice->client->name }},
  </p>

  <p class="body-text">{!! $t['intro'] !!}</p>

  {{-- Récapitulatif --}}
  <div class="panel">
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_ref'] }}</span>
      <span class="panel-val">{{ $invoice->reference }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_issue'] }}</span>
      <span class="panel-val">{{ $invoice->issue_date->format('d/m/Y') }}</span>
    </div>
    @if($invoice->due_date)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_due'] }}</span>
      <span class="panel-val">{{ $invoice->due_date->format('d/m/Y') }}</span>
    </div>
    @endif
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_total'] }}</span>
      <span class="panel-val accent">{{ number_format($invoice->total, 2, ',', ' ') }} {{ $invoice->currency }}</span>
    </div>
  </div>

  {{-- Détail des lignes --}}
  @if(count($invoice->items ?? []))
  <div class="panel" style="margin-bottom:1.5rem">
    <div class="panel-row" style="background:rgba(255,255,255,.04)">
      <span class="panel-lbl" style="flex:2;color:rgba(240,245,255,.55);font-weight:600">{{ $t['lbl_desc'] }}</span>
      <span class="panel-lbl" style="text-align:center">{{ $t['lbl_qty'] }}</span>
      <span class="panel-lbl" style="text-align:right">{{ $t['lbl_unit'] }}</span>
      <span class="panel-lbl" style="text-align:right;min-width:80px">{{ $t['lbl_line_total'] }}</span>
    </div>
    @foreach($invoice->items as $item)
    <div class="panel-row">
      <span class="panel-val" style="flex:2;text-align:left">{{ $invoice->itemDescription($item, $locale) }}</span>
      <span class="panel-val" style="text-align:center;min-width:40px">{{ $item['quantity'] ?? 1 }}</span>
      <span class="panel-val" style="text-align:right;min-width:80px">{{ number_format((float)($item['unit_price'] ?? 0), 2, ',', ' ') }}</span>
      <span class="panel-val accent" style="text-align:right;min-width:80px">{{ number_format((float)($item['total'] ?? 0), 2, ',', ' ') }} {{ $invoice->currency }}</span>
    </div>
    @endforeach
    @if($invoice->tax_rate > 0)
    <div class="panel-row"><span class="panel-lbl">{{ $t['lbl_sub'] }}</span><span class="panel-val">{{ number_format($invoice->subtotal, 2, ',', ' ') }} {{ $invoice->currency }}</span></div>
    <div class="panel-row"><span class="panel-lbl">{{ $t['lbl_vat'] }} ({{ $invoice->tax_rate }}%)</span><span class="panel-val">{{ number_format($invoice->tax_amount, 2, ',', ' ') }} {{ $invoice->currency }}</span></div>
    <div class="panel-row"><span class="panel-lbl" style="font-weight:700;color:#F0F5FF">{{ $t['lbl_ttc'] }}</span><span class="panel-val accent" style="font-size:.95rem">{{ number_format($invoice->total, 2, ',', ' ') }} {{ $invoice->currency }}</span></div>
    @endif
  </div>
  @endif

  @if($invoice->description)
  <p class="body-text">{{ $invoice->displayDescription($locale) }}</p>
  @endif

  @if($invoice->note)
  <div class="alert alert-info">
    <strong>{{ $t['lbl_note'] }}</strong>
    <p>{{ $invoice->displayNote($locale) }}</p>
  </div>
  @endif

  <p class="body-text">{{ $t['body'] }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
