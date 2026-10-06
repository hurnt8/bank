@extends('layouts.client-app')
@section('title', __('app.loans_title') . ' — ' . site_name())
@section('page_title', __('app.loans_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('topbar_action')
<span style="font-size:.8rem;font-weight:800;color:var(--ca-accent-l);min-width:28px;text-align:center">
  {{ $loans->count() }}
</span>
@endsection

@push('styles')
<style>
/* ── Summary strip ── */
.dos-strip{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:.5rem;margin:.75rem 1.25rem 0;
}
.dos-chip{
  background:var(--ca-bg2);border:1px solid var(--ca-border);
  border-radius:14px;padding:.625rem .5rem;text-align:center;
}
.dos-chip__val{font-family:'Outfit',sans-serif;font-size:1.125rem;font-weight:900;color:var(--ca-text);line-height:1}
.dos-chip__lbl{font-size:.6rem;color:var(--ca-text-3);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em}

/* ── Filter pills ── */
.dos-filters{
  display:flex;gap:.5rem;padding:.875rem 1.25rem .375rem;
  overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;
}
.dos-filters::-webkit-scrollbar{display:none}
.dos-pill{
  display:inline-flex;align-items:center;gap:.375rem;
  padding:.35rem .875rem;border-radius:999px;
  font-size:.72rem;font-weight:700;white-space:nowrap;
  border:1.5px solid var(--ca-border);
  color:var(--ca-text-3);background:var(--ca-bg2);cursor:pointer;
  transition:.15s;
}
.dos-pill.active{background:rgba(220,190,135,.12);border-color:rgba(220,190,135,.3);color:var(--ca-accent-l)}

/* ── Loan cards ── */
.dos-list{display:flex;flex-direction:column;gap:.625rem;padding:0 1.25rem}

.dos-card{
  display:block;text-decoration:none;
  background:var(--ca-bg2);border:1px solid var(--ca-border);
  border-radius:18px;overflow:hidden;
  transition:.18s;position:relative;
}
.dos-card:active{transform:scale(.99)}

/* colored top bar */
.dos-card__bar{height:3px;width:100%}

.dos-card__inner{padding:1rem 1.125rem .875rem}

/* row 1: ref + badge */
.dos-card__top{display:flex;align-items:center;justify-content:space-between;margin-bottom:.625rem}
.dos-card__ref{font-size:.7rem;font-weight:700;color:var(--ca-text-3);font-family:monospace;letter-spacing:.04em}

/* Amount */
.dos-card__amount{
  font-family:'Outfit',sans-serif;
  font-size:1.5rem;font-weight:900;color:var(--ca-text);
  line-height:1;margin-bottom:.5rem;
}
.dos-card__amount span{font-size:.8rem;font-weight:600;color:var(--ca-text-3);margin-left:.3rem}

/* Meta row */
.dos-card__meta{
  display:flex;align-items:center;gap:.875rem;
  flex-wrap:wrap;margin-bottom:.75rem;
}
.dos-card__meta-item{
  display:flex;align-items:center;gap:.3rem;
  font-size:.72rem;color:var(--ca-text-3);
}
.dos-card__meta-item strong{color:var(--ca-text-2);font-weight:700}

/* Progress bar */
.dos-prog-wrap{height:4px;background:var(--ca-border);border-radius:999px;overflow:hidden}
.dos-prog-fill{height:100%;border-radius:999px;transition:width .4s}

/* ── Empty ── */
.dos-empty{text-align:center;padding:4rem 2rem 2rem}
.dos-empty__ico{font-size:2.5rem;opacity:.2;margin-bottom:1rem}
.dos-empty__title{font-size:1rem;font-weight:700;color:var(--ca-text-2);margin-bottom:.4rem}
.dos-empty__sub{font-size:.8rem;color:var(--ca-text-3);line-height:1.5}
</style>
@endpush

@section('content')

@if($loans->isEmpty())
<div class="dos-empty">
  <div class="dos-empty__ico"><i class="fas fa-folder-open"></i></div>
  <div class="dos-empty__title">{{ __('app.no_loans_title') }}</div>
  <div class="dos-empty__sub">{{ __('app.no_loans_body') }}</div>
  <div class="ca-btn-wrap" style="margin-top:1.5rem">
    <a href="{{ route('client.app.home') }}" class="ca-btn ca-btn--ghost">
      <i class="fas fa-arrow-left"></i> {{ __('app.nav_home') }}
    </a>
  </div>
</div>
@else

@php
  $finalized = $loans->where('status','finalized')->count();
  $active    = $loans->whereIn('status',['contract_sent','contract_signed'])->count();
  $pending   = $loans->whereIn('status',['pending','validated'])->count();
  $rejected  = $loans->where('status','rejected')->count();
@endphp

{{-- Summary strip ── --}}
<div class="dos-strip">
  <div class="dos-chip">
    <div class="dos-chip__val">{{ $loans->count() }}</div>
    <div class="dos-chip__lbl">{{ __('app.stat_total') }}</div>
  </div>
  <div class="dos-chip">
    <div class="dos-chip__val" style="color:var(--ca-accent-l)">{{ $active }}</div>
    <div class="dos-chip__lbl">{{ __('app.stat_active') }}</div>
  </div>
  <div class="dos-chip">
    <div class="dos-chip__val" style="color:var(--ca-accent-l)">{{ $finalized }}</div>
    <div class="dos-chip__lbl">{{ __('app.stat_finalized') }}</div>
  </div>
  <div class="dos-chip">
    <div class="dos-chip__val" style="color:#f87171">{{ $rejected }}</div>
    <div class="dos-chip__lbl">{{ __('app.dos_rejected') }}</div>
  </div>
</div>

{{-- Filter pills ── --}}
<div class="dos-filters" x-data="{f:'all'}">
  <button class="dos-pill" :class="f==='all'?'active':''" @click="f='all';filterDos('all')">{{ __('app.dos_all') }} ({{ $loans->count() }})</button>
  @if($pending)   <button class="dos-pill" :class="f==='pending'?'active':''"  @click="f='pending';filterDos('pending')">{{ __('app.dos_ongoing') }} ({{ $pending }})</button> @endif
  @if($active)    <button class="dos-pill" :class="f==='active'?'active':''"   @click="f='active';filterDos('active')">{{ __('app.dos_contract') }} ({{ $active }})</button> @endif
  @if($finalized) <button class="dos-pill" :class="f==='finalized'?'active':''" @click="f='finalized';filterDos('finalized')">{{ __('app.stat_finalized') }} ({{ $finalized }})</button> @endif
  @if($rejected)  <button class="dos-pill" :class="f==='rejected'?'active':''" @click="f='rejected';filterDos('rejected')">{{ __('app.dos_rejected') }} ({{ $rejected }})</button> @endif
</div>

{{-- Cards ── --}}
<div class="dos-list" style="margin-top:.375rem;padding-bottom:1.5rem">
@foreach($loans as $loan)
@php
  $steps = ['draft','pending','validated','contract_sent','contract_signed','finalized'];
  $idx   = array_search($loan->status, $steps);
  $pct   = $idx !== false ? round(($idx+1)/count($steps)*100) : 0;

  $filterGroup = match($loan->status){
    'draft', 'pending', 'validated' => 'pending',
    'contract_sent', 'contract_signed' => 'active',
    'finalized' => 'finalized',
    'rejected'  => 'rejected',
    default     => 'pending',
  };
  $barColor    = config("solberg.status_badges.loan.{$loan->status}.color", '#7A90AA');
  $statusLabel = $loan->statusLabel();
@endphp
<a href="{{ route('client.app.loans.show', $loan) }}" class="dos-card"
   data-group="{{ $filterGroup }}">
  <div class="dos-card__bar" style="background:{{ $barColor }}"></div>
  <div class="dos-card__inner">
    <div class="dos-card__top">
      <span class="dos-card__ref">{{ $loan->reference }}</span>
      <x-status-badge domain="loan" :status="$loan->status" :label="$statusLabel" />
    </div>
    <div class="dos-card__amount">
      {{ number_format($loan->amount, 0, ',', ' ') }}
      <span>{{ $loan->currency }}</span>
    </div>
    <div class="dos-card__meta">
      <div class="dos-card__meta-item">
        <i class="fas fa-calendar-day" style="font-size:.65rem"></i>
        <strong>{{ $loan->darly }} {{ __('app.months') }}</strong>
      </div>
      <div class="dos-card__meta-item">
        <i class="fas fa-percent" style="font-size:.65rem"></i>
        <strong>{{ $loan->interest_rate }} %</strong>
      </div>
      <div class="dos-card__meta-item">
        <i class="fas fa-receipt" style="font-size:.65rem"></i>
        <strong>{{ number_format($loan->monthly_payment, 0, ',', ' ') }} {{ $loan->currency }}{{ __('app.per_month') }}</strong>
      </div>
    </div>
    @if($loan->status !== 'rejected')
    <div class="dos-prog-wrap">
      <div class="dos-prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
    </div>
    @endif
  </div>
</a>
@endforeach
</div>

@push('scripts')
<script>
function filterDos(group) {
  document.querySelectorAll('.dos-card').forEach(el => {
    el.style.display = (group === 'all' || el.dataset.group === group) ? '' : 'none';
  });
}
</script>
@endpush

@endif
@endsection
