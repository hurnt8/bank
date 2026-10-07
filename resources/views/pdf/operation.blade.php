@php
    $contact   = \App\Models\SiteContact::current();
    $logo      = \App\Models\Invoice::logoDataUri();
    $cur       = $doc['currency'];
    $fmt       = fn ($n) => number_format((float) $n, 2, ',', ' ') . ' ' . $cur;
    $client    = $doc['client'];
    $tone      = $doc['tone'];
    // Couleurs : vert (crédit / validé), rouge (débit / rejeté), ambre (en attente)
    $c         = ['ok' => ['#15803d', '#dcfce7'], 'bad' => ['#b91c1c', '#fee2e2'], 'wait' => ['#b45309', '#fef3c7']][$tone];
    $contactLines = array_filter([$contact->address_1, $contact->address_2, $contact->address_3, $contact->phone_1, $contact->email]);
    $amountColor  = $tone === 'wait' ? '#1B4976' : $c[0];
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ $doc['number'] }}</title>
<style>
  @page { margin: 0; }
  * { box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #1f2937; line-height: 1.5; margin: 0; }
  table { border-collapse: collapse; width: 100%; }
  td { vertical-align: top; }

  .band { background: #0D2E52; color: #fff; padding: 26px 44px 24px; }
  .band .logo-box { background: #fff; border-radius: 8px; padding: 7px 12px; display: inline-block; }
  .band .brand { font-size: 17px; font-weight: bold; letter-spacing: .3px; }
  .band .doc-title { font-size: 21px; font-weight: bold; text-align: right; letter-spacing: .5px; }
  .band .doc-meta { text-align: right; font-size: 10px; color: #cbd5e1; margin-top: 4px; line-height: 1.6; }
  .gold { height: 4px; background: #C6A15B; }

  .page { padding: 26px 44px 60px; }

  .hero { border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 18px 22px; background: #f8fafc; }
  .pill { display: inline-block; font-size: 9px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 4px 11px; border-radius: 20px; }
  .hero .amount { font-size: 30px; font-weight: bold; margin-top: 8px; }
  .hero .kind { font-size: 12px; color: #475569; margin-top: 2px; }
  .hero .right { text-align: right; color: #64748b; font-size: 10px; line-height: 1.8; }
  .hero .right strong { color: #1f2937; }

  .cols { margin-top: 22px; }
  .cols td { width: 50%; padding-right: 18px; }
  .cols td + td { padding-right: 0; padding-left: 18px; }
  .lbl { font-size: 8.5px; font-weight: bold; letter-spacing: 1.2px; text-transform: uppercase; color: #94a3b8; margin-bottom: 5px; }
  .party-name { font-size: 12px; font-weight: bold; color: #0f172a; }
  .muted { color: #64748b; }

  .sect { margin-top: 26px; }
  .sect-title { font-size: 9.5px; font-weight: bold; letter-spacing: 1.2px; text-transform: uppercase; color: #0D2E52; border-bottom: 2px solid #C6A15B; padding-bottom: 5px; margin-bottom: 4px; }
  .rows td { padding: 8px 10px; border-bottom: 1px solid #eef2f7; font-size: 10.5px; }
  .rows tr.z td { background: #f8fafc; }
  .rows td.k { width: 36%; color: #64748b; }
  .rows td.v { font-weight: bold; color: #0f172a; text-align: right; }
  .mono { letter-spacing: .6px; }

  .account { margin-top: 24px; background: #0D2E52; color: #fff; border-radius: 12px; padding: 16px 22px; }
  .account td { padding: 3px 0; }
  .account .k { color: #cbd5e1; font-size: 9.5px; text-transform: uppercase; letter-spacing: 1px; width: 38%; }
  .account .v { text-align: right; font-weight: bold; font-size: 12px; }

  .foot { position: fixed; bottom: 0; left: 0; right: 0; background: #f1f5f9; border-top: 1px solid #e2e8f0; padding: 9px 44px; font-size: 8.5px; color: #64748b; }
  .foot .r { text-align: right; }
</style>
</head>
<body>

  <div class="band">
    <table><tr>
      <td style="width:55%">
        @if($logo)<span class="logo-box"><img src="{{ $logo }}" style="max-height:40px;max-width:190px" alt="{{ site_name() }}"></span>
        @else<span class="brand">{{ site_name() }}</span>@endif
      </td>
      <td>
        <div class="doc-title">{{ mb_strtoupper(__('invoice.title')) }}</div>
        <div class="doc-meta">{{ __('invoice.number') }} <strong style="color:#fff">{{ $doc['number'] }}</strong><br>{{ $doc['date']->format('d/m/Y H:i') }}</div>
      </td>
    </tr></table>
  </div>
  <div class="gold"></div>

  <div class="page">

    {{-- Montant --}}
    <div class="hero">
      <table><tr>
        <td>
          <span class="pill" style="background:{{ $c[1] }};color:{{ $c[0] }}">{{ $doc['badge'] }}</span>
          <div class="amount" style="color:{{ $amountColor }}">{{ $doc['sign'] }}{{ $fmt($doc['total']) }}</div>
          <div class="kind">{{ $doc['kind'] }}@if(! empty($doc['lines'][0]['description']) && str_contains($doc['lines'][0]['description'], ' — ')) — {{ trim(explode(' — ', $doc['lines'][0]['description'], 2)[1]) }}@endif</div>
        </td>
      </tr></table>
    </div>

    {{-- Parties --}}
    <table class="cols"><tr>
      <td>
        <div class="lbl">{{ site_name() }}</div>
        @foreach($contactLines as $line)<span class="muted">{{ $line }}</span><br>@endforeach
      </td>
      <td>
        <div class="lbl">{{ __('invoice.bill_to') }}</div>
        <div class="party-name">{{ $client->name }}</div>
        @if($client->address)<span class="muted">{{ $client->address }}</span><br>@endif
        <span class="muted">{{ $client->email }}</span>@if($client->phone)<br><span class="muted">{{ $client->phone }}</span>@endif
      </td>
    </tr></table>

    {{-- Détails de l'opération --}}
    <div class="sect">
      <div class="sect-title">{{ __('transfer.details') }}</div>
      <table class="rows">
        @foreach($doc['rows'] as $i => [$label, $value])
        <tr class="{{ $i % 2 === 0 ? 'z' : '' }}">
          <td class="k">{{ $label }}</td>
          <td class="v {{ in_array($label, [__('movement.label_iban')], true) ? 'mono' : '' }}">{{ $value }}</td>
        </tr>
        @endforeach
      </table>
    </div>

    {{-- Compte du client --}}
    @if(! empty($doc['account']))
    <div class="account">
      <table>
        @foreach($doc['account'] as [$label, $value])
        <tr><td class="k">{{ $label }}</td><td class="v {{ $label === __('invoice.pay_iban') ? 'mono' : '' }}">{{ $value }}</td></tr>
        @endforeach
      </table>
    </div>
    @endif

  </div>

  <div class="foot">
    <table><tr>
      <td>{{ site_name() }}@if($contact->email) · {{ $contact->email }}@endif</td>
      <td class="r">{{ $doc['number'] }}</td>
    </tr></table>
  </div>

</body>
</html>
