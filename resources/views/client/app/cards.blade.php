@extends('layouts.client-app')
@section('title', __('cards.title') . ' — ' . site_name())
@section('page_title', __('cards.title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@push('styles')
<style>
.cd-page { padding: 1.25rem 1.25rem 2rem; }
.cd-sub  { font-size: .82rem; color: var(--ca-text-3); margin-bottom: 1.1rem; }
.cd-grid { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
@media (min-width: 768px) { .cd-grid { grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); } }

/* Carte bancaire */
.cd-card {
  position: relative; overflow: hidden;
  aspect-ratio: 1.586 / 1; min-height: 190px; max-width: 440px; width: 100%;
  border-radius: 22px; padding: 1.35rem 1.5rem;
  background: linear-gradient(135deg, #1B4976 0%, #0D2E52 50%, #0A1E38 100%);
  box-shadow: 0 12px 40px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.08);
  display: flex; flex-direction: column; justify-content: space-between;
  color: #fff;
}
.cd-card::before {
  content: ''; position: absolute; top: -70px; right: -70px; width: 240px; height: 240px; border-radius: 50%;
  background: radial-gradient(circle, rgba(220,190,135,.2) 0%, transparent 65%); pointer-events: none;
}
.cd-card--blocked, .cd-card--suspended { filter: grayscale(.85); opacity: .8; }
.cd-card__top { display: flex; align-items: center; justify-content: space-between; position: relative; }
.cd-card__brand { font-size: .68rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: rgba(255,255,255,.7); }
.cd-card__chip {
  width: 42px; height: 31px; border-radius: 7px;
  background: linear-gradient(135deg, #E9D5A1, #C6A15B 55%, #7BA6D9);
}
.cd-card__num {
  position: relative; font-family: 'Space Grotesk', ui-monospace, monospace;
  font-size: clamp(1.05rem, 4.6vw, 1.45rem); letter-spacing: .12em; font-weight: 600; margin: .35rem 0;
  white-space: nowrap;
}
.cd-card__bottom { display: flex; align-items: flex-end; justify-content: space-between; gap: .75rem; position: relative; }
.cd-card__lbl { font-size: .55rem; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.55); margin-bottom: .15rem; }
.cd-card__val { font-size: .85rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; overflow-wrap: anywhere; }
.cd-card__net { font-size: 2.1rem; line-height: 1; color: rgba(255,255,255,.92); }

.cd-meta { display: flex; align-items: center; justify-content: space-between; gap: .75rem; margin-top: .75rem; max-width: 440px; }
.cd-badge { font-size: .72rem; font-weight: 700; padding: .25rem .7rem; border-radius: 999px; }
.cd-badge--ok  { background: rgba(0,200,150,.14); color: var(--ca-positive); }
.cd-badge--off { background: rgba(255,90,90,.14); color: var(--ca-negative); }
.cd-eye { background: none; border: 1px solid var(--ca-border); color: var(--ca-text-2); border-radius: 999px; padding: .3rem .8rem; font-size: .72rem; cursor: pointer; }
.cd-badge--pause { background: rgba(245,158,11,.14); color: var(--ca-amber); }
.cd-actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; max-width: 440px; }
.cd-btn { display: inline-flex; align-items: center; gap: .45rem; padding: .55rem 1rem; border-radius: 999px; cursor: pointer; font-family: inherit; font-size: .78rem; font-weight: 700;
  border: 1px solid var(--ca-border); background: var(--ca-bg3); color: var(--ca-text); }
.cd-btn:hover { border-color: var(--ca-accent); }
.cd-btn--warn { color: var(--ca-amber); }
.cd-limit { margin-top: .9rem; max-width: 440px; padding: .95rem 1rem; border-radius: 14px; background: var(--ca-bg3); border: 1px solid var(--ca-border); }
.cd-limit__top { display: flex; justify-content: space-between; align-items: baseline; gap: .75rem; margin-bottom: .4rem; }
.cd-limit__t { font-size: .8rem; font-weight: 800; }
.cd-limit__v { font-family: 'Space Grotesk', sans-serif; font-weight: 800; font-size: 1.15rem; }
.cd-limit input[type=range] { width: 100%; accent-color: var(--ca-accent); margin: .35rem 0 .2rem; }
.cd-limit__h { font-size: .72rem; color: var(--ca-text-3); margin-bottom: .6rem; }
.cd-note { font-size: .76rem; color: var(--ca-negative); margin-top: .5rem; max-width: 440px; }

.cd-empty { text-align: center; padding: 3rem 1rem; }
.cd-empty__ico { width: 76px; height: 76px; margin: 0 auto 1rem; border-radius: 50%; background: var(--ca-bg3); border: 1px solid var(--ca-border);
  display: flex; align-items: center; justify-content: center; font-size: 1.9rem; color: var(--ca-text-3); }
.cd-empty__t { font-size: 1rem; font-weight: 700; margin-bottom: .4rem; }
.cd-empty__s { font-size: .82rem; color: var(--ca-text-3); line-height: 1.6; max-width: 360px; margin: 0 auto 1.4rem; }
.cd-ok { margin-bottom: 1rem; padding: .8rem 1rem; border-radius: 12px; background: rgba(0,200,150,.1); border: 1px solid rgba(0,200,150,.3); color: var(--ca-text-2); font-size: .82rem; }
.cd-pending { display: inline-flex; align-items: center; gap: .5rem; padding: .7rem 1.2rem; border-radius: 999px; font-size: .82rem; font-weight: 700; background: rgba(245,158,11,.12); color: var(--ca-amber); }
button.cd-empty__btn { border: 0; cursor: pointer; font-family: inherit; }
.cd-empty__btn { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 1.4rem; border-radius: 999px; font-size: .85rem; font-weight: 700;
  color: #fff; background: linear-gradient(135deg, #DCBE87, #C6A15B); }
</style>
@endpush

@section('content')
<div class="cd-page">
  @if(session('success'))<div class="cd-ok" role="status"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>@endif
  @if($cards->isNotEmpty())
  <p class="cd-sub">{{ __('cards.subtitle') }}</p>

  <div class="cd-grid">
    @foreach($cards as $card)
    @php
      $blocked   = $card->status === \App\Models\Card::STATUS_BLOCKED;
      $suspended = $card->status === \App\Models\Card::STATUS_SUSPENDED;
      $limit     = (int) ($card->spending_limit ?: \App\Models\Card::LIMIT_MIN);
      $net     = strtolower($card->network);
    @endphp
    <div x-data="{ shown: false }">
      <div class="cd-card {{ $blocked ? 'cd-card--blocked' : ($suspended ? 'cd-card--suspended' : '') }}">
        <div class="cd-card__top">
          <div class="cd-card__brand"><i class="fas fa-landmark"></i> {{ site_name() }}</div>
          <div class="cd-card__chip" aria-hidden="true"></div>
        </div>

        <div class="cd-card__num" x-show="shown">{{ $card->maskedNumber() }}</div>
        <div class="cd-card__num" x-show="!shown">&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; {{ $card->last_four }}</div>

        <div class="cd-card__bottom">
          <div style="min-width:0">
            <div class="cd-card__lbl">{{ __('cards.holder') }}</div>
            <div class="cd-card__val">{{ $card->holder_name }}</div>
          </div>
          <div>
            <div class="cd-card__lbl">{{ __('cards.expires') }}</div>
            <div class="cd-card__val">{{ $card->expires_at->format('m/y') }}</div>
          </div>
          <div class="cd-card__net" aria-label="{{ ucfirst($net) }}">
            <i class="fa-brands {{ $net === 'visa' ? 'fa-cc-visa' : 'fa-cc-mastercard' }}"></i>
          </div>
        </div>
      </div>

      <div class="cd-meta">
        <span class="cd-badge {{ $blocked ? 'cd-badge--off' : ($suspended ? 'cd-badge--pause' : 'cd-badge--ok') }}">
          {{ $blocked ? __('cards.status_blocked') : ($suspended ? __('cards.status_suspended') : __('cards.status_active')) }}
        </span>
      </div>
      @if($blocked)
      <div class="cd-note"><i class="fas fa-circle-exclamation"></i> {{ __('cards.blocked_notice') }}</div>
      @elseif($suspended)
      <div class="cd-note" style="color:var(--ca-amber)"><i class="fas fa-pause-circle"></i> {{ __('cards.suspended_notice') }}</div>
      @endif

      @unless($blocked)
      <div class="cd-actions">
        <form method="POST" action="{{ route('client.app.cards.suspend', $card) }}">
          @csrf
          <button type="submit" class="cd-btn {{ $suspended ? '' : 'cd-btn--warn' }}">
            <i class="fas {{ $suspended ? 'fa-play' : 'fa-pause' }}"></i> {{ $suspended ? __('cards.resume_button') : __('cards.suspend_button') }}
          </button>
        </form>
      </div>

      <form method="POST" action="{{ route('client.app.cards.limit', $card) }}" class="cd-limit" x-data="{ v: {{ $limit }} }">
        @csrf
        <div class="cd-limit__top">
          <span class="cd-limit__t"><i class="fas fa-gauge-high"></i> {{ __('cards.limit_title') }}</span>
          <span class="cd-limit__v"><span x-text="new Intl.NumberFormat('fr-FR').format(v)"></span> {{ $user->currency ?? 'EUR' }}</span>
        </div>
        <input type="range" name="spending_limit" min="{{ \App\Models\Card::LIMIT_MIN }}" max="{{ \App\Models\Card::LIMIT_MAX }}" step="{{ \App\Models\Card::LIMIT_STEP }}" x-model.number="v">
        <div class="cd-limit__h">{{ __('cards.limit_hint', ['min' => number_format(\App\Models\Card::LIMIT_MIN, 0, ',', ' '), 'max' => number_format(\App\Models\Card::LIMIT_MAX, 0, ',', ' '), 'currency' => $user->currency ?? 'EUR']) }}</div>
        @error('spending_limit')<div class="cd-note" style="margin:0 0 .5rem">{{ $message }}</div>@enderror
        <button type="submit" class="cd-btn" :disabled="v === {{ $limit }}"><i class="fas fa-floppy-disk"></i> {{ __('cards.limit_save') }}</button>
      </form>
      @endunless
    </div>
    @endforeach
  </div>

  @else
  <div class="cd-empty">
    <div class="cd-empty__ico"><i class="fas fa-credit-card"></i></div>
    <div class="cd-empty__t">{{ __('cards.empty_title') }}</div>
    <p class="cd-empty__s">{{ __('cards.empty_text') }}</p>
    @if($pendingRequest)
      <span class="cd-pending"><i class="fas fa-hourglass-half"></i> {{ __('cards.request_pending') }}</span>
    @else
      <form method="POST" action="{{ route('client.app.cards.request') }}" x-data="{ busy: false }" @submit="busy = true">
        @csrf
        <button type="submit" class="cd-empty__btn" :disabled="busy">
          <i class="fas" :class="busy ? 'fa-spinner fa-spin' : 'fa-credit-card'"></i> {{ __('cards.request_button') }}
        </button>
      </form>
    @endif
  </div>
  @endif
</div>
@endsection
