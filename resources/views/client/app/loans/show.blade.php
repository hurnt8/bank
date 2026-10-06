@extends('layouts.client-app')
@section('title', $loan->reference . ' — ' . site_name())
@section('page_title', $loan->reference)
@section('back_btn', true)
@section('back_url', route('client.app.loans'))

@php
  $steps = [
    'draft'           => __('app.status_draft'),
    'pending'         => __('app.status_pending'),
    'validated'       => __('app.status_validated'),
    'contract_sent'   => __('app.status_sent'),
    'contract_signed' => __('app.status_signed'),
    'finalized'       => __('app.status_finalized'),
  ];
  $stepKeys   = array_keys($steps);
  $currentIdx = array_search($loan->status, $stepKeys);

  $accentColor = config("solberg.status_badges.loan.{$loan->status}.color", '#7A90AA');
@endphp

@section('topbar_action')
<x-status-badge domain="loan" :status="$loan->status" :label="$loan->statusLabel()" style="font-size:.62rem" />
@endsection

@push('styles')
<style>
/* ── Hero ── */
.ds-hero{
  margin:.75rem 1.25rem 0;
  background:linear-gradient(145deg,#1B4976,#0D2E52);
  border-radius:20px;padding:1.5rem;
  position:relative;overflow:hidden;
  box-shadow:0 12px 32px rgba(0,0,0,.4);
}
.ds-hero::before{
  content:'';position:absolute;top:-60px;right:-50px;
  width:180px;height:180px;border-radius:50%;
  background:radial-gradient(circle,rgba(27,138,122,.2) 0%,transparent 70%);
  pointer-events:none;
}
.ds-hero__ref{font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:.5rem;font-family:monospace}
.ds-hero__amount{font-family:'Outfit',sans-serif;font-size:2.25rem;font-weight:900;color:#fff;line-height:1;margin-bottom:.25rem}
.ds-hero__amount sup{font-size:.875rem;font-weight:600;color:rgba(255,255,255,.55);margin-right:.25rem;vertical-align:top;margin-top:.5rem}
.ds-hero__obj{font-size:.8rem;color:rgba(255,255,255,.45);margin-bottom:1rem}
.ds-hero__pills{display:flex;flex-wrap:wrap;gap:.5rem}
.ds-hero__pill{
  display:inline-flex;align-items:center;gap:.3rem;
  padding:.3rem .75rem;border-radius:999px;
  font-size:.7rem;font-weight:700;
  background:rgba(255,255,255,.08);
  border:1px solid rgba(255,255,255,.12);
  color:rgba(255,255,255,.7);
}
.ds-hero__pill--accent{background:rgba(220,190,135,.15);border-color:rgba(220,190,135,.3);color:#DCBE87}

/* ── Alert ── */
.ds-alert{
  margin:.875rem 1.25rem 0;
  border-radius:14px;padding:.875rem 1rem;
  display:flex;align-items:flex-start;gap:.625rem;
}
.ds-alert--warn  {background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.22)}
.ds-alert--ok    {background:rgba(74,222,128,.06);border:1px solid rgba(74,222,128,.2)}
.ds-alert--danger{background:rgba(248,113,113,.07);border:1px solid rgba(248,113,113,.2)}
.ds-alert__ico{flex-shrink:0;font-size:1rem;margin-top:.05rem}
.ds-alert--warn   .ds-alert__ico{color:#f59e0b}
.ds-alert--ok     .ds-alert__ico{color:#4ade80}
.ds-alert--danger .ds-alert__ico{color:#f87171}
.ds-alert__title{font-size:.82rem;font-weight:700;color:var(--ca-text);margin-bottom:.2rem}
.ds-alert__body{font-size:.75rem;color:var(--ca-text-3);line-height:1.5}

/* ── Quick stats ── */
.ds-kpis{display:grid;grid-template-columns:repeat(2,1fr);gap:.625rem;margin:.875rem 1.25rem 0}
.ds-kpi{
  background:var(--ca-bg2);border:1px solid var(--ca-border);border-radius:16px;
  padding:.875rem 1rem;
}
.ds-kpi__lbl{font-size:.65rem;text-transform:uppercase;letter-spacing:.07em;font-weight:700;color:var(--ca-text-3);margin-bottom:.35rem}
.ds-kpi__val{font-family:'Outfit',sans-serif;font-size:1.0625rem;font-weight:800;color:var(--ca-text);line-height:1}
.ds-kpi__sub{font-size:.67rem;color:var(--ca-text-3);margin-top:.2rem}

/* ── Section title ── */
.ds-sec{
  padding:.875rem 1.25rem .375rem;
  font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;
  color:var(--ca-text-3);display:flex;align-items:center;gap:.625rem;
}
.ds-sec::after{content:'';flex:1;height:1px;background:var(--ca-border-2)}

/* ── Progress steps ── */
.ds-steps{padding:0 1.25rem;display:flex;flex-direction:column;gap:0}
.ds-step{display:flex;align-items:flex-start;gap:.875rem;padding:.5rem 0;position:relative}
.ds-step:not(:last-child)::after{
  content:'';position:absolute;left:14px;top:36px;bottom:-4px;width:1.5px;
  background:var(--ca-border);
}
.ds-step__dot{
  width:28px;height:28px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:.65rem;font-weight:800;z-index:1;
  background:var(--ca-bg3);border:1.5px solid var(--ca-border);
  color:var(--ca-text-3);
}
.ds-step.done .ds-step__dot{background:rgba(220,190,135,.15);border-color:var(--ca-accent-l);color:var(--ca-accent-l)}
.ds-step.current .ds-step__dot{background:var(--ca-accent-l);border-color:var(--ca-accent-l);color:#fff;box-shadow:0 0 12px rgba(220,190,135,.35)}
.ds-step:not(:last-child).done::after{background:var(--ca-accent-l);opacity:.4}
.ds-step__info{padding-top:.4rem}
.ds-step__label{font-size:.825rem;font-weight:600;color:var(--ca-text-3)}
.ds-step.done    .ds-step__label{color:var(--ca-text-2)}
.ds-step.current .ds-step__label{color:var(--ca-text);font-weight:700}
.ds-step__tag{display:inline-block;font-size:.6rem;padding:.1rem .5rem;border-radius:999px;margin-top:.25rem;font-weight:700}
.ds-step__tag--done{background:rgba(220,190,135,.12);color:var(--ca-accent-l)}
.ds-step__tag--cur {background:rgba(220,190,135,.2);color:var(--ca-accent-l)}

/* ── Detail card ── */
.ds-detail{background:var(--ca-bg2);border:1px solid var(--ca-border);border-radius:18px;margin:0 1.25rem;overflow:hidden}
.ds-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:.8rem 1.125rem;border-bottom:1px solid var(--ca-border-2);
}
.ds-row:last-child{border-bottom:none}
.ds-row__left{display:flex;align-items:center;gap:.625rem}
.ds-row__ico{
  width:30px;height:30px;border-radius:9px;background:var(--ca-bg3);
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;color:var(--ca-text-3);flex-shrink:0;
}
.ds-row__label{font-size:.78rem;color:var(--ca-text-3)}
.ds-row__val{font-size:.8rem;font-weight:700;color:var(--ca-text);text-align:right;max-width:55%}
.ds-row__val--accent{color:var(--ca-accent-l)}
.ds-row__val--teal{color:var(--ca-teal-l)}

/* ── Amortization table ── */
.ds-amort-wrap{margin:0 1.25rem;border-radius:16px;overflow:hidden;border:1px solid var(--ca-border)}
.ds-amort-inner{overflow-x:auto;max-height:260px;overflow-y:auto}
.ds-amort-inner::-webkit-scrollbar{width:3px;height:3px}
.ds-amort-inner::-webkit-scrollbar-thumb{background:var(--ca-border);border-radius:99px}
table.ds-table{width:100%;border-collapse:collapse;font-size:.75rem}
table.ds-table th{
  background:var(--ca-bg3);color:var(--ca-text-3);font-weight:700;
  padding:.5rem .75rem;text-align:left;white-space:nowrap;
  position:sticky;top:0;z-index:1;border-bottom:1px solid var(--ca-border);
  font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;
}
table.ds-table td{padding:.5rem .75rem;border-bottom:1px solid var(--ca-border-2);color:var(--ca-text-2);white-space:nowrap}
table.ds-table tr:last-child td{border-bottom:none}
table.ds-table td.td-num{color:var(--ca-text-3);font-size:.7rem}
table.ds-table td.td-pay{font-weight:700;color:var(--ca-text)}
table.ds-table td.td-cap{color:var(--ca-teal-l)}
table.ds-table td.td-int{color:#f59e0b}
table.ds-table td.td-rem{color:var(--ca-text-3)}

/* ── Amortization table — card mode ≤ 640 px ── */
@media(max-width:640px){
  .ds-amort-wrap{margin:0 .875rem}
  table.ds-table{display:block}
  table.ds-table thead{display:none}
  table.ds-table tbody{display:block}
  table.ds-table tbody tr{
    display:block;
    background:var(--ca-bg2);
    border:1px solid var(--ca-border);
    border-radius:12px;
    padding:.75rem;
    margin-bottom:.625rem;
  }
  table.ds-table tbody td{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:.3rem 0;
    border-bottom:1px solid var(--ca-border-2);
    font-size:.8rem;
    white-space:normal;
    gap:.5rem;
  }
  table.ds-table tbody td:last-child{border-bottom:none}
  table.ds-table tbody td[data-label]::before{
    content:attr(data-label);
    font-size:.65rem;
    font-weight:700;
    color:var(--ca-text-3);
    text-transform:uppercase;
    letter-spacing:.05em;
    flex-shrink:0;
    white-space:nowrap;
  }
  table.ds-table td.td-pay{font-weight:700;color:var(--ca-text)}
  table.ds-table td.td-cap{color:var(--ca-teal-l)}
  table.ds-table td.td-int{color:#f59e0b}
  table.ds-table td.td-num,
  table.ds-table td.td-rem{color:var(--ca-text-3)}
}
</style>
@endpush

@section('content')

{{-- Hero ── --}}
<div class="ds-hero">
  <div class="ds-hero__ref">{{ $loan->reference }}</div>
  <div class="ds-hero__amount">
    <sup>{{ $loan->currency }}</sup>{{ number_format($loan->amount, 0, ',', ' ') }}
  </div>
  <div class="ds-hero__obj">{{ $loan->objet ?? __('app.loan_card_label') }}</div>
  <div class="ds-hero__pills">
    <div class="ds-hero__pill">
      <i class="fas fa-calendar" style="font-size:.6rem"></i>
      {{ $loan->darly }} {{ __('app.months') }}
    </div>
    <div class="ds-hero__pill">
      <i class="fas fa-percent" style="font-size:.6rem"></i>
      {{ $loan->interest_rate }}%
    </div>
    <div class="ds-hero__pill ds-hero__pill--accent">
      <i class="fas fa-receipt" style="font-size:.6rem"></i>
      {{ number_format($loan->monthly_payment, 0, ',', ' ') }} {{ $loan->currency }}{{ __('app.per_month') }}
    </div>
  </div>
</div>

{{-- Alerts ── --}}
@if($loan->status === 'contract_sent')
<div class="ds-alert ds-alert--warn">
  <div class="ds-alert__ico"><i class="fas fa-envelope"></i></div>
  <div>
    <div class="ds-alert__title">{{ __('app.status_sent') }}</div>
    <div class="ds-alert__body">{{ __('app.loan_alert_sent_body', ['date' => $loan->sent_at?->format('d/m/Y') ?? '—']) }}</div>
  </div>
</div>
@endif
@if($loan->status === 'finalized')
<div class="ds-alert ds-alert--ok">
  <div class="ds-alert__ico"><i class="fas fa-circle-check"></i></div>
  <div>
    <div class="ds-alert__title">{{ __('app.loan_alert_fin_title') }}</div>
    <div class="ds-alert__body">{{ __('app.loan_alert_fin_body', ['amount' => number_format($loan->amount,2,',',' '), 'currency' => $loan->currency]) }}</div>
  </div>
</div>
@endif
@if($loan->status === 'rejected')
<div class="ds-alert ds-alert--danger">
  <div class="ds-alert__ico"><i class="fas fa-ban"></i></div>
  <div>
    <div class="ds-alert__title">{{ __('app.loan_alert_rej_title') }}</div>
    <div class="ds-alert__body">{{ __('app.loan_alert_rej_body') }}</div>
  </div>
</div>
@endif

{{-- Quick KPIs ── --}}
<div class="ds-kpis">
  <div class="ds-kpi">
    <div class="ds-kpi__lbl">{{ __('app.loan_capital') }}</div>
    <div class="ds-kpi__val">{{ number_format($principal, 0, ',', ' ') }}</div>
    <div class="ds-kpi__sub">{{ $loan->currency }}</div>
  </div>
  <div class="ds-kpi">
    <div class="ds-kpi__lbl">{{ __('app.loan_interest') }}</div>
    <div class="ds-kpi__val" style="color:var(--ca-accent-l)">{{ number_format($interest, 0, ',', ' ') }}</div>
    <div class="ds-kpi__sub">{{ $loan->currency }} · {{ $loan->interest_rate }}%</div>
  </div>
  <div class="ds-kpi">
    <div class="ds-kpi__lbl">{{ __('app.loan_total') }}</div>
    <div class="ds-kpi__val">{{ number_format($loan->total_with_interest, 0, ',', ' ') }}</div>
    <div class="ds-kpi__sub">{{ $loan->currency }}</div>
  </div>
  <div class="ds-kpi">
    <div class="ds-kpi__lbl">{{ __('app.monthly') }}</div>
    <div class="ds-kpi__val" style="color:var(--ca-teal-l)">{{ number_format($loan->monthly_payment, 0, ',', ' ') }}</div>
    <div class="ds-kpi__sub">{{ $loan->currency }}{{ __('app.per_month') }}</div>
  </div>
</div>

{{-- Progress ── --}}
@if($loan->status !== 'rejected')
<div class="ds-sec">{{ __('app.progress') }}</div>
<div class="ds-steps">
@foreach($steps as $key => $label)
@php
  $i    = array_search($key, $stepKeys);
  $done = $currentIdx !== false && $i <= $currentIdx;
  $cur  = $loan->status === $key;
@endphp
<div class="ds-step {{ $cur ? 'current' : ($done ? 'done' : '') }}">
  <div class="ds-step__dot">
    @if($done && !$cur)
      <i class="fas fa-check" style="font-size:.5rem"></i>
    @else
      {{ $i + 1 }}
    @endif
  </div>
  <div class="ds-step__info">
    <div class="ds-step__label">{{ $label }}</div>
    @if($cur)
      <span class="ds-step__tag ds-step__tag--cur">{{ __('app.step_current') }}</span>
    @elseif($done)
      <span class="ds-step__tag ds-step__tag--done">{{ __('app.step_done') }}</span>
    @endif
  </div>
</div>
@endforeach
</div>
@endif

{{-- Détails financement ── --}}
<div class="ds-sec" style="margin-top:.875rem">{{ __('app.loan_details') }}</div>
<div class="ds-detail">
  @foreach([
    ['fa-coins',        __('app.loan_capital'),  number_format($principal,2,',',' ').' '.$loan->currency, 'teal'],
    ['fa-percent',      __('app.rate'),           $loan->interest_rate.' %', ''],
    ['fa-chart-line',   __('app.loan_interest'),  number_format($interest,2,',',' ').' '.$loan->currency, 'accent'],
    ['fa-calculator',   __('app.loan_total'),     number_format($loan->total_with_interest,2,',',' ').' '.$loan->currency, ''],
    ['fa-receipt',      __('app.loan_fees'),      $loan->admin_fees ? number_format($loan->admin_fees,2,',',' ').' '.$loan->currency : '—', ''],
    ['fa-calendar-day', __('app.loan_start'),     $loan->start_date?->format('d/m/Y') ?? '—', ''],
    ['fa-tag',          __('app.loan_object'),    $loan->objet ?? '—', ''],
  ] as [$icon, $label, $val, $color])
  <div class="ds-row">
    <div class="ds-row__left">
      <div class="ds-row__ico"><i class="fas {{ $icon }}"></i></div>
      <span class="ds-row__label">{{ $label }}</span>
    </div>
    <span class="ds-row__val @if($color==='teal') ds-row__val--teal @elseif($color==='accent') ds-row__val--accent @endif">{{ $val }}</span>
  </div>
  @endforeach
</div>

{{-- Suivi ── --}}
<div class="ds-sec" style="margin-top:.875rem">{{ __('app.loan_tracking') }}</div>
<div class="ds-detail">
  @foreach([
    ['fa-hashtag',       __('app.loan_ref'),       $loan->reference],
    ['fa-calendar-plus', __('app.loan_opened'),     $loan->created_at->format('d/m/Y')],
    ['fa-check-double',  __('app.loan_validated'),  $loan->validated_at?->format('d/m/Y') ?? '—'],
    ['fa-envelope-open', __('app.loan_sent'),       $loan->sent_at?->format('d/m/Y') ?? '—'],
    ['fa-file-check',    __('app.loan_signed'),     $loan->signed_received_at?->format('d/m/Y') ?? '—'],
    ['fa-user-tie',      __('app.loan_advisor'),    $loan->admin?->name ?? '—'],
  ] as [$icon, $label, $val])
  <div class="ds-row">
    <div class="ds-row__left">
      <div class="ds-row__ico"><i class="fas {{ $icon }}"></i></div>
      <span class="ds-row__label">{{ $label }}</span>
    </div>
    <span class="ds-row__val">{{ $val }}</span>
  </div>
  @endforeach
</div>

{{-- Amortissement ── --}}
@if($loan->amortization_schedule && $loan->status !== 'draft')
<div class="ds-sec" style="margin-top:.875rem">
  {{ __('app.amortization') }} — {{ count($loan->amortization_schedule) }} {{ __('app.installments') }}
</div>
<div class="ds-amort-wrap">
  <div class="ds-amort-inner">
    <table class="ds-table">
      <thead>
        <tr>
          <th>{{ __('app.amort_num') }}</th>
          <th>{{ __('app.monthly') }}</th>
          <th>{{ __('app.loan_capital') }}</th>
          <th>{{ __('app.loan_interest') }}</th>
          <th>{{ __('app.amort_remaining') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->amortization_schedule as $row)
        <tr>
          <td data-label="N°" class="td-num">{{ $row['month'] }}</td>
          <td data-label="Mensualité" class="td-pay">{{ number_format($row['payment'],2,',',' ') }}</td>
          <td data-label="Capital" class="td-cap">{{ number_format($row['principal'],2,',',' ') }}</td>
          <td data-label="Intérêts" class="td-int">{{ number_format($row['interest'],2,',',' ') }}</td>
          <td data-label="Solde" class="td-rem">{{ number_format($row['balance'],2,',',' ') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

<div style="height:1.5rem"></div>

@endsection
