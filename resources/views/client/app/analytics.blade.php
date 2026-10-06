@extends('layouts.client-app')
@section('title', __('app.analytics_title') . ' — ' . site_name())
@section('page_title', __('app.analytics_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('content')

@php
  $currency      = $user->currency ?? \App\Models\Currency::default();
  $totalSchedule = $loans->sum(fn($l) => (float) $l->total_with_interest);
  $totalCapital  = $loans->sum(fn($l) => (float) $l->amount);
  $totalInterest = max(0, $totalSchedule - $totalCapital);
  $loanCount     = $loans->count();
  $hasData       = $totalSchedule > 0;

  $donutData   = [$totalCapital, $totalInterest, (float) $totalPaid, (float) $totalReceived];
  $donutColors = ['rgba(27,138,122,.75)', 'rgba(220,190,135,.75)', 'rgba(255,90,90,.75)', 'rgba(74,222,128,.65)'];
  $donutLabels = [__('app.chart_capital'), __('app.chart_interest'), __('app.chart_transfers'), 'Crédits reçus'];

  $lineLabels = array_keys(array_slice($monthlyData, 0, 10));
  $lineValues = array_values(array_slice($monthlyData, 0, 10));
@endphp

<div x-data="{ period: 'month' }" style="margin-top:.75rem">

  <div class="ca-period-tabs">
    @foreach(['day'=>__('app.period_day'),'week'=>__('app.period_week'),'month'=>__('app.period_month'),'year'=>__('app.period_year')] as $p => $label)
    <button class="ca-period-tab" :class="period==='{{ $p }}' ? 'active' : ''"
            @click="period='{{ $p }}'" type="button">{{ $label }}</button>
    @endforeach
  </div>

  {{-- Hero total --}}
  <div class="ca-chart-hero">
    <div class="ca-chart-hero__amount">
      {{ $currency }} {{ number_format($totalSchedule, 2, ',', ' ') }}
    </div>
    <div class="ca-chart-hero__label">{{ __('app.total_schedule') }}</div>
  </div>

  {{-- Doughnut chart --}}
  @if($hasData)
  <div class="ca-chart-container" style="padding:0 1.25rem">
    <canvas id="analyticsDonut" style="max-height:220px"></canvas>
    <div class="ca-chart-center-label">
      <div class="ca-chart-center-label__val">{{ $loanCount }}</div>
      <div class="ca-chart-center-label__sub">{{ __('app.categories') }}</div>
    </div>
  </div>
  @endif

  {{-- 4 KPI chips --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:.625rem;padding:.875rem 1.25rem 0">
    <div class="ca-stat-chip">
      <div class="ca-stat-chip__val" style="color:var(--ca-teal-l)">
        {{ number_format($totalReceived, 0, ',', ' ') }}
      </div>
      <div class="ca-stat-chip__lbl">Crédits reçus</div>
    </div>
    <div class="ca-stat-chip">
      <div class="ca-stat-chip__val" style="color:var(--ca-negative)">
        {{ number_format($totalPaid, 0, ',', ' ') }}
      </div>
      <div class="ca-stat-chip__lbl">{{ __('app.total_sent') }}</div>
    </div>
    <div class="ca-stat-chip">
      <div class="ca-stat-chip__val" style="color:var(--ca-accent-l)">
        {{ number_format($totalInterest, 0, ',', ' ') }}
      </div>
      <div class="ca-stat-chip__lbl">Intérêts</div>
    </div>
    <div class="ca-stat-chip">
      <div class="ca-stat-chip__val" style="color:var(--ca-amber)">
        {{ number_format($pendingAmount, 0, ',', ' ') }}
      </div>
      <div class="ca-stat-chip__lbl">Virements en attente</div>
    </div>
  </div>

  {{-- Pending transfers alert --}}
  @if($pendingTransfers->isNotEmpty())
  <div style="margin:.875rem 1.25rem 0;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.22);border-left:3px solid var(--ca-amber);border-radius:14px;padding:.75rem 1rem;display:flex;align-items:center;gap:.625rem">
    <i class="fas fa-hourglass-half" style="color:var(--ca-amber);font-size:.9rem;flex-shrink:0"></i>
    <div style="font-size:.78rem;color:rgba(255,255,255,.8);line-height:1.5">
      <strong style="color:var(--ca-amber)">{{ $pendingTransfers->count() }} virement{{ $pendingTransfers->count() > 1 ? 's' : '' }} en cours</strong>
      — Montant réservé : {{ number_format($pendingAmount, 2, ',', ' ') }} {{ $currency }}
    </div>
  </div>
  @endif

  {{-- Categories --}}
  <div class="ca-section" style="margin-top:.75rem">
    <span class="ca-section__title">{{ __('app.top_categories') }}</span>
  </div>

  <div class="ca-txn-list">
    @php $maxVal = max($totalCapital, (float)$totalPaid, $totalInterest, (float)$totalReceived, 1); @endphp

    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(27,138,122,.15);color:var(--ca-teal-l)">
        <i class="fas fa-file-contract"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">{{ __('app.cat_loans') }}</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-teal-l);width:{{ min(100, $totalCapital/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($totalCapital, 0, ',', ' ') }}</div>
    </div>

    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(74,222,128,.12);color:var(--ca-positive)">
        <i class="fas fa-arrow-down"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">Crédits reçus</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-positive);width:{{ min(100, $totalReceived/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($totalReceived, 0, ',', ' ') }}</div>
    </div>

    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(220,190,135,.15);color:var(--ca-accent-l)">
        <i class="fas fa-percent"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">{{ __('app.cat_fees') }}</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-accent-l);width:{{ min(100, $totalInterest/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($totalInterest, 0, ',', ' ') }}</div>
    </div>

    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(255,90,90,.1);color:var(--ca-negative)">
        <i class="fas fa-right-left"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">{{ __('app.cat_transfers') }}</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-negative);width:{{ min(100, (float)$totalPaid/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($totalPaid, 0, ',', ' ') }}</div>
    </div>

    @if($pendingAmount > 0)
    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(245,158,11,.12);color:var(--ca-amber)">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">Virements en attente</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-amber);width:{{ min(100, $pendingAmount/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($pendingAmount, 0, ',', ' ') }}</div>
    </div>
    @endif
  </div>

  {{-- Monthly schedule line chart --}}
  @if(count($lineLabels) > 1)
  <div class="ca-section" style="margin-top:.75rem">
    <span class="ca-section__title">{{ __('app.monthly_schedule') }}</span>
  </div>
  <div style="padding:0 1.25rem 1rem;background:var(--ca-bg3);margin:0 1.25rem;border-radius:var(--ca-radius-md);border:1px solid var(--ca-border)">
    <canvas id="analyticsLine" style="max-height:160px;margin-top:1rem"></canvas>
  </div>
  @endif

</div>

<div style="height:1rem"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  @if($hasData)
  buildDoughnutChart(
    'analyticsDonut',
    {!! json_encode($donutData) !!},
    {!! json_encode($donutColors) !!},
    {!! json_encode($donutLabels) !!}
  );
  @endif

  @if(count($lineLabels) > 1)
  buildLineChart(
    'analyticsLine',
    {!! json_encode($lineLabels) !!},
    {!! json_encode($lineValues) !!},
    '{{ $currency }}'
  );
  @endif
});
</script>
@endpush
