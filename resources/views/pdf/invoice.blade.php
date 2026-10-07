@php
    $client    = $invoice->client;
    $contact   = \App\Models\SiteContact::current();
    $logo      = \App\Models\Invoice::logoDataUri();
    $iban      = $invoice->paymentIban();
    $bic       = $invoice->paymentBic();
    $lt        = $invoice->linkedTransfer;
    $fmt       = fn ($n) => number_format((float) $n, 2, ',', ' ') . ' ' . $invoice->currency;
    $fromLines = array_filter([$contact->address_1, $contact->address_2, $contact->address_3, $contact->phone_1, $contact->email]);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ $invoice->reference }}</title>
<style>
  @page { margin: 32px 48px 52px; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #111; line-height: 1.55; margin: 0; }
  table { border-collapse: collapse; width: 100%; }
  td { vertical-align: top; }
  .muted { color: #666; }
  .lbl { font-size: 8.5px; letter-spacing: 1px; text-transform: uppercase; color: #777; margin-bottom: 5px; }
  .mono { letter-spacing: .6px; }

  .top td { padding-bottom: 12px; border-bottom: 1px solid #111; }
  .title { font-size: 21px; font-weight: bold; text-align: right; letter-spacing: .5px; }
  .meta { text-align: right; color: #555; font-size: 10px; margin-top: 3px; }

  .due-box { margin-top: 18px; }
  .due-box .amount { font-size: 26px; font-weight: bold; margin-top: 4px; }

  .cols { margin-top: 18px; }
  .cols td { width: 50%; padding-right: 16px; }
  .name { font-weight: bold; }

  .sect { margin-top: 20px; }
  .rows td { padding: 5px 0; border-bottom: 1px solid #ddd; }
  .rows tr:first-child td { border-top: 1px solid #111; }
  .rows td.k { width: 38%; color: #666; }
  .rows td.v { text-align: right; }
  .rows td.v.strong { font-weight: bold; }

  .items { margin-top: 20px; }
  .items th { text-align: left; font-weight: normal; font-size: 9px; letter-spacing: 1px; text-transform: uppercase; color: #777; padding: 0 6px 7px 0; border-bottom: 1px solid #111; }
  .items th.r, .items td.r { text-align: right; }
  .items td { padding: 8px 6px 8px 0; border-bottom: 1px solid #ddd; }

  .tot { width: 46%; margin-left: 54%; margin-top: 8px; }
  .tot td { padding: 3px 0; }
  .tot td.r { text-align: right; }
  .tot tr.strong td { font-weight: bold; font-size: 12px; border-top: 1px solid #111; padding-top: 8px; }

  .hint { margin-top: 6px; font-size: 9.5px; color: #666; }
  .note { margin-top: 16px; font-size: 9.5px; color: #555; }

  .foot { position: fixed; bottom: -36px; left: 0; right: 0; border-top: 1px solid #ccc; padding-top: 6px; font-size: 8.5px; color: #777; }
  .foot .r { text-align: right; }
</style>
</head>
<body>

  <table class="top"><tr>
    <td style="width:50%">
      @if($logo)<img src="{{ $logo }}" style="max-height:44px;max-width:190px" alt="{{ site_name() }}">@else<strong style="font-size:15px">{{ site_name() }}</strong>@endif
    </td>
    <td>
      <div class="title">{{ mb_strtoupper(__('invoice.title')) }}</div>
      <div class="meta">
        {{ __('invoice.number') }} {{ $invoice->reference }}<br>
        {{ __('invoice.issued') }} {{ $invoice->issue_date?->format('d/m/Y') }}@if($invoice->due_date) · {{ __('invoice.due') }} {{ $invoice->due_date->format('d/m/Y') }}@endif
      </div>
    </td>
  </tr></table>

  {{-- Montant dû --}}
  <div class="due-box">
    <div class="lbl">{{ __('invoice.amount_due') }}</div>
    <div class="amount">{{ $fmt($invoice->total) }}</div>
  </div>

  {{-- Parties --}}
  <table class="cols"><tr>
    <td>
      <div class="lbl">{{ site_name() }}</div>
      @foreach($fromLines as $line)<span class="muted">{{ $line }}</span><br>@endforeach
    </td>
    <td>
      <div class="lbl">{{ __('invoice.bill_to') }}</div>
      <span class="name">{{ $client->name }}</span><br>
      @if($client->address)<span class="muted">{{ $client->address }}</span><br>@endif
      <span class="muted">{{ $client->email }}</span>@if($client->phone)<br><span class="muted">{{ $client->phone }}</span>@endif
    </td>
  </tr></table>

  {{-- Lignes --}}
  <table class="items">
    <thead><tr>
      <th>{{ __('invoice.description') }}</th><th class="r" style="width:44px">{{ __('invoice.qty') }}</th>
      <th class="r" style="width:92px">{{ __('invoice.unit_price') }}</th><th class="r" style="width:96px">{{ __('invoice.amount') }}</th>
    </tr></thead>
    <tbody>
    @foreach(($invoice->items ?? []) as $item)
      <tr>
        <td>{{ $invoice->itemDescription($item) }}</td>
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
    <tr class="strong"><td>{{ __('invoice.total') }}</td><td class="r">{{ $fmt($invoice->total) }}</td></tr>
  </table>

  {{-- Virement concerné --}}
  @if($lt)
  <div class="sect">
    <div class="lbl">{{ __('transfer.detail_title') }}</div>
    <table class="rows">
      <tr><td class="k">{{ __('transfer.reference') }}</td><td class="v strong">{{ $lt->reference }}</td></tr>
      <tr><td class="k">{{ __('transfer.type') }}</td><td class="v">{{ $lt->typeLabel() }}</td></tr>
      <tr><td class="k">{{ __('transfer.beneficiary') }}</td><td class="v">{{ $lt->beneficiary_name }}</td></tr>
      @if($lt->beneficiary_iban)<tr><td class="k">{{ __('invoice.pay_iban') }}</td><td class="v mono">{{ \App\Models\Invoice::formatIban((string) $lt->beneficiary_iban) }}</td></tr>@endif
      <tr><td class="k">{{ __('invoice.amount') }}</td><td class="v">{{ number_format((float) $lt->amount, 2, ',', ' ') }} {{ $lt->currency }}</td></tr>
    </table>
  </div>
  @endif

  {{-- Règlement --}}
  @if($iban !== '')
  <div class="sect">
    <div class="lbl">{{ __('invoice.pay_title') }}</div>
    <table class="rows">
      <tr><td class="k">{{ __('transfer.type') }}</td><td class="v strong">{{ $invoice->paymentTypeLabel() }}</td></tr>
      <tr><td class="k">{{ __('invoice.pay_holder') }}</td><td class="v strong">{{ $invoice->paymentHolder() }}</td></tr>
      <tr><td class="k">{{ __('invoice.pay_iban') }}</td><td class="v strong mono">{{ \App\Models\Invoice::formatIban($iban) }}</td></tr>
      @if($bic !== '')<tr><td class="k">{{ __('invoice.pay_bic') }}</td><td class="v mono">{{ $bic }}</td></tr>@endif
      <tr><td class="k">{{ __('invoice.pay_reference') }}</td><td class="v strong">{{ $invoice->reference }}@if($lt) · {{ $lt->reference }}@endif</td></tr>
    </table>
    <div class="hint">{{ __('invoice.pay_hint') }}</div>
  </div>
  @endif

  @if($invoice->displayNote())<div class="note">{{ $invoice->displayNote() }}</div>@endif

  <div class="foot">
    <table><tr>
      <td>{{ site_name() }}@if($contact->email) · {{ $contact->email }}@endif</td>
      <td class="r">{{ $invoice->reference }}</td>
    </tr></table>
  </div>
</body>
</html>
