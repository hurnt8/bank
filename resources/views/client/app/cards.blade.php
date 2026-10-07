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
.cd-card--blocked { filter: grayscale(.85); opacity: .8; }
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
.cd-note { font-size: .76rem; color: var(--ca-negative); margin-top: .5rem; max-width: 440px; }

.cd-empty { text-align: center; padding: 3rem 1rem; }
.cd-empty__ico { width: 76px; height: 76px; margin: 0 auto 1rem; border-radius: 50%; background: var(--ca-bg3); border: 1px solid var(--ca-border);
  display: flex; align-items: center; justify-content: center; font-size: 1.9rem; color: var(--ca-text-3); }
.cd-empty__t { font-size: 1rem; font-weight: 700; margin-bottom: .4rem; }
.cd-empty__s { font-size: .82rem; color: var(--ca-text-3); line-height: 1.6; max-width: 360px; margin: 0 auto 1.4rem; }
.cd-empty__btn { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 1.4rem; border-radius: 999px; font-size: .85rem; font-weight: 700;
  color: #fff; background: linear-gradient(135deg, #DCBE87, #C6A15B); }
</style>
@endpush

@section('content')
<div class="cd-page">
  @if($cards->isNotEmpty())
  <p class="cd-sub">{{ __('cards.subtitle') }}</p>

  <div class="cd-grid">
    @foreach($cards as $card)
    @php
      $blocked = $card->status === \App\Models\Card::STATUS_BLOCKED;
      $net     = strtolower($card->network);
    @endphp
    <div x-data="{ shown: false }">
      <div class="cd-card {{ $blocked ? 'cd-card--blocked' : '' }}">
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
        <span class="cd-badge {{ $blocked ? 'cd-badge--off' : 'cd-badge--ok' }}">
          {{ $blocked ? __('cards.status_blocked') : __('cards.status_active') }}
        </span>
      </div>
      @if($blocked)
      <div class="cd-note"><i class="fas fa-circle-exclamation"></i> {{ __('cards.blocked_notice') }}</div>
      @endif
    </div>
    @endforeach
  </div>

  @else
  <div class="cd-empty">
    <div class="cd-empty__ico"><i class="fas fa-credit-card"></i></div>
    <div class="cd-empty__t">{{ __('cards.empty_title') }}</div>
    <p class="cd-empty__s">{{ __('cards.empty_text') }}</p>
    <a href="{{ route('client.app.support') }}" class="cd-empty__btn"><i class="fas fa-headset"></i> {{ __('cards.contact') }}</a>
  </div>
  @endif
</div>
@endsection
