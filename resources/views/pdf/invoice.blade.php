@php
    $client   = $invoice->client;
    $contact  = \App\Models\SiteContact::current();
    $iban     = $invoice->paymentIban();
    $bic      = $invoice->paymentBic();
    $fmt      = fn ($n) => number_format((float) $n, 2, ',', ' ') . ' ' . $invoice->currency;
    $fromLines = array_filter([$contact->address_1, $contact->address_2, $contact->address_3, $contact->email]);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ $invoice->reference }}</title>
<style>
  @page { margin: 38px 44px 54px; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #111; line-height: 1.5; }
  h1 { font-size: 24px; margin: 0 0 14px; }
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
  .pay { margin-top: 28px; border: 1.5px solid #111; padding: 12px 14px; }
  .pay-title { font-size: 12px; font-weight: bold; margin-bottom: 8px; }
  .pay td { padding: 2px 0; vertical-align: top; }
  .pay td:first-child { width: 140px; color: #444; }
  .pay .iban { font-size: 12px; font-weight: bold; letter-spacing: .5px; }
  .hint { margin-top: 8px; font-size: 9.5px; color: #555; }
  .note { margin-top: 16px; font-size: 9.5px; color: #444; }
  .foot { position: fixed; bottom: -30px; left: 0; right: 0; border-top: 1px solid #ccc; padding-top: 6px; font-size: 9px; color: #444; text-align: right; }
</style>
</head>
<body>
  @php $logo = \App\Models\Invoice::logoDataUri(); @endphp
  <table style="margin-bottom:6px"><tr>
    <td style="vertical-align:top"><h1 style="margin:0">{{ __('invoice.title') }}</h1></td>
    <td style="text-align:right;vertical-align:top">
      @if($logo)<img src="{{ $logo }}" style="max-height:52px;max-width:200px" alt="{{ site_name() }}">@else<strong style="font-size:16px">{{ site_name() }}</strong>@endif
    </td>
  </tr></table>
  <div style="height:8px"></div>

  <table class="meta">
    <tr><td>{{ __('invoice.number') }}</td><td>{{ $invoice->reference }}</td></tr>
    <tr><td>{{ __('invoice.issued') }}</td><td>{{ $invoice->issue_date?->format('d/m/Y') }}</td></tr>
    @if($invoice->due_date)<tr><td>{{ __('invoice.due') }}</td><td>{{ $invoice->due_date->format('d/m/Y') }}</td></tr>@endif
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

  <div class="due">{{ $fmt($invoice->total) }}@if($invoice->due_date) — {{ __('invoice.due') }} {{ $invoice->due_date->format('d/m/Y') }}@endif</div>

  <table class="items">
    <thead><tr>
      <th>{{ __('invoice.description') }}</th><th class="r" style="width:42px">{{ __('invoice.qty') }}</th>
      <th class="r" style="width:85px">{{ __('invoice.unit_price') }}</th><th class="r" style="width:90px">{{ __('invoice.amount') }}</th>
    </tr></thead>
    <tbody>
    @foreach(($invoice->items ?? []) as $item)
      <tr>
        <td>{{ $item['description'] }}</td>
        <td class="r">{{ rtrim(rtrim(number_format((float) $item['quantity'], 2, ',', ''), '0'), ',') }}</td>
        <td class="r">{{ $fmt($item['unit_price']) }}</td>
        <td class="r">{{ $fmt($item['total']) }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <table class="tot">
    <tr><td>{{ __('invoice.subtotal') }}</td><td class="r">{{ $fmt($invoice->subtotal) }}</td></tr>
    @if($invoice->tax_rate > 0)<tr><td>{{ __('invoice.tax') }} ({{ rtrim(rtrim(number_format($invoice->tax_rate, 2, ',', ''), '0'), ',') }} %)</td><td class="r">{{ $fmt($invoice->tax_amount) }}</td></tr>@endif
    <tr><td>{{ __('invoice.total') }}</td><td class="r">{{ $fmt($invoice->total) }}</td></tr>
    <tr class="strong"><td>{{ __('invoice.amount_due') }}</td><td class="r">{{ $fmt($invoice->total) }}</td></tr>
  </table>

  @if($invoice->linkedTransfer)
  @php $lt = $invoice->linkedTransfer; @endphp
  <div class="pay" style="margin-top:20px">
    <div class="pay-title">{{ __('transfer.detail_title') }}</div>
    <table>
      <tr><td>{{ __('transfer.reference') }}</td><td><strong>{{ $lt->reference }}</strong></td></tr>
      <tr><td>{{ __('transfer.type') }}</td><td>{{ $lt->typeLabel() }}</td></tr>
      <tr><td>{{ __('transfer.beneficiary') }}</td><td>{{ $lt->beneficiary_name }}</td></tr>
      <tr><td>{{ __('invoice.amount') }}</td><td>{{ number_format((float) $lt->amount, 2, ',', ' ') }} {{ $lt->currency }}</td></tr>
    </table>
  </div>
  @endif

  @if($iban !== '')
  <div class="pay">
    <div class="pay-title">{{ __('invoice.pay_title') }}</div>
    <table>
      <tr><td>{{ __('invoice.pay_holder') }}</td><td>{{ $invoice->paymentHolder() }}</td></tr>
      <tr><td>{{ __('invoice.pay_iban') }}</td><td class="iban">{{ \App\Models\Invoice::formatIban($iban) }}</td></tr>
      @if($bic !== '')<tr><td>{{ __('invoice.pay_bic') }}</td><td class="iban">{{ $bic }}</td></tr>@endif
      <tr><td>{{ __('invoice.pay_reference') }}</td><td><strong>{{ $invoice->reference }}</strong></td></tr>
    </table>
    <div class="hint">{{ __('invoice.pay_hint') }}</div>
  </div>
  @endif

  @if($invoice->note)<div class="note">{{ $invoice->note }}</div>@endif

  <div class="foot">{{ site_name() }} · {{ $invoice->reference }}</div>
</body>
</html>
