@php
    $isCredit = $movement->type === 'credit';
    $t        = fn ($key, $params = []) => __('movement.' . $key, $params, $locale);
    $amount   = ($isCredit ? '+' : '−') . number_format($movement->amount, 2, ',', ' ') . ' ' . $movement->currency;
    $partyLbl = $movement->kind === 'card' ? $t('label_merchant') : ($isCredit ? $t('label_from') : $t('label_to'));

    $rows = array_filter([
        [$t('label_type'), $movement->kindLabel($locale)],
        [$t('label_amount'), $amount],
        $movement->counterparty ? [$partyLbl, $movement->counterparty] : null,
        $movement->counterparty_iban ? [$t('label_iban'), \App\Models\Invoice::formatIban($movement->counterparty_iban)] : null,
        $movement->kind === 'card' && $movement->card_id && ($card = \App\Models\Card::find($movement->card_id)) ? [$t('label_card'), '•••• ' . $card->last_four] : null,
        $movement->reference ? [$t('label_reference'), $movement->reference] : null,
        $movement->note ? [$t('label_note'), $movement->note] : null,
        [$t('label_date'), $movement->created_at->format('d/m/Y H:i')],
        [$t('label_balance'), number_format($movement->balance_after, 2, ',', ' ') . ' ' . $movement->currency],
    ]);
@endphp
<x-email-layout
    :title="$t($isCredit ? 'mail_credit_title' : 'mail_debit_title')"
    :subtitle="$t('mail_subtitle')"
    accent="{{ $isCredit ? 'teal' : 'orange' }}"
    :locale="$locale"
>

  <p class="greeting">{!! $t($isCredit ? 'mail_credit_intro' : 'mail_debit_intro') !!}</p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0;border-collapse:collapse">
    @foreach($rows as [$label, $value])
    <tr>
      <td style="padding:9px 0;border-bottom:1px solid #e5e7eb;color:#6b7280;font-size:13px;width:42%">{{ $label }}</td>
      <td style="padding:9px 0;border-bottom:1px solid #e5e7eb;font-size:14px;font-weight:700;text-align:right;color:{{ $label === $t('label_amount') ? ($isCredit ? '#16a34a' : '#dc2626') : '#111827' }}">{{ $value }}</td>
    </tr>
    @endforeach
  </table>

  <div class="btn-wrap">
    <a href="{{ route('client.app.movements') }}" class="btn">{{ $t('mail_btn') }}</a>
  </div>

  <p class="closing">
    {{ $t('mail_closing') }}<br>
    <strong>{{ $t('mail_team', ['site' => site_name()]) }}</strong>
  </p>

</x-email-layout>
