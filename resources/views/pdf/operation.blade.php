@php
    $contact   = \App\Models\SiteContact::current();
    $logo      = \App\Models\Invoice::logoDataUri();
    $cur       = $doc['currency'];
    $fmt       = fn ($n) => number_format((float) $n, 2, ',', ' ') . ' ' . $cur;
    $fromLines = array_filter([$contact->address_1, $contact->address_2, $contact->address_3, $contact->email]);
    $client    = $doc['client'];
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ $doc['number'] }}</title>
<style>
  @page { margin: 38px 44px 54px; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #111; line-height: 1.5; }
  h1 { font-size: 24px; margin: 0; }
  table { border-collapse: collapse; width: 100%; }
  .meta td { padding: 1px 0; vertical-align: top; }
  .meta td:first-child { font-weight: bold; width: 125px; }
  .cols td { vertical-align: top; width: 50%; padding-top: 22px; }
  .lbl { font-weight: bold; margin-bottom: 4px; }
  .due { font-size: 15px; font-weight: bold; margin: 26px 0 16px; }
  .items th { text-align: left; font-weight: normal; font-size: 9.5px; color: #444; border-bottom: 1.5px solid #111; padding: 0 4px 5px 0; }
  .items th.r, .items td.r { text-align: right; }
  .items td { padding: 8px 4px 8px 0; vertical-align: top; }
  .tot { width: 52%; margin-left: 48%; margin-top: 16px; }
  .tot td { padding: 3px 0; border-bottom: 1px solid #ddd; }
  .tot td.r { text-align: right; }
  .tot tr.strong td { font-weight: bold; border-bottom: 0; }
  .box { margin-top: 26px; border: 1.5px solid #111; padding: 12px 14px; }
  .box td { padding: 3px 0; vertical-align: top; }
  .box td:first-child { width: 150px; color: #444; }
  .foot { position: fixed; bottom: -30px; left: 0; right: 0; border-top: 1px solid #ccc; padding-top: 6px; font-size: 9px; color: #444; text-align: right; }
</style>
</head>
<body>
  <table style="margin-bottom:6px"><tr>
    <td style="vertical-align:top"><h1>{{ __('invoice.title') }}</h1></td>
    <td style="text-align:right;vertical-align:top">
      @if($logo)<img src="{{ $logo }}" style="max-height:52px;max-width:200px" alt="{{ site_name() }}">@else<strong style="font-size:16px">{{ site_name() }}</strong>@endif
    </td>
  </tr></table>

  <table class="meta">
    <tr><td>{{ __('invoice.number') }}</td><td>{{ $doc['number'] }}</td></tr>
    <tr><td>{{ __('invoice.issued') }}</td><td>{{ $doc['date']->format('d/m/Y H:i') }}</td></tr>
  </table>

  <table class="cols"><tr>
    <td>
      <div class="lbl">{{ site_name() }}</div>
      @foreach($fromLines as $line){{ $line }}<br>@endforeach
    </td>
    <td>
      <div class="lbl">{{ __('invoice.bill_to') }}</div>
      {{ $client->name }}<br>
      @if($client->address){{ $client->address }}<br>@endif
      {{ $client->email }}
    </td>
  </tr></table>

  <div class="due">{{ $doc['sign'] }}{{ $fmt($doc['total']) }}</div>

  <table class="items">
    <thead><tr>
      <th>{{ __('invoice.description') }}</th><th class="r" style="width:42px">{{ __('invoice.qty') }}</th>
      <th class="r" style="width:85px">{{ __('invoice.unit_price') }}</th><th class="r" style="width:90px">{{ __('invoice.amount') }}</th>
    </tr></thead>
    <tbody>
    @foreach($doc['lines'] as $l)
      <tr>
        <td>{{ $l['description'] }}</td>
        <td class="r">{{ $l['quantity'] }}</td>
        <td class="r">{{ $fmt($l['unit']) }}</td>
        <td class="r">{{ $fmt($l['total']) }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <table class="tot">
    <tr><td>{{ __('invoice.subtotal') }}</td><td class="r">{{ $fmt($doc['total']) }}</td></tr>
    <tr class="strong"><td>{{ __('invoice.total') }}</td><td class="r">{{ $doc['sign'] }}{{ $fmt($doc['total']) }}</td></tr>
  </table>

  <div class="box">
    <table>
      @foreach($doc['rows'] as [$label, $value])
      <tr><td>{{ $label }}</td><td>{{ $value }}</td></tr>
      @endforeach
    </table>
  </div>

  <div class="foot">{{ site_name() }} · {{ $doc['number'] }}</div>
</body>
</html>
