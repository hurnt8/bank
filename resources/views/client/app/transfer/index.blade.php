@extends('layouts.client-app')
@section('title', __('app.nav_transfer') . ' — ' . site_name())
@section('page_title', __('app.nav_transfer'))

@section('content')

@push('styles')
<style>
/* ── Balance hero ── */
.trf-hero{
  margin:.875rem 1.25rem 0;
  background:linear-gradient(145deg,#1B527A,#0D2E54);
  border-radius:20px;
  padding:1.375rem 1.5rem;
  position:relative;overflow:hidden;
  box-shadow:0 12px 32px rgba(0,0,0,.4);
}
.trf-hero::before{
  content:'';position:absolute;top:-60px;right:-60px;
  width:200px;height:200px;border-radius:50%;
  background:radial-gradient(circle,rgba(27,138,122,.18) 0%,transparent 70%);
  pointer-events:none;
}
.trf-hero__lbl{font-size:.63rem;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.4);margin-bottom:.3rem}
.trf-hero__bal{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:900;color:#fff;line-height:1}
.trf-hero__cur{font-size:.875rem;font-weight:600;color:rgba(255,255,255,.5);margin-left:.3rem}
.trf-hero__sub{font-size:.72rem;color:rgba(255,255,255,.35);margin-top:.35rem}

/* ── Action grid ── */
.trf-actions{display:grid;grid-template-columns:1fr 1fr;gap:.875rem;padding:1rem 1.25rem 0}
.trf-action{
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.625rem;
  padding:1.375rem 1rem;border-radius:18px;text-decoration:none;transition:.18s;
}
.trf-action:active{transform:scale(.97)}
.trf-action--send{background:rgba(27,138,122,.15);border:1px solid rgba(27,138,122,.28)}
.trf-action--recv{background:rgba(220,190,135,.12);border:1px solid rgba(220,190,135,.22)}
.trf-action__ico{
  width:54px;height:54px;border-radius:16px;
  display:flex;align-items:center;justify-content:center;font-size:1.25rem;color:#fff;
}
.trf-action--send .trf-action__ico{background:linear-gradient(145deg,#F5EDDD,#DCBE87);box-shadow:0 4px 14px rgba(220,190,135,.4)}
.trf-action--recv .trf-action__ico{background:linear-gradient(145deg,#F5EDDD,#DCBE87);box-shadow:0 4px 14px rgba(220,190,135,.35)}
.trf-action__name{font-size:.875rem;font-weight:700;color:var(--ca-text)}
.trf-action__desc{font-size:.7rem;color:var(--ca-text-3);text-align:center;line-height:1.4}

/* ── Section header ── */
.trf-section{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.25rem .625rem}
.trf-section__title{font-size:.85rem;font-weight:700;color:var(--ca-text)}
.trf-section__link{font-size:.75rem;font-weight:600;color:var(--ca-accent-l);display:inline-flex;align-items:center;gap:.3rem}

/* ── Transfer list ── */
.trf-list{padding:0 1.25rem;display:flex;flex-direction:column;gap:.5rem}
.trf-item{
  display:flex;align-items:center;gap:.875rem;
  background:var(--ca-bg2);border:1px solid var(--ca-border);
  border-radius:14px;padding:.875rem 1rem;
}
.trf-item__ico{
  width:42px;height:42px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;font-size:.9rem;
}
.trf-item__ico--send   {background:rgba(255,90,90,.12);color:var(--ca-negative)}
.trf-item__ico--pending{background:rgba(245,158,11,.12);color:#f59e0b}
.trf-item__ico--fee    {background:rgba(96,165,250,.12);color:#60a5fa}
.trf-item__ico--done   {background:rgba(74,222,128,.12);color:var(--ca-positive)}
.trf-item__ico--rej    {background:rgba(148,163,184,.12);color:#94a3b8}

.trf-item__body{flex:1;min-width:0}
.trf-item__name{font-size:.875rem;font-weight:600;color:var(--ca-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.trf-item__ref{font-size:.7rem;color:var(--ca-text-3);font-family:monospace;margin-top:.1rem}
.trf-item__right{text-align:right;flex-shrink:0}
.trf-item__amt{font-family:'Outfit',sans-serif;font-size:.9375rem;font-weight:800}
.trf-item__amt--neg{color:var(--ca-negative)}
.trf-item__amt--pos{color:var(--ca-positive)}
.trf-item__amt--muted{color:var(--ca-text-3)}
.trf-item__date{font-size:.67rem;color:var(--ca-text-3);margin-top:.15rem}

.trf-item__body .ca-badge{margin-top:.2rem;text-transform:uppercase}

/* ── Empty ── */
.trf-empty{text-align:center;padding:2.5rem 1rem}
.trf-empty__ico{font-size:2.25rem;opacity:.25;margin-bottom:.75rem}
.trf-empty__title{font-size:.875rem;font-weight:600;color:var(--ca-text-2)}
</style>
@endpush

@php $currency = $user->currency ?? \App\Models\Currency::default(); @endphp

{{-- Balance hero ── --}}
<div class="trf-hero">
  <div class="trf-hero__lbl">{{ __('app.balance') }}</div>
  <div>
    <span class="trf-hero__bal">{{ number_format((float)$user->balance, 2, ',', ' ') }}</span>
    <span class="trf-hero__cur">{{ $currency }}</span>
  </div>
  <div class="trf-hero__sub">{{ __('app.available') }}</div>
</div>

{{-- Alerte solde négatif ── --}}
@if((float)$user->balance < 0)
<div style="margin:.875rem 1.25rem 0;padding:.875rem 1rem;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.25);border-left:3px solid #f87171;border-radius:0 14px 14px 0;display:flex;align-items:flex-start;gap:.625rem">
  <i class="fas fa-circle-exclamation" style="color:#f87171;margin-top:.1rem;flex-shrink:0"></i>
  <div>
    <div style="font-size:.8rem;font-weight:700;color:#f87171;margin-bottom:.2rem">Envoi bloqué — Solde négatif</div>
    <div style="font-size:.73rem;color:var(--ca-text-3);line-height:1.5">Votre solde est négatif ({{ number_format((float)$user->balance,2,',',' ') }} {{ $user->currency ?? \App\Models\Currency::default() }}). Les virements sont désactivés jusqu'à la régularisation de votre compte.</div>
  </div>
</div>
@endif

@if($errors->has('blocked'))
<div style="margin:.5rem 1.25rem 0;padding:.75rem 1rem;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.25);border-radius:12px;font-size:.78rem;color:#f87171">
  <i class="fas fa-ban"></i> {{ $errors->first('blocked') }}
</div>
@endif

{{-- Actions ── --}}
<div class="trf-actions">
  @if((float)$user->balance < 0)
  <div class="trf-action trf-action--send" style="opacity:.4;pointer-events:none;cursor:not-allowed">
    <div class="trf-action__ico"><i class="fas fa-lock"></i></div>
    <div class="trf-action__name">{{ __('app.action_send') }}</div>
    <div class="trf-action__desc">Solde négatif</div>
  </div>
  @else
  <a href="{{ route('client.app.transfer.send') }}" class="trf-action trf-action--send">
    <div class="trf-action__ico"><i class="fas fa-paper-plane"></i></div>
    <div class="trf-action__name">{{ __('app.action_send') }}</div>
    <div class="trf-action__desc">{{ __('app.send_subtitle') }}</div>
  </a>
  @endif
  <a href="{{ route('client.app.transfer.receive') }}" class="trf-action trf-action--recv">
    <div class="trf-action__ico"><i class="fas fa-arrow-down"></i></div>
    <div class="trf-action__name">{{ __('app.action_receive') }}</div>
    <div class="trf-action__desc">{{ __('app.receive_subtitle_short') }}</div>
  </a>
</div>

{{-- Recent transfers ── --}}
@if($transfers->isNotEmpty())
<div class="trf-section">
  <span class="trf-section__title">Virements récents</span>
  <a href="{{ route('client.app.movements') }}" class="trf-section__link">
    Voir tout <i class="fas fa-chevron-right" style="font-size:.6rem"></i>
  </a>
</div>

<div class="trf-list">
  @foreach($transfers as $t)
  @php
    $isPending = $t->status === \App\Models\Transfer::STATUS_PENDING;
    $isFee     = $t->status === \App\Models\Transfer::STATUS_FEE_REQUIRED;
    $isDone    = $t->status === \App\Models\Transfer::STATUS_COMPLETED;
    $isRej     = $t->status === \App\Models\Transfer::STATUS_REJECTED;

    $icoClass  = $isPending ? 'pending' : ($isFee ? 'fee' : ($isDone ? 'done' : ($isRej ? 'rej' : 'send')));
    $amtClass  = $isRej ? 'muted' : 'neg';
    $icon      = $isPending ? 'hourglass-half' : ($isFee ? 'file-invoice' : ($isDone ? 'check' : ($isRej ? 'xmark' : 'paper-plane')));
  @endphp
  <div class="trf-item">
    <div class="trf-item__ico trf-item__ico--{{ $icoClass }}">
      <i class="fas fa-{{ $icon }}"></i>
    </div>
    <div class="trf-item__body">
      <div class="trf-item__name">{{ $t->beneficiary_name ?? '—' }}</div>
      <div class="trf-item__ref">{{ $t->reference }}</div>
      @if($isPending)
        <x-status-badge domain="movement" status="pending" label="En attente de validation" />
      @elseif($isFee)
        <x-status-badge domain="movement" status="fee_required" label="Frais requis" />
      @elseif($isDone)
        <x-status-badge domain="movement" status="completed" label="Validé" />
      @elseif($isRej)
        <x-status-badge domain="movement" status="rejected" label="Rejeté" />
      @endif
    </div>
    <div class="trf-item__right">
      <div class="trf-item__amt trf-item__amt--{{ $amtClass }}"
           style="{{ $isRej ? 'text-decoration:line-through' : '' }}">
        -{{ number_format($t->amount, 2, ',', ' ') }}
      </div>
      <div class="trf-item__date">{{ $t->created_at->format('d/m · H:i') }}</div>
    </div>
  </div>
  @endforeach
</div>

@else
<div class="trf-empty">
  <div class="trf-empty__ico"><i class="fas fa-right-left"></i></div>
  <div class="trf-empty__title">{{ __('app.no_activity') }}</div>
</div>
@endif

<div style="height:1.5rem"></div>
@endsection
