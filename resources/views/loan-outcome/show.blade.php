@php
$ref = $loan->reference;

$texts = [
    'approved' => [
        'fr' => ['title'=>'Votre dossier a été validé','intro'=>'Bonne nouvelle ! Votre dossier de financement (Référence : :ref) a été validé.','steps_title'=>'Prochaines étapes','steps'=>['Nous préparons votre contrat de prêt.','Vous le recevrez par email pour lecture et signature.','Une fois signé et retourné, les fonds seront débloqués sur votre compte.'],'contact_intro'=>'Une question ? Écrivez-nous à :','contact_btn'=>'Nous contacter'],
        'en' => ['title'=>'Your file has been approved','intro'=>'Good news! Your financing file (Reference: :ref) has been approved.','steps_title'=>'Next steps','steps'=>['We are preparing your loan contract.','You will receive it by email to review and sign.','Once signed and returned, the funds will be released to your account.'],'contact_intro'=>'Have a question? Write to us at:','contact_btn'=>'Contact us'],
        'pl' => ['title'=>'Twój wniosek został zatwierdzony','intro'=>'Dobra wiadomość! Twój wniosek o finansowanie (Nr referencyjny: :ref) został zatwierdzony.','steps_title'=>'Kolejne kroki','steps'=>['Przygotowujemy Twoją umowę pożyczki.','Otrzymasz ją e-mailem do przeczytania i podpisania.','Po podpisaniu i odesłaniu środki zostaną uwolnione na Twoje konto.'],'contact_intro'=>'Masz pytanie? Napisz do nas na:','contact_btn'=>'Skontaktuj się z nami'],
        'es' => ['title'=>'Su expediente ha sido aprobado','intro'=>'¡Buenas noticias! Su expediente de financiación (Referencia: :ref) ha sido aprobado.','steps_title'=>'Próximos pasos','steps'=>['Estamos preparando su contrato de préstamo.','Lo recibirá por correo electrónico para su lectura y firma.','Una vez firmado y devuelto, los fondos se liberarán en su cuenta.'],'contact_intro'=>'¿Tiene alguna pregunta? Escríbanos a:','contact_btn'=>'Contáctenos'],
        'bg' => ['title'=>'Вашето досие беше одобрено','intro'=>'Добра новина! Вашето досие за финансиране (Референция: :ref) беше одобрено.','steps_title'=>'Следващи стъпки','steps'=>['Подготвяме вашия договор за заем.','Ще го получите по имейл за преглед и подпис.','След подписване и връщане, средствата ще бъдат освободени по вашата сметка.'],'contact_intro'=>'Имате въпрос? Пишете ни на:','contact_btn'=>'Свържете се с нас'],
        'hu' => ['title'=>'Kérelmét jóváhagyták','intro'=>'Jó hír! Finanszírozási kérelmét (Hivatkozási szám: :ref) jóváhagyták.','steps_title'=>'Következő lépések','steps'=>['Elkészítjük kölcsönszerződését.','E-mailben kapja meg elolvasásra és aláírásra.','Az aláírt és visszaküldött szerződés után az összeg felszabadításra kerül számláján.'],'contact_intro'=>'Kérdése van? Írjon nekünk:','contact_btn'=>'Kapcsolatfelvétel'],
        'it' => ['title'=>'La tua pratica è stata approvata','intro'=>'Buone notizie! La tua pratica di finanziamento (Riferimento: :ref) è stata approvata.','steps_title'=>'Prossimi passi','steps'=>['Stiamo preparando il tuo contratto di prestito.','Lo riceverai via email per la lettura e la firma.','Una volta firmato e restituito, i fondi saranno erogati sul tuo conto.'],'contact_intro'=>'Hai una domanda? Scrivici a:','contact_btn'=>'Contattaci'],
        'de' => ['title'=>'Ihr Antrag wurde genehmigt','intro'=>'Gute Nachrichten! Ihre Finanzierungsakte (Referenz: :ref) wurde genehmigt.','steps_title'=>'Nächste Schritte','steps'=>['Wir bereiten Ihren Kreditvertrag vor.','Sie erhalten ihn per E-Mail zur Durchsicht und Unterschrift.','Nach Unterzeichnung und Rücksendung werden die Mittel auf Ihr Konto freigegeben.'],'contact_intro'=>'Haben Sie eine Frage? Schreiben Sie uns an:','contact_btn'=>'Kontaktieren Sie uns'],
        'lt' => ['title'=>'Jūsų paraiška patvirtinta','intro'=>'Gera žinia! Jūsų finansavimo paraiška (Numeris: :ref) buvo patvirtinta.','steps_title'=>'Tolimesni žingsniai','steps'=>['Ruošiame jūsų paskolos sutartį.','Ją gausite el. paštu peržiūrai ir pasirašymui.','Pasirašius ir grąžinus, lėšos bus išmokėtos į jūsų sąskaitą.'],'contact_intro'=>'Turite klausimų? Rašykite mums:','contact_btn'=>'Susisiekite su mumis'],
        'ro' => ['title'=>'Cererea dumneavoastră a fost aprobată','intro'=>'Vești bune! Cererea dumneavoastră de finanțare (Referință: :ref) a fost aprobată.','steps_title'=>'Următorii pași','steps'=>['Pregătim contractul dumneavoastră de împrumut.','Îl veți primi prin e-mail pentru citire și semnare.','După semnare și returnare, fondurile vor fi deblocate în contul dumneavoastră.'],'contact_intro'=>'Aveți o întrebare? Scrieți-ne la:','contact_btn'=>'Contactați-ne'],
        'lv' => ['title'=>'Jūsu pieteikums ir apstiprināts','intro'=>'Labas ziņas! Jūsu finansējuma pieteikums (Atsauce: :ref) ir apstiprināts.','steps_title'=>'Nākamie soļi','steps'=>['Mēs sagatavojam jūsu aizdevuma līgumu.','Jūs to saņemsiet pa e-pastu izlasīšanai un parakstīšanai.','Pēc parakstīšanas un atgriešanas līdzekļi tiks izmaksāti jūsu kontā.'],'contact_intro'=>'Ir jautājums? Rakstiet mums:','contact_btn'=>'Sazinieties ar mums'],
        'nl' => ['title'=>'Uw aanvraag is goedgekeurd','intro'=>'Goed nieuws! Uw financieringsdossier (Referentie: :ref) is goedgekeurd.','steps_title'=>'Volgende stappen','steps'=>['We bereiden uw leningcontract voor.','U ontvangt het per e-mail om te lezen en te ondertekenen.','Zodra het is ondertekend en teruggestuurd, worden de middelen vrijgegeven op uw rekening.'],'contact_intro'=>'Heeft u een vraag? Schrijf ons op:','contact_btn'=>'Neem contact op'],
        'pt' => ['title'=>'O seu processo foi aprovado','intro'=>'Boas notícias! O seu processo de financiamento (Referência: :ref) foi aprovado.','steps_title'=>'Próximos passos','steps'=>['Estamos a preparar o seu contrato de empréstimo.','Vai recebê-lo por email para leitura e assinatura.','Após assinado e devolvido, os fundos serão libertados na sua conta.'],'contact_intro'=>'Tem alguma questão? Escreva-nos para:','contact_btn'=>'Contacte-nos'],
    ],
    'rejected' => [
        'fr' => ['title'=>"Votre dossier n'a pas été retenu",'intro'=>'Nous vous informons que votre dossier de financement (Référence : :ref) n\'a pas pu être approuvé.','reason_title'=>'Motif','steps_title'=>'Que faire maintenant ?','steps'=>['Vous pouvez contacter notre équipe pour obtenir plus de précisions sur cette décision.','Selon votre situation, vous pourrez être invité à soumettre un nouveau dossier.'],'contact_intro'=>'Pour toute question, écrivez-nous à :','contact_btn'=>'Nous contacter'],
        'en' => ['title'=>'Your file was not approved','intro'=>'We regret to inform you that your financing file (Reference: :ref) could not be approved.','reason_title'=>'Reason','steps_title'=>'What to do next','steps'=>['You can contact our team for more details about this decision.','Depending on your situation, you may be invited to submit a new application.'],'contact_intro'=>'For any questions, write to us at:','contact_btn'=>'Contact us'],
        'pl' => ['title'=>'Twój wniosek nie został zaakceptowany','intro'=>'Informujemy, że Twój wniosek o finansowanie (Nr referencyjny: :ref) nie mógł zostać zatwierdzony.','reason_title'=>'Powód','steps_title'=>'Co dalej?','steps'=>['Możesz skontaktować się z naszym zespołem, aby uzyskać więcej informacji na temat tej decyzji.','W zależności od Twojej sytuacji możesz zostać zaproszony do złożenia nowego wniosku.'],'contact_intro'=>'W razie pytań, napisz do nas na:','contact_btn'=>'Skontaktuj się z nami'],
        'es' => ['title'=>'Su expediente no ha sido aprobado','intro'=>'Lamentamos informarle que su expediente de financiación (Referencia: :ref) no pudo ser aprobado.','reason_title'=>'Motivo','steps_title'=>'¿Qué hacer ahora?','steps'=>['Puede contactar a nuestro equipo para obtener más detalles sobre esta decisión.','Según su situación, podría ser invitado a presentar una nueva solicitud.'],'contact_intro'=>'Para cualquier pregunta, escríbanos a:','contact_btn'=>'Contáctenos'],
        'bg' => ['title'=>'Вашето досие не беше одобрено','intro'=>'Съжаляваме да ви уведомим, че вашето досие за финансиране (Референция: :ref) не можа да бъде одобрено.','reason_title'=>'Причина','steps_title'=>'Какво да направите сега?','steps'=>['Можете да се свържете с нашия екип за повече подробности относно това решение.','В зависимост от вашата ситуация, може да бъдете поканени да подадете ново досие.'],'contact_intro'=>'При въпроси, пишете ни на:','contact_btn'=>'Свържете се с нас'],
        'hu' => ['title'=>'Kérelmét nem hagyták jóvá','intro'=>'Sajnálattal tájékoztatjuk, hogy finanszírozási kérelmét (Hivatkozási szám: :ref) nem sikerült jóváhagyni.','reason_title'=>'Indok','steps_title'=>'Mi a teendő most?','steps'=>['Kapcsolatba léphet csapatunkkal, hogy több részletet tudjon meg erről a döntésről.','Helyzetétől függően felkérhetik egy új kérelem benyújtására.'],'contact_intro'=>'Bármilyen kérdés esetén írjon nekünk:','contact_btn'=>'Kapcsolatfelvétel'],
        'it' => ['title'=>'La tua pratica non è stata approvata','intro'=>'Siamo spiacenti di informarti che la tua pratica di finanziamento (Riferimento: :ref) non ha potuto essere approvata.','reason_title'=>'Motivo','steps_title'=>'Cosa fare ora?','steps'=>['Puoi contattare il nostro team per maggiori dettagli su questa decisione.','A seconda della tua situazione, potresti essere invitato a presentare una nuova richiesta.'],'contact_intro'=>'Per qualsiasi domanda, scrivici a:','contact_btn'=>'Contattaci'],
        'de' => ['title'=>'Ihr Antrag wurde nicht genehmigt','intro'=>'Wir müssen Ihnen leider mitteilen, dass Ihre Finanzierungsakte (Referenz: :ref) nicht genehmigt werden konnte.','reason_title'=>'Grund','steps_title'=>'Was Sie jetzt tun können','steps'=>['Sie können sich an unser Team wenden, um weitere Details zu dieser Entscheidung zu erhalten.','Je nach Ihrer Situation können Sie eingeladen werden, einen neuen Antrag einzureichen.'],'contact_intro'=>'Bei Fragen schreiben Sie uns an:','contact_btn'=>'Kontaktieren Sie uns'],
        'lt' => ['title'=>'Jūsų paraiška nebuvo patvirtinta','intro'=>'Su apgailestavimu pranešame, kad jūsų finansavimo paraiška (Numeris: :ref) negalėjo būti patvirtinta.','reason_title'=>'Priežastis','steps_title'=>'Ką daryti toliau?','steps'=>['Galite susisiekti su mūsų komanda, kad gautumėte daugiau informacijos apie šį sprendimą.','Priklausomai nuo jūsų situacijos, galite būti pakviesti pateikti naują paraišką.'],'contact_intro'=>'Iškilus klausimams, rašykite mums:','contact_btn'=>'Susisiekite su mumis'],
        'ro' => ['title'=>'Cererea dumneavoastră nu a fost aprobată','intro'=>'Regretăm să vă informăm că cererea dumneavoastră de finanțare (Referință: :ref) nu a putut fi aprobată.','reason_title'=>'Motiv','steps_title'=>'Ce puteți face acum?','steps'=>['Puteți contacta echipa noastră pentru mai multe detalii despre această decizie.','În funcție de situația dumneavoastră, este posibil să fiți invitat să depuneți o nouă cerere.'],'contact_intro'=>'Pentru orice întrebare, scrieți-ne la:','contact_btn'=>'Contactați-ne'],
        'lv' => ['title'=>'Jūsu pieteikums netika apstiprināts','intro'=>'Mums ir žēl jums paziņot, ka jūsu finansējuma pieteikums (Atsauce: :ref) nevarēja tikt apstiprināts.','reason_title'=>'Iemesls','steps_title'=>'Ko darīt tālāk?','steps'=>['Varat sazināties ar mūsu komandu, lai iegūtu vairāk informācijas par šo lēmumu.','Atkarībā no jūsu situācijas, jūs varat tikt aicināti iesniegt jaunu pieteikumu.'],'contact_intro'=>'Ja ir jautājumi, rakstiet mums:','contact_btn'=>'Sazinieties ar mums'],
        'nl' => ['title'=>'Uw aanvraag is niet goedgekeurd','intro'=>'Wij moeten u helaas meedelen dat uw financieringsdossier (Referentie: :ref) niet kon worden goedgekeurd.','reason_title'=>'Reden','steps_title'=>'Wat nu te doen?','steps'=>['U kunt contact opnemen met ons team voor meer details over deze beslissing.','Afhankelijk van uw situatie kunt u worden uitgenodigd om een nieuwe aanvraag in te dienen.'],'contact_intro'=>'Voor vragen kunt u ons schrijven op:','contact_btn'=>'Neem contact op'],
        'pt' => ['title'=>'O seu processo não foi aprovado','intro'=>'Lamentamos informar que o seu processo de financiamento (Referência: :ref) não pôde ser aprovado.','reason_title'=>'Motivo','steps_title'=>'O que fazer agora?','steps'=>['Pode contactar a nossa equipa para obter mais detalhes sobre esta decisão.','Dependendo da sua situação, poderá ser convidado a submeter um novo pedido.'],'contact_intro'=>'Para qualquer questão, escreva-nos para:','contact_btn'=>'Contacte-nos'],
    ],
];

$t = $texts[$decision][$locale] ?? $texts[$decision]['fr'];
$intro = str_replace(':ref', $ref, $t['intro']);
$isApproved = $decision === 'approved';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $t['title'] }} — {{ site_name() }}</title>
<style>
body{margin:0;padding:0;background:#F1F3F6;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;color:#1F2937}
.wrap{max-width:600px;margin:0 auto;min-height:100vh;background:#FFFFFF}
.hdr{background:#0E3B2E;padding:36px 40px;text-align:center}
.status-badge{display:inline-flex;align-items:center;gap:8px;padding:7px 18px;border-radius:999px;font-size:.78rem;font-weight:700;letter-spacing:.02em;margin-bottom:16px}
.status-badge--ok{background:rgba(21,128,61,.18);color:#4ADE80}
.status-badge--no{background:rgba(185,28,28,.18);color:#F87171}
.hdr-title{font-size:1.4rem;font-weight:800;color:#FFFFFF;margin:0;letter-spacing:-.01em;line-height:1.35}
.body{padding:36px 40px}
.ref{font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9CA3AF;margin-bottom:6px}
.intro{font-size:1rem;color:#374151;line-height:1.7;margin-bottom:28px}
.reason-box{background:#FDECEC;border:1px solid #F5C6C6;border-left:4px solid #B91C1C;border-radius:10px;padding:16px 20px;margin-bottom:28px}
.reason-box strong{display:block;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#991B1B;margin-bottom:6px}
.reason-box p{margin:0;font-size:.92rem;color:#7A1F1F;line-height:1.65}
.steps-title{font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;margin-bottom:14px}
.steps{list-style:none;padding:0;margin:0 0 32px}
.steps li{display:flex;gap:14px;padding:14px 0;border-bottom:1px solid #F1F3F6}
.steps li:last-child{border-bottom:none}
.step-num{flex-shrink:0;width:28px;height:28px;border-radius:50%;background:#0E3B2E;color:#DEC066;font-size:.8rem;font-weight:800;display:flex;align-items:center;justify-content:center}
.step-text{font-size:.9rem;color:#374151;line-height:1.6;padding-top:3px}
.contact-box{background:#F8F9FB;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;text-align:center}
.contact-box p{margin:0 0 10px;font-size:.85rem;color:#6B7280}
.contact-btn{display:inline-block;padding:10px 26px;border-radius:8px;font-size:.88rem;font-weight:700;text-decoration:none;color:#ffffff;background:#0E3B2E}
.footer{padding:24px 40px;text-align:center;font-size:.7rem;color:#9CA3AF}
@media only screen and (max-width:600px){
  .hdr,.body{padding-left:24px!important;padding-right:24px!important}
}
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <div class="status-badge {{ $isApproved ? 'status-badge--ok' : 'status-badge--no' }}">
      @if($isApproved) ✓ {{ __('app.status_validated', [], $locale) }} @else ✕ {{ __('app.status_rejected', [], $locale) }} @endif
    </div>
    <h1 class="hdr-title">{{ $t['title'] }}</h1>
  </div>

  <div class="body">
    <div class="ref">{{ $ref }}</div>
    <p class="intro">{{ $intro }}</p>

    @if(!$isApproved && $loan->rejection_reason)
    <div class="reason-box">
      <strong>{{ $t['reason_title'] }}</strong>
      <p>{{ $loan->rejection_reason }}</p>
    </div>
    @endif

    <div class="steps-title">{{ $t['steps_title'] }}</div>
    <ul class="steps">
      @foreach($t['steps'] as $i => $step)
      <li>
        <div class="step-num">{{ $i + 1 }}</div>
        <div class="step-text">{{ $step }}</div>
      </li>
      @endforeach
    </ul>

    <div class="contact-box">
      <p>{{ $t['contact_intro'] }} {{ $contactEmail }}</p>
      @if($contactEmail)
      <a href="mailto:{{ $contactEmail }}" class="contact-btn">{{ $t['contact_btn'] }}</a>
      @endif
    </div>
  </div>

  <div class="footer">&copy; {{ date('Y') }} {{ site_name() }}</div>

</div>
</body>
</html>
