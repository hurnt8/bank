@extends('layouts.client-app')
@section('title', __('app.send_title') . ' — ' . site_name())
@section('page_title', __('app.send_title'))
@section('back_btn', true)
@section('back_url', route('client.app.transfers'))

@section('content')

@push('styles')
<style>
/* ── Balance strip ── */
.send-balance{
  display:flex;align-items:center;justify-content:space-between;
  margin:.75rem 1.25rem .5rem;
  padding:.75rem 1.125rem;
  background:rgba(27,138,122,.09);
  border:1px solid rgba(27,138,122,.22);
  border-radius:14px;
}
.send-balance__lbl{font-size:.7rem;color:var(--ca-text-3);font-weight:600}
.send-balance__val{font-family:'Space Grotesk',sans-serif;font-size:1rem;font-weight:800;color:var(--ca-teal-l)}

/* ── Info banner (pending notice) ── */
.send-notice{
  margin:0 1.25rem .75rem;
  padding:.75rem 1rem;
  background:rgba(245,158,11,.06);
  border:1px solid rgba(245,158,11,.2);
  border-left:3px solid #f59e0b;
  border-radius:0 12px 12px 0;
  display:flex;align-items:flex-start;gap:.5rem;
}
.send-notice i{color:#f59e0b;font-size:.85rem;margin-top:.1rem;flex-shrink:0}
.send-notice-text{font-size:.75rem;color:var(--ca-text-3);line-height:1.5}

/* ── Overlay de traitement (entre le clic "Envoyer" et la redirection) ── */
.send-overlay{
  position:fixed;inset:0;z-index:9998;
  background:rgba(14,59,46,.92);
  backdrop-filter:blur(2px);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:1rem;padding:2rem;text-align:center;
}
.send-overlay__spinner{
  width:48px;height:48px;border-radius:50%;
  border:3px solid rgba(255,255,255,.2);
  border-top-color:var(--ca-teal-l);
  animation:send-overlay-spin .8s linear infinite;
}
@keyframes send-overlay-spin{to{transform:rotate(360deg)}}
.send-overlay__title{color:#fff;font-weight:700;font-size:1rem}
.send-overlay__hint{color:rgba(255,255,255,.65);font-size:.8rem;max-width:280px;line-height:1.5}
[x-cloak]{display:none !important}
</style>
@endpush

@php $balance = (float) $user->balance; @endphp

<div x-data="keypad('')" style="display:flex;flex-direction:column;min-height:calc(100dvh - 130px)">

  {{-- Balance disponible ── --}}
  <div class="send-balance">
    <span class="send-balance__lbl">{{ __('app.available') }}</span>
    <span class="send-balance__val">{{ number_format($balance, 2, ',', ' ') }} {{ $user->currency ?? \App\Models\Currency::default() }}</span>
  </div>

  {{-- Notice en attente ── --}}
  <div class="send-notice">
    <i class="fas fa-hourglass-half"></i>
    <span class="send-notice-text">Votre virement sera soumis pour validation. Vous serez notifié dès qu'il sera traité.</span>
  </div>

  {{-- Formulaire ── --}}
  <form method="POST" action="{{ route('client.app.transfer.send.process') }}" id="sendForm"
        @submit="submitting = true">
    @csrf

    <div class="ca-form" style="margin-top:.25rem">
      @error('amount')
      <div class="ca-flash ca-flash--err" style="margin:0 0 .75rem">
        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
      </div>
      @enderror

      <div class="ca-form-group">
        <label class="ca-form-label">{{ __('app.send_name') }}</label>
        <input type="text" name="beneficiary_name" class="ca-form-input"
               placeholder="Jean Dupont" value="{{ old('beneficiary_name') }}" required>
      </div>
      <div class="ca-form-group">
        <label class="ca-form-label">{{ __('app.send_iban') }}</label>
        <input type="text" name="beneficiary_iban" class="ca-form-input"
               placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX"
               value="{{ old('beneficiary_iban') }}" required>
      </div>
    </div>

    {{-- Montant display ── --}}
    <div class="ca-amount-display">
      <div class="ca-amount-display__val">
        <sup>{{ $user->currency ?? \App\Models\Currency::default() }}</sup>
        <span x-text="display">0</span>
      </div>
      <div class="ca-amount-display__available"
           :style="numericValue > {{ $balance }} && numericValue > 0 ? 'color:var(--ca-negative)' : ''">
        <span x-show="numericValue > 0 && numericValue <= {{ $balance }}">
          Solde restant : <strong x-text="fmt({{ $balance }} - numericValue)"></strong>
        </span>
        <span x-show="numericValue > {{ $balance }} && numericValue > 0" style="color:var(--ca-negative)">
          <i class="fas fa-exclamation-triangle" style="font-size:.7rem"></i> Solde insuffisant
        </span>
        <span x-show="numericValue <= 0">
          {{ __('app.available') }} :
          <strong>{{ number_format($balance, 2, ',', ' ') }} {{ $user->currency ?? \App\Models\Currency::default() }}</strong>
        </span>
      </div>
    </div>

    {{-- Note ── --}}
    <textarea name="note" class="ca-note-field" rows="1"
              placeholder="{{ __('app.send_note') }}">{{ old('note') }}</textarea>

    <input type="hidden" name="amount" :value="numericValue">

    {{-- Clavier ── --}}
    <div class="ca-keypad">
      @foreach(['1','2','3','4','5','6','7','8','9','.','0','del'] as $k)
      @if($k === 'del')
        <button type="button" class="ca-key ca-key--del" @click="press('del')">
          <i class="fas fa-delete-left"></i>
        </button>
      @else
        <button type="button" class="ca-key" @click="press('{{ $k }}')">{{ $k }}</button>
      @endif
      @endforeach
    </div>

    {{-- Bouton envoyer ── --}}
    <div class="ca-btn-wrap">
      <button type="submit" class="ca-btn ca-btn--accent"
              :disabled="submitting || numericValue <= 0 || numericValue > {{ $balance }}"
              :style="(submitting || numericValue <= 0 || numericValue > {{ $balance }}) ? 'opacity:.45;pointer-events:none' : ''">
        <template x-if="!submitting">
          <span>
            <i class="fas fa-paper-plane"></i>
            {{ __('app.send_btn') }}
            <span x-show="numericValue > 0 && numericValue <= {{ $balance }}">
              — <span x-text="display"></span> {{ $user->currency ?? \App\Models\Currency::default() }}
            </span>
          </span>
        </template>
        <template x-if="submitting">
          <span>
            <i class="fas fa-spinner fa-spin"></i>
            {{ __('app.send_processing') }}
          </span>
        </template>
      </button>
    </div>

  </form>

  {{-- Overlay de traitement — reste affiché jusqu'à la redirection serveur --}}
  <div class="send-overlay" x-show="submitting" x-cloak x-transition.opacity>
    <div class="send-overlay__spinner"></div>
    <div class="send-overlay__title">{{ __('app.send_processing') }}</div>
    <div class="send-overlay__hint">{{ __('app.send_processing_hint') }}</div>
  </div>
</div>

@push('scripts')
<script>
// Helper format number (needed for restant calculation display)
function fmt(n) {
  return new Intl.NumberFormat('fr-FR', {minimumFractionDigits:2,maximumFractionDigits:2}).format(n || 0);
}
// Patch Alpine component to expose fmt
document.addEventListener('alpine:init', () => {
  Alpine.data('keypad', (init) => ({
    raw: init || '',
    submitting: false,
    get numericValue() { return parseFloat(this.raw) || 0; },
    get display() { return this.raw || '0'; },
    fmt(n) { return fmt(n); },
    press(k) {
      if (k === 'del') { this.raw = this.raw.slice(0, -1); return; }
      if (k === '.' && this.raw.includes('.')) return;
      if (k === '.' && this.raw === '') { this.raw = '0.'; return; }
      if (this.raw === '0' && k !== '.') { this.raw = k; return; }
      const parts = this.raw.split('.');
      if (parts[1] !== undefined && parts[1].length >= 2) return;
      this.raw += k;
    }
  }));
});
</script>
@endpush

@endsection
