@php
$ref    = $transfer->reference;
$amount = number_format($transfer->amount, 2, ',', ' ') . ' ' . $transfer->currency;
$bene   = $transfer->beneficiary_name;

$texts = [
    'approved' => [
        'fr' => ['title'=>'Virement validé','sub'=>'Confirmation de traitement','greeting'=>'Bonjour','intro'=>'Votre virement a été <strong>validé</strong> et traité avec succès.','lbl_ref'=>'Référence','lbl_amount'=>'Montant','lbl_bene'=>'Bénéficiaire','lbl_iban'=>'IBAN','lbl_note'=>'Note','body'=>'Le montant a été débité de votre compte. Pour toute question, contactez votre conseiller.','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
        'en' => ['title'=>'Transfer approved','sub'=>'Processing confirmation','greeting'=>'Hello','intro'=>'Your transfer has been <strong>approved</strong> and processed successfully.','lbl_ref'=>'Reference','lbl_amount'=>'Amount','lbl_bene'=>'Beneficiary','lbl_iban'=>'IBAN','lbl_note'=>'Note','body'=>'The amount has been debited from your account. For any questions, contact your advisor.','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
        'es' => ['title'=>'Transferencia aprobada','sub'=>'Confirmación de procesamiento','greeting'=>'Hola','intro'=>'Su transferencia ha sido <strong>aprobada</strong> y procesada con éxito.','lbl_ref'=>'Referencia','lbl_amount'=>'Importe','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Nota','body'=>'El importe ha sido debitado de su cuenta. Para cualquier consulta, contacte a su asesor.','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
        'pl' => ['title'=>'Przelew zatwierdzony','sub'=>'Potwierdzenie przetworzenia','greeting'=>'Witaj','intro'=>'Twój przelew został <strong>zatwierdzony</strong> i przetworzony pomyślnie.','lbl_ref'=>'Referencja','lbl_amount'=>'Kwota','lbl_bene'=>'Beneficjent','lbl_iban'=>'IBAN','lbl_note'=>'Uwaga','body'=>'Kwota została pobrana z Twojego konta. W razie pytań skontaktuj się z doradcą.','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
        'bg' => ['title'=>'Преводът е одобрен','sub'=>'Потвърждение за обработка','greeting'=>'Здравейте','intro'=>'Вашият превод беше <strong>одобрен</strong> и обработен успешно.','lbl_ref'=>'Референция','lbl_amount'=>'Сума','lbl_bene'=>'Получател','lbl_iban'=>'IBAN','lbl_note'=>'Бележка','body'=>'Сумата беше задължена от вашата сметка. При въпроси, свържете се с вашия консултант.','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
        'hu' => ['title'=>'Átutalás jóváhagyva','sub'=>'Feldolgozás visszaigazolása','greeting'=>'Üdvözöljük','intro'=>'Átutalása <strong>jóváhagyásra</strong> és sikeresen feldolgozásra került.','lbl_ref'=>'Referenciaszám','lbl_amount'=>'Összeg','lbl_bene'=>'Kedvezményezett','lbl_iban'=>'IBAN','lbl_note'=>'Megjegyzés','body'=>'Az összeget levontuk számlájáról. Bármilyen kérdés esetén forduljon tanácsadójához.','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
        'it' => ['title'=>'Bonifico approvato','sub'=>'Conferma di elaborazione','greeting'=>'Ciao','intro'=>'Il tuo bonifico è stato <strong>approvato</strong> ed elaborato con successo.','lbl_ref'=>'Riferimento','lbl_amount'=>'Importo','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Nota','body'=>'L\'importo è stato addebitato sul tuo conto. Per qualsiasi domanda, contatta il tuo consulente.','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
        'de' => ['title'=>'Überweisung genehmigt','sub'=>'Bestätigung der Bearbeitung','greeting'=>'Guten Tag','intro'=>'Ihre Überweisung wurde <strong>genehmigt</strong> und erfolgreich bearbeitet.','lbl_ref'=>'Referenz','lbl_amount'=>'Betrag','lbl_bene'=>'Begünstigter','lbl_iban'=>'IBAN','lbl_note'=>'Hinweis','body'=>'Der Betrag wurde von Ihrem Konto abgebucht. Bei Fragen wenden Sie sich bitte an Ihren Berater.','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
        'lt' => ['title'=>'Pavedimas patvirtintas','sub'=>'Apdorojimo patvirtinimas','greeting'=>'Sveiki','intro'=>'Jūsų pavedimas buvo <strong>patvirtintas</strong> ir sėkmingai apdorotas.','lbl_ref'=>'Numeris','lbl_amount'=>'Suma','lbl_bene'=>'Gavėjas','lbl_iban'=>'IBAN','lbl_note'=>'Pastaba','body'=>'Suma buvo nurašyta nuo jūsų sąskaitos. Iškilus klausimams, susisiekite su savo konsultantu.','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
        'ro' => ['title'=>'Transfer aprobat','sub'=>'Confirmare de procesare','greeting'=>'Bună ziua','intro'=>'Transferul dumneavoastră a fost <strong>aprobat</strong> și procesat cu succes.','lbl_ref'=>'Referință','lbl_amount'=>'Sumă','lbl_bene'=>'Beneficiar','lbl_iban'=>'IBAN','lbl_note'=>'Notă','body'=>'Suma a fost debitată din contul dumneavoastră. Pentru orice întrebare, contactați consilierul dumneavoastră.','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
        'lv' => ['title'=>'Pārvedums apstiprināts','sub'=>'Apstrādes apstiprinājums','greeting'=>'Sveiki','intro'=>'Jūsu pārvedums ir <strong>apstiprināts</strong> un veiksmīgi apstrādāts.','lbl_ref'=>'Atsauce','lbl_amount'=>'Summa','lbl_bene'=>'Saņēmējs','lbl_iban'=>'IBAN','lbl_note'=>'Piezīme','body'=>'Summa ir norakstīta no jūsu konta. Ja jums ir kādi jautājumi, sazinieties ar savu konsultantu.','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
        'nl' => ['title'=>'Overschrijving goedgekeurd','sub'=>'Bevestiging van verwerking','greeting'=>'Hallo','intro'=>'Uw overschrijving is <strong>goedgekeurd</strong> en met succes verwerkt.','lbl_ref'=>'Referentie','lbl_amount'=>'Bedrag','lbl_bene'=>'Begunstigde','lbl_iban'=>'IBAN','lbl_note'=>'Opmerking','body'=>'Het bedrag is van uw rekening afgeschreven. Neem voor vragen contact op met uw adviseur.','closing'=>'Met vriendelijke groet,','team'=>'Het team van ' . site_name()],
        'pt' => ['title'=>'Transferência aprovada','sub'=>'Confirmação de processamento','greeting'=>'Olá','intro'=>'A sua transferência foi <strong>aprovada</strong> e processada com sucesso.','lbl_ref'=>'Referência','lbl_amount'=>'Montante','lbl_bene'=>'Beneficiário','lbl_iban'=>'IBAN','lbl_note'=>'Nota','body'=>'O montante foi debitado da sua conta. Para qualquer questão, contacte o seu consultor.','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
        'hr' => ['title'=>'Prijenos odobren','sub'=>'Potvrda obrade','greeting'=>'Pozdrav','intro'=>'Vaš prijenos je <strong>odobren</strong> i uspješno obrađen.','lbl_ref'=>'Referenca','lbl_amount'=>'Iznos','lbl_bene'=>'Primatelj','lbl_iban'=>'IBAN','lbl_note'=>'Napomena','body'=>'Iznos je zadužen s vašeg računa. Za sva pitanja kontaktirajte svog savjetnika.','closing'=>'S poštovanjem,','team'=>'Tim ' . site_name()],
    ],
    'rejected' => [
        'fr' => ['title'=>'Virement rejeté','sub'=>'Information sur votre virement','greeting'=>'Bonjour','intro'=>'Nous avons le regret de vous informer que votre virement a été <strong>rejeté</strong>.','lbl_ref'=>'Référence','lbl_amount'=>'Montant','lbl_bene'=>'Bénéficiaire','lbl_iban'=>'IBAN','lbl_note'=>'Motif','body'=>'Le montant de <strong>'.$amount.'</strong> a été <strong>recrédité</strong> sur votre compte. Pour soumettre à nouveau ou obtenir plus d\'informations, contactez votre conseiller.','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
        'en' => ['title'=>'Transfer rejected','sub'=>'Information about your transfer','greeting'=>'Hello','intro'=>'We regret to inform you that your transfer has been <strong>rejected</strong>.','lbl_ref'=>'Reference','lbl_amount'=>'Amount','lbl_bene'=>'Beneficiary','lbl_iban'=>'IBAN','lbl_note'=>'Reason','body'=>'The amount of <strong>'.$amount.'</strong> has been <strong>credited back</strong> to your account. To resubmit or for more information, contact your advisor.','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
        'es' => ['title'=>'Transferencia rechazada','sub'=>'Información sobre su transferencia','greeting'=>'Hola','intro'=>'Lamentamos informarle que su transferencia ha sido <strong>rechazada</strong>.','lbl_ref'=>'Referencia','lbl_amount'=>'Importe','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Motivo','body'=>'El importe de <strong>'.$amount.'</strong> ha sido <strong>devuelto</strong> a su cuenta. Para reenviar o más información, contacte a su asesor.','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
        'pl' => ['title'=>'Przelew odrzucony','sub'=>'Informacja o Twoim przelewie','greeting'=>'Witaj','intro'=>'Z przykrością informujemy, że Twój przelew został <strong>odrzucony</strong>.','lbl_ref'=>'Referencja','lbl_amount'=>'Kwota','lbl_bene'=>'Beneficjent','lbl_iban'=>'IBAN','lbl_note'=>'Powód','body'=>'Kwota <strong>'.$amount.'</strong> została <strong>zwrócona</strong> na Twoje konto. Aby ponownie przesłać lub uzyskać więcej informacji, skontaktuj się z doradcą.','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
        'bg' => ['title'=>'Преводът е отхвърлен','sub'=>'Информация относно вашия превод','greeting'=>'Здравейте','intro'=>'Съжаляваме да ви уведомим, че вашият превод беше <strong>отхвърлен</strong>.','lbl_ref'=>'Референция','lbl_amount'=>'Сума','lbl_bene'=>'Получател','lbl_iban'=>'IBAN','lbl_note'=>'Причина','body'=>'Сумата от <strong>'.$amount.'</strong> беше <strong>възстановена</strong> по вашата сметка. За повторно подаване или повече информация, свържете се с вашия консултант.','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
        'hu' => ['title'=>'Átutalás elutasítva','sub'=>'Tájékoztatás átutalásáról','greeting'=>'Üdvözöljük','intro'=>'Sajnálattal tájékoztatjuk, hogy átutalása <strong>elutasításra</strong> került.','lbl_ref'=>'Referenciaszám','lbl_amount'=>'Összeg','lbl_bene'=>'Kedvezményezett','lbl_iban'=>'IBAN','lbl_note'=>'Indok','body'=>'A <strong>'.$amount.'</strong> összeget <strong>jóváírtuk</strong> számláján. Az újbóli beküldéshez vagy további információért forduljon tanácsadójához.','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
        'it' => ['title'=>'Bonifico rifiutato','sub'=>'Informazioni sul tuo bonifico','greeting'=>'Ciao','intro'=>'Siamo spiacenti di informarti che il tuo bonifico è stato <strong>rifiutato</strong>.','lbl_ref'=>'Riferimento','lbl_amount'=>'Importo','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Motivo','body'=>'L\'importo di <strong>'.$amount.'</strong> è stato <strong>riaccreditato</strong> sul tuo conto. Per inviare nuovamente o per maggiori informazioni, contatta il tuo consulente.','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
        'de' => ['title'=>'Überweisung abgelehnt','sub'=>'Information zu Ihrer Überweisung','greeting'=>'Guten Tag','intro'=>'Wir müssen Ihnen leider mitteilen, dass Ihre Überweisung <strong>abgelehnt</strong> wurde.','lbl_ref'=>'Referenz','lbl_amount'=>'Betrag','lbl_bene'=>'Begünstigter','lbl_iban'=>'IBAN','lbl_note'=>'Grund','body'=>'Der Betrag von <strong>'.$amount.'</strong> wurde Ihrem Konto <strong>wieder gutgeschrieben</strong>. Für eine erneute Einreichung oder weitere Informationen wenden Sie sich bitte an Ihren Berater.','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
        'lt' => ['title'=>'Pavedimas atmestas','sub'=>'Informacija apie jūsų pavedimą','greeting'=>'Sveiki','intro'=>'Su apgailestavimu pranešame, kad jūsų pavedimas buvo <strong>atmestas</strong>.','lbl_ref'=>'Numeris','lbl_amount'=>'Suma','lbl_bene'=>'Gavėjas','lbl_iban'=>'IBAN','lbl_note'=>'Priežastis','body'=>'Suma <strong>'.$amount.'</strong> buvo <strong>grąžinta</strong> į jūsų sąskaitą. Norėdami pateikti pakartotinai arba gauti daugiau informacijos, susisiekite su savo konsultantu.','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
        'ro' => ['title'=>'Transfer respins','sub'=>'Informații despre transferul dumneavoastră','greeting'=>'Bună ziua','intro'=>'Regretăm să vă informăm că transferul dumneavoastră a fost <strong>respins</strong>.','lbl_ref'=>'Referință','lbl_amount'=>'Sumă','lbl_bene'=>'Beneficiar','lbl_iban'=>'IBAN','lbl_note'=>'Motiv','body'=>'Suma de <strong>'.$amount.'</strong> a fost <strong>recreditată</strong> în contul dumneavoastră. Pentru a retrimite sau pentru mai multe informații, contactați consilierul dumneavoastră.','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
        'lv' => ['title'=>'Pārvedums noraidīts','sub'=>'Informācija par jūsu pārvedumu','greeting'=>'Sveiki','intro'=>'Mums ir žēl jums paziņot, ka jūsu pārvedums ir <strong>noraidīts</strong>.','lbl_ref'=>'Atsauce','lbl_amount'=>'Summa','lbl_bene'=>'Saņēmējs','lbl_iban'=>'IBAN','lbl_note'=>'Iemesls','body'=>'Summa <strong>'.$amount.'</strong> ir <strong>atmaksāta atpakaļ</strong> jūsu kontā. Lai iesniegtu atkārtoti vai iegūtu vairāk informācijas, sazinieties ar savu konsultantu.','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
        'nl' => ['title'=>'Overschrijving geweigerd','sub'=>'Informatie over uw overschrijving','greeting'=>'Hallo','intro'=>'Wij moeten u helaas meedelen dat uw overschrijving is <strong>geweigerd</strong>.','lbl_ref'=>'Referentie','lbl_amount'=>'Bedrag','lbl_bene'=>'Begunstigde','lbl_iban'=>'IBAN','lbl_note'=>'Reden','body'=>'Het bedrag van <strong>'.$amount.'</strong> is <strong>teruggestort</strong> op uw rekening. Neem voor een nieuwe aanvraag of meer informatie contact op met uw adviseur.','closing'=>'Met vriendelijke groet,','team'=>'Het team van ' . site_name()],
        'pt' => ['title'=>'Transferência rejeitada','sub'=>'Informação sobre a sua transferência','greeting'=>'Olá','intro'=>'Lamentamos informar que a sua transferência foi <strong>rejeitada</strong>.','lbl_ref'=>'Referência','lbl_amount'=>'Montante','lbl_bene'=>'Beneficiário','lbl_iban'=>'IBAN','lbl_note'=>'Motivo','body'=>'O montante de <strong>'.$amount.'</strong> foi <strong>creditado de volta</strong> na sua conta. Para reenviar ou obter mais informações, contacte o seu consultor.','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
        'hr' => ['title'=>'Prijenos odbijen','sub'=>'Informacije o vašem prijenosu','greeting'=>'Pozdrav','intro'=>'Sa žaljenjem vas obavještavamo da je vaš prijenos <strong>odbijen</strong>.','lbl_ref'=>'Referenca','lbl_amount'=>'Iznos','lbl_bene'=>'Primatelj','lbl_iban'=>'IBAN','lbl_note'=>'Razlog','body'=>'Iznos od <strong>'.$amount.'</strong> je <strong>vraćen</strong> na vaš račun. Za ponovno slanje ili više informacija kontaktirajte svog savjetnika.','closing'=>'S poštovanjem,','team'=>'Tim ' . site_name()],
    ],
    'fee_required' => [
        'fr' => ['title'=>'Frais requis','sub'=>'Action requise pour votre virement','greeting'=>'Bonjour','intro'=>'Votre virement est en attente de règlement de <strong>frais de traitement</strong>.','lbl_ref'=>'Référence virement','lbl_amount'=>'Montant virement','lbl_bene'=>'Bénéficiaire','lbl_iban'=>'IBAN','lbl_note'=>'Frais à régler','body'=>'Connectez-vous à votre espace client pour consulter la facture. Une fois les frais réglés, votre virement sera traité.','closing'=>'Cordialement,','team'=>"L'équipe " . site_name()],
        'en' => ['title'=>'Fees required','sub'=>'Action required for your transfer','greeting'=>'Hello','intro'=>'Your transfer is pending settlement of <strong>processing fees</strong>.','lbl_ref'=>'Transfer reference','lbl_amount'=>'Transfer amount','lbl_bene'=>'Beneficiary','lbl_iban'=>'IBAN','lbl_note'=>'Fees to settle','body'=>'Log in to your client space to view the invoice. Once the fees are paid, your transfer will be processed.','closing'=>'Best regards,','team'=>'The ' . site_name() . ' team'],
        'es' => ['title'=>'Comisiones requeridas','sub'=>'Acción requerida para su transferencia','greeting'=>'Hola','intro'=>'Su transferencia está pendiente del pago de <strong>comisiones de procesamiento</strong>.','lbl_ref'=>'Referencia transferencia','lbl_amount'=>'Importe transferencia','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Comisiones a pagar','body'=>'Inicie sesión en su espacio cliente para consultar la factura. Una vez pagadas las comisiones, su transferencia será procesada.','closing'=>'Atentamente,','team'=>'El equipo ' . site_name()],
        'pl' => ['title'=>'Wymagane opłaty','sub'=>'Wymagane działanie dla Twojego przelewu','greeting'=>'Witaj','intro'=>'Twój przelew oczekuje na uregulowanie <strong>opłat za przetwarzanie</strong>.','lbl_ref'=>'Referencja przelewu','lbl_amount'=>'Kwota przelewu','lbl_bene'=>'Beneficjent','lbl_iban'=>'IBAN','lbl_note'=>'Opłaty do uregulowania','body'=>'Zaloguj się do swojego obszaru klienta, aby zobaczyć fakturę. Po opłaceniu zostanie przetworzony.','closing'=>'Z poważaniem,','team'=>'Zespół ' . site_name()],
        'bg' => ['title'=>'Изисква се такса','sub'=>'Необходимо действие за вашия превод','greeting'=>'Здравейте','intro'=>'Вашият превод очаква заплащане на <strong>такса за обработка</strong>.','lbl_ref'=>'Референция на превода','lbl_amount'=>'Сума на превода','lbl_bene'=>'Получател','lbl_iban'=>'IBAN','lbl_note'=>'Такса за заплащане','body'=>'Влезте в клиентското си пространство, за да видите фактурата. След заплащане на таксата, преводът ви ще бъде обработен.','closing'=>'С уважение,','team'=>'Екипът на ' . site_name()],
        'hu' => ['title'=>'Díj szükséges','sub'=>'Teendő az átutalásával kapcsolatban','greeting'=>'Üdvözöljük','intro'=>'Átutalása <strong>feldolgozási díj</strong> rendezésére vár.','lbl_ref'=>'Átutalás referenciaszáma','lbl_amount'=>'Átutalás összege','lbl_bene'=>'Kedvezményezett','lbl_iban'=>'IBAN','lbl_note'=>'Rendezendő díj','body'=>'Jelentkezzen be ügyfélfiókjába a számla megtekintéséhez. A díj rendezése után átutalása feldolgozásra kerül.','closing'=>'Tisztelettel,','team'=>'A ' . site_name() . ' csapata'],
        'it' => ['title'=>'Commissioni richieste','sub'=>'Azione richiesta per il tuo bonifico','greeting'=>'Ciao','intro'=>'Il tuo bonifico è in attesa del pagamento delle <strong>commissioni di elaborazione</strong>.','lbl_ref'=>'Riferimento bonifico','lbl_amount'=>'Importo bonifico','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Commissioni da pagare','body'=>'Accedi alla tua area cliente per consultare la fattura. Una volta pagate le commissioni, il tuo bonifico sarà elaborato.','closing'=>'Cordiali saluti,','team'=>'Il team ' . site_name()],
        'de' => ['title'=>'Gebühren erforderlich','sub'=>'Maßnahme für Ihre Überweisung erforderlich','greeting'=>'Guten Tag','intro'=>'Ihre Überweisung wartet auf die Begleichung von <strong>Bearbeitungsgebühren</strong>.','lbl_ref'=>'Überweisungsreferenz','lbl_amount'=>'Überweisungsbetrag','lbl_bene'=>'Begünstigter','lbl_iban'=>'IBAN','lbl_note'=>'Zu begleichende Gebühren','body'=>'Melden Sie sich in Ihrem Kundenbereich an, um die Rechnung einzusehen. Sobald die Gebühren beglichen sind, wird Ihre Überweisung bearbeitet.','closing'=>'Mit freundlichen Grüßen,','team'=>'Das ' . site_name() . '-Team'],
        'lt' => ['title'=>'Reikalingas mokestis','sub'=>'Reikalingas veiksmas dėl jūsų pavedimo','greeting'=>'Sveiki','intro'=>'Jūsų pavedimas laukia <strong>apdorojimo mokesčio</strong> apmokėjimo.','lbl_ref'=>'Pavedimo numeris','lbl_amount'=>'Pavedimo suma','lbl_bene'=>'Gavėjas','lbl_iban'=>'IBAN','lbl_note'=>'Mokamas mokestis','body'=>'Prisijunkite prie savo kliento srities, kad peržiūrėtumėte sąskaitą faktūrą. Sumokėjus mokestį, jūsų pavedimas bus apdorotas.','closing'=>'Pagarbiai,','team'=>site_name() . ' komanda'],
        'ro' => ['title'=>'Comision necesar','sub'=>'Acțiune necesară pentru transferul dumneavoastră','greeting'=>'Bună ziua','intro'=>'Transferul dumneavoastră este în așteptarea achitării <strong>comisioanelor de procesare</strong>.','lbl_ref'=>'Referință transfer','lbl_amount'=>'Sumă transfer','lbl_bene'=>'Beneficiar','lbl_iban'=>'IBAN','lbl_note'=>'Comision de achitat','body'=>'Conectați-vă la spațiul dumneavoastră de client pentru a consulta factura. Odată achitat comisionul, transferul dumneavoastră va fi procesat.','closing'=>'Cu stimă,','team'=>'Echipa ' . site_name()],
        'lv' => ['title'=>'Nepieciešama maksa','sub'=>'Nepieciešama rīcība attiecībā uz jūsu pārvedumu','greeting'=>'Sveiki','intro'=>'Jūsu pārvedums gaida <strong>apstrādes maksas</strong> nokārtošanu.','lbl_ref'=>'Pārveduma atsauce','lbl_amount'=>'Pārveduma summa','lbl_bene'=>'Saņēmējs','lbl_iban'=>'IBAN','lbl_note'=>'Apmaksājamā maksa','body'=>'Pieslēdzieties savai klienta zonai, lai apskatītu rēķinu. Kad maksa būs nokārtota, jūsu pārvedums tiks apstrādāts.','closing'=>'Ar cieņu,','team'=>site_name() . ' komanda'],
        'nl' => ['title'=>'Kosten vereist','sub'=>'Actie vereist voor uw overschrijving','greeting'=>'Hallo','intro'=>'Uw overschrijving is in afwachting van betaling van <strong>verwerkingskosten</strong>.','lbl_ref'=>'Referentie overschrijving','lbl_amount'=>'Bedrag overschrijving','lbl_bene'=>'Begunstigde','lbl_iban'=>'IBAN','lbl_note'=>'Te betalen kosten','body'=>'Log in op uw klantomgeving om de factuur te bekijken. Zodra de kosten zijn betaald, wordt uw overschrijving verwerkt.','closing'=>'Met vriendelijke groet,','team'=>'Het team van ' . site_name()],
        'pt' => ['title'=>'Comissão necessária','sub'=>'Ação necessária para a sua transferência','greeting'=>'Olá','intro'=>'A sua transferência está pendente do pagamento de <strong>comissões de processamento</strong>.','lbl_ref'=>'Referência da transferência','lbl_amount'=>'Montante da transferência','lbl_bene'=>'Beneficiário','lbl_iban'=>'IBAN','lbl_note'=>'Comissão a pagar','body'=>'Aceda à sua área de cliente para consultar a fatura. Assim que a comissão for paga, a sua transferência será processada.','closing'=>'Atenciosamente,','team'=>'A equipa ' . site_name()],
        'hr' => ['title'=>'Potrebni troškovi','sub'=>'Potrebna radnja za vaš prijenos','greeting'=>'Pozdrav','intro'=>'Vaš prijenos čeka podmirenje <strong>troškova obrade</strong>.','lbl_ref'=>'Referenca prijenosa','lbl_amount'=>'Iznos prijenosa','lbl_bene'=>'Primatelj','lbl_iban'=>'IBAN','lbl_note'=>'Trošak za podmirenje','body'=>'Prijavite se u svoj klijentski prostor za pregled računa. Nakon podmirenja troškova, vaš prijenos će biti obrađen.','closing'=>'S poštovanjem,','team'=>'Tim ' . site_name()],
    ],
];

$t      = $texts[$action][$locale] ?? $texts[$action]['fr'];
$accent = match($action) { 'approved' => 'green', 'rejected' => 'red', default => 'orange' };
@endphp

<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    :accent="$accent"
    :locale="$locale"
>

  <p class="greeting">
    {{ $t['greeting'] }} {{ $transfer->user->name }},
  </p>

  <p class="body-text">{!! $t['intro'] !!}</p>

  <div class="panel">
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_ref'] }}</span>
      <span class="panel-val">{{ $ref }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ __('transfer.type', [], $locale) }}</span>
      <span class="panel-val">{{ $transfer->typeLabel($locale) }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_amount'] }}</span>
      <span class="panel-val accent">{{ $amount }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_bene'] }}</span>
      <span class="panel-val">{{ $bene }}</span>
    </div>
    @if($transfer->beneficiary_iban)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_iban'] }}</span>
      <span class="panel-val" style="font-family:monospace;font-size:.82rem">{{ $transfer->beneficiary_iban }}</span>
    </div>
    @endif
    @if($action === 'fee_required' && $transfer->invoice)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_note'] }}</span>
      <span class="panel-val accent">{{ number_format($transfer->invoice->total, 2, ',', ' ') }} {{ $transfer->invoice->currency }}</span>
    </div>
    @if($transfer->invoice->reference)
    <div class="panel-row">
      <span class="panel-lbl">Réf. facture</span>
      <span class="panel-val">{{ $transfer->invoice->reference }}</span>
    </div>
    @endif
    @elseif($transfer->admin_note)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_note'] }}</span>
      <span class="panel-val">{{ $transfer->admin_note }}</span>
    </div>
    @endif
  </div>

  <p class="body-text">{!! $t['body'] !!}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
