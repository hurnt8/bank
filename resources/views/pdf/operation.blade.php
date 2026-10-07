@php
    $contact      = \App\Models\SiteContact::current();
    $logo         = \App\Models\Invoice::logoDataUri();
    $cur          = $doc['currency'];
    $fmt          = fn ($n) => number_format((float) $n, 2, ',', ' ') . ' ' . $cur;
    $client       = $doc['client'];
    $contactLines = array_filter([$contact->address_1, $contact->address_2, $contact->address_3, $contact->phone_1, $contact->email]);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ $doc['number'] }}</title>
<style>
  @page { margin: 40px 48px 56px; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #111; line-height: 1.55; margin: 0; }
  table { border-collapse: collapse; width: 100%; }
  td { vertical-align: top; }
  .muted { color: #666; }
  .lbl { font-size: 8.5px; letter-spacing: 1px; text-transform: uppercase; color: #777; margin-bottom: 4px; }

  .top td { padding-bottom: 14px; border-bottom: 1px solid #111; }
  .title { font-size: 20px; font-weight: bold; text-align: right; letter-spacing: .5px; }
  .meta { text-align: right; color: #555; font-size: 10px; }

  .amount-box { margin-top: 26px; }
  .badge { display: inline-block; font-size: 8.5px; letter-spacing: 1px; text-transform: uppercase; border: 1px solid #111; padding: 2px 9px; }
  .amount { font-size: 28px; font-weight: bold; margin-top: 8px; }
  .kind { color: #555; margin-top: 2px; }

  .cols { margin-top: 26px; }
  .cols td { width: 50%; }
  .name { font-weight: bold; }

  .sect { margin-top: 28px; }
  .rows td { padding: 7px 0; border-bottom: 1px solid #ddd; }
  .rows td.k { width: 38%; color: #666; }
  .rows td.v { text-align: right; }
  .rows tr:first-child td { border-top: 1px solid #111; }

  .foot { position: fixed; bottom: -34px; left: 0; right: 0; border-top: 1px solid #ccc; padding-top: 6px; font-size: 8.5px; color: #777; }
  .foot .r { text-align: right; }
</style>
</head>
<body>

  <table class="top"><tr>
    <td style="width:50%">
      @if($logo)<img src="{{ $logo }}" style="max-height:42px;max-width:190px" alt="{{ site_name() }}">@else<strong style="font-size:15px">{{ site_name() }}</strong>@endif
    </td>
    <td>
      <div class="title">{{ mb_strtoupper(__('invoice.title')) }}</div>
      <div class="meta">{{ __('invoice.number') }} {{ $doc['number'] }}<br>{{ $doc['date']->format('d/m/Y H:i') }}</div>
    </td>
  </tr></table>

  <div class="amount-box">
    <span class="badge">{{ $doc['badge'] }}</span>
    <div class="amount">{{ $doc['sign'] }}{{ $fmt($doc['total']) }}</div>
    <div class="kind">{{ $doc['kind'] }}@if(! empty($doc['lines'][0]['description']) && str_contains($doc['lines'][0]['description'], ' — ')) — {{ trim(explode(' — ', $doc['lines'][0]['description'], 2)[1]) }}@endif</div>
  </div>

  <table class="cols"><tr>
    <td>
      <div class="lbl">{{ site_name() }}</div>
      @foreach($contactLines as $line)<span class="muted">{{ $line }}</span><br>@endforeach
    </td>
    <td>
      <div class="lbl">{{ __('invoice.bill_to') }}</div>
      <span class="name">{{ $client->name }}</span><br>
      @if($client->address)<span class="muted">{{ $client->address }}</span><br>@endif
      <span class="muted">{{ $client->email }}</span>@if($client->phone)<br><span class="muted">{{ $client->phone }}</span>@endif
    </td>
  </tr></table>

  <div class="sect">
    <div class="lbl">{{ __('transfer.details') }}</div>
    <table class="rows">
      @foreach(array_merge($doc['rows'], $doc['account'] ?? []) as [$label, $value])
      <tr><td class="k">{{ $label }}</td><td class="v">{{ $value }}</td></tr>
      @endforeach
    </table>
  </div>

  <div class="foot">
    <table><tr>
      <td>{{ site_name() }}@if($contact->email) · {{ $contact->email }}@endif</td>
      <td class="r">{{ $doc['number'] }}</td>
    </tr></table>
  </div>

</body>
</html>
