@extends('layouts.dashboard')
@section('title', __('app.loan_ref').' '.$loan->reference.' — ' . site_name())
@section('page_title', __('app.loan_ref').' '.$loan->reference)

@push('styles')
<style>
  /* ── Tableau d'amortissement — responsive mobile ── */
  .cl-amort-wrap { overflow-x: auto; overflow-y: auto; max-height: 380px; -webkit-overflow-scrolling: touch; }

  @media(max-width:640px) {
    /* Panel head wrap */
    .cl-panel__head { flex-wrap: wrap; gap: .5rem; }
    /* Steps : scroll horizontal sur mobile */
    .cl-steps { padding-bottom: .5rem; }
    /* Row g-4 : réduire gap sur mobile */
    .row.g-4 { --bs-gutter-y: 1rem; }
  }

  @media(max-width:480px) {
    .cl-data-row { flex-wrap: wrap; gap: .2rem; }
    .cl-data-row__val { text-align: left; flex: 1 1 100%; }
    .cl-track-label { font-size: .65rem; }
    .cl-track-val   { font-size: .78rem; }
  }
</style>
@endpush

@section('content')
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

  $badgeClass = match($loan->status) {
      'draft'           => 'cl-badge--draft',
      'pending'         => 'cl-badge--pending',
      'validated'       => 'cl-badge--validated',
      'contract_sent'   => 'cl-badge--sent',
      'contract_signed' => 'cl-badge--signed',
      'finalized'       => 'cl-badge--finalized',
      'rejected'        => 'cl-badge--rejected',
      default           => 'cl-badge--draft',
  };

  $principal = (float) $loan->amount;
  $total     = (float) $loan->total_with_interest;
  $interest  = max(0, $total - $principal);
@endphp
<div class="cl-scope">

{{-- ── Page header ──────────────────────────────────────────── --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
      <span style="font-family:'Space Grotesk', monospace;font-size:1rem;font-weight:700;color:var(--cl-accent)">
        {{ $loan->reference }}
      </span>
      <span class="cl-badge {{ $badgeClass }}">{{ $loan->statusLabel() }}</span>
    </div>
    <div style="font-size:.8rem;color:var(--cl-muted)">
      {{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }} ·
      {{ $loan->darly }} {{ __('app.months') }} · {{ $loan->interest_rate }}% · {{ __('app.date_opened') }} {{ $loan->created_at->format('d/m/Y') }}
    </div>
  </div>
  <a href="{{ route('client.loans') }}" class="cl-btn cl-btn--ghost">
    <i class="fas fa-arrow-left"></i> {{ __('app.loans_title') }}
  </a>
</div>

{{-- ── Alerts ───────────────────────────────────────────────── --}}
@if($loan->status === 'contract_sent')
<div class="cl-alert cl-alert--warn mb-4">
  <div class="cl-alert__icon"><i class="fas fa-envelope"></i></div>
  <div>
    <div class="cl-alert__title">{{ __('app.contract_pending_title') }}</div>
    {{ __('app.contract_pending_body', ['date' => $loan->sent_at?->format('d/m/Y')]) }}
  </div>
</div>
@endif

@if($loan->status === 'finalized')
<div class="cl-alert cl-alert--green mb-4">
  <div class="cl-alert__icon"><i class="fas fa-check-circle"></i></div>
  <div>
    <div class="cl-alert__title">{{ __('app.funded_title') }}</div>
    {{ __('app.funded_body', ['amount' => number_format($loan->amount, 2, ',', ' '), 'currency' => $loan->currency]) }}
  </div>
</div>
@endif

@if($loan->status === 'rejected')
<div class="cl-alert cl-alert--error mb-4">
  <div class="cl-alert__icon"><i class="fas fa-ban"></i></div>
  <div>
    <div class="cl-alert__title">{{ __('app.rejected_title') }}</div>
    {{ __('app.contact_advisor') }}
  </div>
</div>
@endif

{{-- ── Timeline ─────────────────────────────────────────────── --}}
@if($loan->status !== 'rejected')
<div class="cl-panel mb-4">
  <div class="cl-panel__head">
    <div class="cl-panel__title">
      <span class="cl-panel__dot"></span> {{ __('app.file_progress') }}
    </div>
    @php
      $pct = $currentIdx !== false ? round(($currentIdx + 1) / count($stepKeys) * 100) : 0;
    @endphp
    <span style="font-size:.72rem;color:var(--cl-accent-2);font-weight:700">{{ $pct }}%</span>
  </div>
  <div class="cl-panel__body" style="padding:1.5rem 1.25rem">
    <div class="cl-steps">
      @foreach($steps as $key => $label)
      @php
        $i    = array_search($key, $stepKeys);
        $done = $currentIdx !== false && $i <= $currentIdx;
        $cur  = $loan->status === $key;
      @endphp
      <div class="cl-step {{ $cur ? 'current' : ($done ? 'done' : '') }}">
        <div class="cl-step__dot">
          @if($done && !$cur)
            <i class="fas fa-check" style="font-size:.55rem"></i>
          @else
            {{ $i + 1 }}
          @endif
        </div>
        <div class="cl-step__label">{{ $label }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- ── Main content ─────────────────────────────────────────── --}}
<div class="row g-4">

  {{-- Financement --}}
  <div class="col-lg-7">
    <div class="cl-panel h-100">
      <div class="cl-panel__head">
        <div class="cl-panel__title">
          <span class="cl-panel__dot"></span> {{ __('app.loan_details') }}
        </div>
      </div>
      <div class="cl-panel__body">

        {{-- Hero amounts --}}
        <div class="row g-3 mb-4">
          <div class="col-6">
            <div style="background:var(--cl-surface-2);border-radius:12px;padding:1rem;border:1px solid var(--cl-border)">
              <div style="font-size:.65rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.4rem">
                {{ __('app.loan_amount') }}
              </div>
              <div style="font-family:'Outfit',sans-serif;font-size:1.5rem;font-weight:700;color:var(--cl-text);line-height:1">
                {{ number_format($loan->amount, 2, ',', ' ') }}
                <span style="font-size:.8rem;color:var(--cl-accent);font-weight:600">{{ $loan->currency }}</span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div style="background:var(--cl-surface-2);border-radius:12px;padding:1rem;border:1px solid var(--cl-border)">
              <div style="font-size:.65rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.4rem">
                {{ __('app.monthly') }}
              </div>
              <div style="font-family:'Outfit',sans-serif;font-size:1.5rem;font-weight:700;color:var(--cl-accent-2);line-height:1">
                {{ number_format($loan->monthly_payment, 2, ',', ' ') }}
                <span style="font-size:.8rem;font-weight:600">{{ $loan->currency }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Data rows --}}
        @foreach([
          [__('app.total_duration'), $loan->darly.' '.__('app.months')],
          [__('app.interest_rate'), $loan->interest_rate.' %'],
          [__('app.loan_total'), number_format($loan->total_with_interest, 2, ',', ' ').' '.$loan->currency],
          [__('app.total_credit_cost'), number_format($loan->total_cost ?? 0, 2, ',', ' ').' '.$loan->currency],
          [__('app.loan_fees'), $loan->admin_fees ? number_format($loan->admin_fees, 2, ',', ' ').' '.$loan->currency : '—'],
          [__('app.loan_start'), $loan->start_date?->format('d/m/Y') ?? '—'],
          [__('app.loan_object'), $loan->objet ?? '—'],
        ] as [$lbl, $val])
        <div class="cl-data-row">
          <span class="cl-data-row__label">{{ $lbl }}</span>
          <span class="cl-data-row__val">{{ $val }}</span>
        </div>
        @endforeach

        @if($loan->special_conditions)
        <div style="margin-top:1rem;background:var(--cl-surface-2);border-radius:10px;padding:.875rem;border:1px solid var(--cl-border)">
          <div style="font-size:.68rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.4rem">
            {{ __('app.loan_conditions') }}
          </div>
          <div style="font-size:.82rem;color:var(--cl-text-2)">{{ $loan->special_conditions }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Sidebar: chart + tracking --}}
  <div class="col-lg-5 d-flex flex-column gap-4">

    {{-- Doughnut chart --}}
    @if($total > 0)
    <div class="cl-panel">
      <div class="cl-panel__head">
        <div class="cl-panel__title">
          <span class="cl-panel__dot"></span> {{ __('app.financing_breakdown') }}
        </div>
      </div>
      <div class="cl-panel__body">
        <div class="cl-chart-wrap" style="max-height:220px">
          <canvas id="amortChart" style="max-height:220px"></canvas>
          <div class="cl-chart-center">
            <div class="cl-chart-center__val">
              {{ number_format($principal / max(1, $total) * 100, 0) }}%
            </div>
            <div class="cl-chart-center__lbl">{{ __('app.loan_capital') }}</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1.25rem">
          <div style="background:rgba(29,58,92,.4);border-radius:9px;padding:.75rem;border:1px solid rgba(29,58,92,.6)">
            <div style="font-size:.65rem;color:var(--cl-muted);margin-bottom:.2rem">{{ __('app.loan_capital') }}</div>
            <div style="font-weight:700;color:var(--cl-text-2);font-size:.85rem">
              {{ number_format($principal, 2, ',', ' ') }} {{ $loan->currency }}
            </div>
          </div>
          <div style="background:rgba(184,136,62,.08);border-radius:9px;padding:.75rem;border:1px solid rgba(184,136,62,.2)">
            <div style="font-size:.65rem;color:var(--cl-muted);margin-bottom:.2rem">{{ __('app.loan_interest') }}</div>
            <div style="font-weight:700;color:var(--cl-accent-2);font-size:.85rem">
              {{ number_format($interest, 2, ',', ' ') }} {{ $loan->currency }}
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Suivi dossier --}}
    <div class="cl-panel">
      <div class="cl-panel__head">
        <div class="cl-panel__title">
          <span class="cl-panel__dot"></span> {{ __('app.loan_tracking') }}
        </div>
      </div>
      <div class="cl-panel__body" style="padding:1rem 1.25rem">
        @foreach([
          ['fa-hashtag',         __('app.loan_ref'),        $loan->reference],
          ['fa-calendar-plus',   __('app.loan_opened'),     $loan->created_at->format('d/m/Y')],
          ['fa-check-double',    __('app.loan_validated'),  $loan->validated_at?->format('d/m/Y') ?? __('app.status_pending')],
          ['fa-envelope-open',   __('app.loan_sent'),       $loan->sent_at?->format('d/m/Y') ?? '—'],
          ['fa-file-check',      __('app.loan_signed'),     $loan->signed_received_at?->format('d/m/Y') ?? '—'],
          ['fa-user-tie',        __('app.loan_advisor'),    $loan->admin?->name ?? '—'],
        ] as [$icon, $label, $value])
        <div class="cl-track-item">
          <div class="cl-track-icon"><i class="fas {{ $icon }}"></i></div>
          <div>
            <div class="cl-track-label">{{ $label }}</div>
            <div class="cl-track-val">{{ $value }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div>
</div>

{{-- ── Amortization table ───────────────────────────────────── --}}
@if($loan->amortization_schedule && $loan->status !== 'draft')
<div class="cl-panel mt-4">
  <div class="cl-panel__head">
    <div class="cl-panel__title">
      <span class="cl-panel__dot"></span> {{ __('app.amortization') }}
    </div>
    <span style="font-size:.72rem;color:var(--cl-muted)">
      {{ __('app.installments_count', ['count' => count($loan->amortization_schedule), 'months' => $loan->darly]) }}
    </span>
  </div>
  <div class="cl-amort-wrap">
    <table class="cl-table">
      <thead>
        <tr>
          <th>{{ __('app.amort_num') }}</th>
          <th>{{ __('app.monthly') }}</th>
          <th>{{ __('app.principal_paid') }}</th>
          <th>{{ __('app.interest_paid') }}</th>
          <th>{{ __('app.remaining_capital') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->amortization_schedule as $row)
        <tr>
          <td data-label="{{ __('app.amort_num') }}" class="td-muted">{{ $row['month'] }}</td>
          <td data-label="{{ __('app.monthly') }}" class="td-bold">{{ number_format($row['payment'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td data-label="{{ __('app.principal_paid') }}" class="td-green">{{ number_format($row['principal'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td data-label="{{ __('app.interest_paid') }}" class="td-red">{{ number_format($row['interest'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td data-label="{{ __('app.remaining_capital') }}" class="td-muted">{{ number_format($row['balance'], 2, ',', ' ') }} {{ $loan->currency }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof buildAmortChart !== 'undefined') {
    buildAmortChart(
      'amortChart',
      {{ $principal }},
      {{ $interest }},
      '{{ $loan->currency }}'
    );
  }
});
</script>
@endpush
