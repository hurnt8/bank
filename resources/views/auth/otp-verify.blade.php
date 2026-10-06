<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="{{ site_name() }}">
<meta name="theme-color" content="#C6A15B">
<link rel="manifest" href="{{ route('pwa.manifest') }}">
<link rel="apple-touch-icon" sizes="180x180" href="/site-icon-180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/site-icon-192.png">
<title>{{ __('auth.otp_title') }} — {{ site_name() }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root{
  --bg:   #02182E;
  --card: #05243F;
  --inp:  #06304F;
  --navy: #0E3B2E;
  --navy2:#14503D;
  --accent: #DCBE87;
  --accent-2:#2B94F7;
  --text: #FFFFFF;
  --sub:  rgba(255,255,255,.52);
  --muted:rgba(255,255,255,.28);
  --bdr:  rgba(220,190,135,.16);
  /* Compat: le reste de la feuille référence encore --cyan/--cyan2 pour ses accents ponctuels */
  --cyan: #2B94F7;
  --cyan2:#DCBE87;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{
  height:100%;background:var(--bg);color:var(--text);
  font-family:'Outfit',system-ui,sans-serif;font-size:15px;
  -webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;
  /* Empêche le resize du viewport quand le clavier natif s'ouvre */
  overflow:hidden;
}

/* ── Loading overlay ── */
#ld{
  position:fixed;inset:0;z-index:9999;background:var(--bg);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  opacity:0;pointer-events:none;transition:opacity .35s ease;
}
#ld.on{opacity:1;pointer-events:all}
.ld-bar{
  position:absolute;top:0;left:0;width:0;height:3px;
  background:linear-gradient(90deg,var(--accent),var(--accent-2),var(--accent));
  background-size:200% 100%;border-radius:0 3px 3px 0;
}
#ld.on .ld-bar{animation:ldbar 1.8s cubic-bezier(.4,0,.2,1) forwards}
@keyframes ldbar{0%{width:0}40%{width:60%}100%{width:92%}}
.ld-logo{
  width:80px;height:80px;border-radius:24px;
  background:linear-gradient(135deg,var(--navy2),var(--navy));
  display:flex;align-items:center;justify-content:center;
  margin-bottom:1.5rem;position:relative;
  box-shadow:0 0 40px rgba(14,59,46,.25);
  overflow:hidden;
}
.ld-logo img{width:auto;height:auto !important;max-width:220px;max-height:80px;object-fit:contain;display:block}
.ld-ring{
  position:absolute;inset:-8px;border-radius:32px;
  border:2px solid rgba(14,59,46,.15);border-top-color:var(--accent);
  animation:spin .9s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
.ld-lbl{font-size:.8rem;font-weight:600;color:var(--sub);letter-spacing:.06em;margin-bottom:.875rem}
.ld-dots{display:flex;gap:.4rem}
.ld-dot{
  width:6px;height:6px;border-radius:50%;background:var(--accent);
  animation:ldp 1.2s ease-in-out infinite;
}
.ld-dot:nth-child(2){animation-delay:.18s}
.ld-dot:nth-child(3){animation-delay:.36s}
@keyframes ldp{0%,80%,100%{transform:scale(.6);opacity:.4}40%{transform:scale(1);opacity:1}}

/* ── Background orbs ── */
.bg-orbs{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.orb{position:absolute;border-radius:50%;filter:blur(90px)}
.orb-1{
  width:480px;height:480px;top:-10%;right:-8%;
  background:radial-gradient(circle,rgba(220,190,135,.09) 0%,transparent 65%);
  animation:orbf 10s ease-in-out infinite alternate;
}
.orb-2{
  width:360px;height:360px;bottom:-15%;left:-8%;
  background:radial-gradient(circle,rgba(14,59,46,.05) 0%,transparent 65%);
  animation:orbf 14s ease-in-out infinite alternate-reverse;
}
@keyframes orbf{from{transform:scale(1)}to{transform:scale(1.1) translate(2%,3%)}}

/* ── Full-screen layout (PWA-style) ── */
.page-shell{
  position:fixed;inset:0;
  display:flex;flex-direction:column;
  padding-top:env(safe-area-inset-top,0px);
  padding-bottom:env(safe-area-inset-bottom,0px);
  z-index:1;
}

/* ── Top bar ── */
.topbar{
  display:flex;align-items:center;justify-content:space-between;
  padding:.75rem 1.25rem;
  flex-shrink:0;
}
.topbar__back{
  display:inline-flex;align-items:center;gap:.45rem;
  font-size:.78rem;font-weight:500;color:var(--sub);
  background:var(--inp);border:1.5px solid var(--bdr);
  border-radius:999px;padding:.4rem .85rem;
  text-decoration:none;
  transition:color .18s,background .18s;
  -webkit-tap-highlight-color:transparent;
}
.topbar__back:hover{color:var(--navy);background:#F5EDDD}
.topbar__back i{font-size:.65rem}
.topbar__logo img{height:26px;object-fit:contain}

/* ── Scrollable center zone ── */
.page-content{
  flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;
  display:flex;flex-direction:column;align-items:center;
  /* espace pour le clavier fixé (4×68px + 3×gap + padding ≈ 330px) */
  padding:1rem 1.25rem calc(330px + env(safe-area-inset-bottom,0px));
  scrollbar-width:none;
}
.page-content::-webkit-scrollbar{display:none}

/* ── Card / heading ── */
.card{width:100%;max-width:400px;text-align:center}

/* Shield icon */
.otp-icon{
  width:76px;height:76px;border-radius:22px;
  background:linear-gradient(135deg,rgba(220,190,135,.16),rgba(150,112,47,.16));
  border:1.5px solid rgba(220,190,135,.3);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.25rem;
  position:relative;
}
.otp-icon i{font-size:2rem;color:var(--accent-2)}
.otp-icon::after{
  content:'';
  position:absolute;inset:-6px;border-radius:28px;
  border:1px solid rgba(220,190,135,.15);
  animation:pulse-ring 2.5s ease-in-out infinite;
}
@keyframes pulse-ring{
  0%,100%{opacity:.4;transform:scale(1)}
  50%{opacity:.12;transform:scale(1.04)}
}

.card-title{
  font-family:'Space Grotesk',sans-serif;
  font-size:1.55rem;font-weight:800;color:var(--text);margin-bottom:.35rem;
}
.card-sub{font-size:.82rem;color:var(--sub);line-height:1.6;margin-bottom:1.5rem}
.card-sub strong{color:var(--cyan);font-weight:600}

/* ── Error ── */
.oerr{
  display:flex;align-items:flex-start;gap:.55rem;
  background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);
  border-left:3px solid #ef4444;border-radius:10px;
  padding:.65rem .875rem;font-size:.79rem;color:#991B1B;
  margin-bottom:1rem;text-align:left;
}
.oerr i{margin-top:.1rem;flex-shrink:0}

/* ── OTP digit circles ── */
.odigits{
  display:flex;gap:.5rem;justify-content:center;
  margin-bottom:1.25rem;
}
.odigit{
  width:52px;height:52px;border-radius:14px;
  background:var(--inp);
  border:2px solid var(--bdr);
  display:flex;align-items:center;justify-content:center;
  font-family:'Space Grotesk',sans-serif;font-size:1.5rem;font-weight:800;
  color:var(--text);
  transition:border-color .2s,background .2s,box-shadow .2s;
  flex-shrink:0;position:relative;
}
.odigit.filled{
  border-color:var(--navy);
  background:rgba(14,59,46,.05);
  color:var(--navy);
}
.odigit.active{
  border-color:var(--navy);
  background:#fff;
  box-shadow:0 0 0 4px rgba(14,59,46,.1),0 0 16px rgba(14,59,46,.08);
}
.odigit.active::after{
  content:'';position:absolute;
  width:2px;height:60%;border-radius:2px;
  background:var(--navy);
  animation:blink .8s step-end infinite;
}
@keyframes blink{0%,100%{opacity:1}50%{opacity:0}}
.odigit.shake{animation:shake .35s ease}
@keyframes shake{
  0%,100%{transform:translateX(0)}
  20%{transform:translateX(-5px)}
  40%{transform:translateX(5px)}
  60%{transform:translateX(-4px)}
  80%{transform:translateX(4px)}
}
.odigit.err{
  border-color:#ef4444;
  background:rgba(239,68,68,.1);
  color:#dc2626;
}
@media(max-width:360px){
  .odigit{width:44px;height:44px;font-size:1.25rem;border-radius:12px}
  .odigits{gap:.35rem}
}

/* Verify button */
.obtn{
  width:100%;max-width:400px;
  padding:.88rem 1.5rem;border:none;border-radius:999px;
  font-size:.97rem;font-weight:700;font-family:'Outfit',sans-serif;
  cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.625rem;
  background:linear-gradient(135deg,var(--navy2) 0%,var(--navy) 100%);
  color:#fff;letter-spacing:.01em;
  box-shadow:0 6px 28px rgba(14,59,46,.28),0 2px 8px rgba(14,59,46,.15);
  transition:filter .2s,box-shadow .2s,transform .1s;
  margin-bottom:.5rem;
}
.obtn:hover{filter:brightness(1.2)}
.obtn:active{transform:scale(.975)}
.obtn:disabled{opacity:.45;cursor:not-allowed;filter:none}

.btn-spinner{
  display:inline-block;width:18px;height:18px;border-radius:50%;
  border:2.5px solid rgba(255,255,255,.35);border-top-color:#fff;
  animation:spin .65s linear infinite;
}

/* Timer & resend */
.resend-row{
  font-size:.79rem;color:var(--sub);
  margin-bottom:1rem;min-height:1.8rem;
  display:flex;align-items:center;justify-content:center;gap:.35rem;
  flex-wrap:wrap;
}
.resend-timer strong{color:var(--cyan);font-weight:700;font-family:'Space Grotesk',sans-serif}
.resend-btn{
  background:none;border:none;cursor:pointer;padding:0;
  font-size:.79rem;font-weight:700;color:var(--cyan);
  transition:opacity .18s;
}
.resend-btn:hover{opacity:.75}
.resend-btn:disabled{opacity:.35;cursor:not-allowed}
.resend-msg{
  display:block;font-size:.74rem;font-weight:600;margin-top:.2rem;
  text-align:center;
}
.resend-msg.ok{color:#059669}.resend-msg.fail{color:#dc2626}

/* ── Numeric keypad (fixé en bas, hors du flux flex) ── */
.keypad-zone{
  position:fixed;
  bottom:0;left:0;right:0;
  z-index:20;
  background:linear-gradient(to bottom,transparent 0,var(--bg) 14px);
  padding:.625rem 1.25rem calc(.875rem + env(safe-area-inset-bottom,0px));
}
.keypad{
  display:grid;grid-template-columns:repeat(3,1fr);
  gap:.5rem;
  max-width:400px;
  margin:0 auto;
}
.kbtn{
  height:68px;border-radius:18px;
  background:var(--inp);
  border:1.5px solid var(--bdr);
  color:var(--text);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  cursor:pointer;gap:.1rem;
  transition:background .1s,border-color .1s,transform .08s;
  -webkit-tap-highlight-color:transparent;
  user-select:none;-webkit-user-select:none;
}
.kbtn:active,.kbtn.pressed{
  transform:scale(.92);
  background:rgba(14,59,46,.08);
  border-color:rgba(14,59,46,.25);
  box-shadow:0 0 14px rgba(14,59,46,.12);
}
.kbtn:disabled{opacity:.3;cursor:not-allowed}
.knum{
  font-family:'Outfit',sans-serif;
  font-size:1.375rem;font-weight:700;line-height:1;
}
.ksub{
  font-size:.4rem;font-weight:600;letter-spacing:.12em;
  color:var(--muted);font-family:'Outfit',sans-serif;
  text-transform:uppercase;
}
.kbtn-del{background:#F1F2F3;border-color:var(--bdr)}
.kbtn-del i{font-size:1.15rem;color:var(--muted)}
.kbtn-del:active,.kbtn-del.pressed{
  background:rgba(220,38,38,.1);border-color:rgba(220,38,38,.25);
}
.kbtn-empty{pointer-events:none;background:none;border:none}

/* Entrance animations */
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.otp-icon  {animation:fadeUp .35s ease .05s both}
.card-title{animation:fadeUp .35s ease .1s  both}
.card-sub  {animation:fadeUp .35s ease .15s both}
.oerr      {animation:fadeUp .35s ease .08s both}
.odigits   {animation:fadeUp .35s ease .18s both}
.resend-row{animation:fadeUp .35s ease .2s  both}
.obtn      {animation:fadeUp .35s ease .22s both}
.keypad-zone{animation:fadeUp .4s ease .28s both}
</style>
</head>
<body>

{{-- Loading overlay --}}
<div id="ld" role="status">
  <div class="ld-bar"></div>
  <div class="ld-logo">
    <x-logo variant="full" theme="dark" size="md" />
    <div class="ld-ring"></div>
  </div>
  <p class="ld-lbl">{{ __('auth.otp_verifying') ?? 'Vérification…' }}</p>
  <div class="ld-dots">
    <div class="ld-dot"></div>
    <div class="ld-dot"></div>
    <div class="ld-dot"></div>
  </div>
</div>

{{-- Background --}}
<div class="bg-orbs" aria-hidden="true">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
</div>

{{-- ══ PAGE SHELL ══ --}}
<div class="page-shell" x-data="otpApp()">

  {{-- Top bar --}}
  <div class="topbar">
    <a href="{{ $backUrl ?? '/login' }}" class="topbar__back">
      <i class="fas fa-chevron-left"></i> {{ __('auth.otp_back') }}
    </a>
    <x-logo variant="full" theme="light" size="sm" href="{{ url('/') }}" class="topbar__logo" />
  </div>

  {{-- Scrollable content --}}
  <div class="page-content">
    <div class="card">

      {{-- Shield icon --}}
      <div class="otp-icon">
        <i class="fas fa-shield-halved"></i>
      </div>

      {{-- Heading --}}
      <h1 class="card-title">{{ __('auth.otp_heading') }}</h1>
      <p class="card-sub">
        {{ __('auth.otp_subtitle') }}<br>
        <strong>{{ $masked }}</strong>
      </p>

      {{-- Error (inline, géré par Alpine) --}}
      <div class="oerr" x-show="errorMsg" x-transition style="display:none">
        <i class="fas fa-circle-exclamation"></i>
        <span x-text="errorMsg"></span>
      </div>

      {{-- OTP form --}}
      <form id="otp-form" action="{{ route('otp.verify') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="code" :value="digits.join('')">

        {{-- Input invisible pour le remplissage automatique SMS (sans déclencher le clavier) --}}
        <input id="otp-real"
               type="text"
               inputmode="none"
               autocomplete="one-time-code"
               maxlength="6"
               tabindex="-1"
               aria-hidden="true"
               style="position:fixed;opacity:0;width:1px;height:1px;pointer-events:none;top:-100px"
               @input="onSmsAutofill($event)">

        {{-- Digit circles --}}
        <div class="odigits" id="odigits-row">
          <template x-for="(d, i) in digits" :key="i">
            <div class="odigit"
                 :class="{filled: d !== '' && !hasErr, active: d === '' && i === activeIndex && !hasErr, err: hasErr, shake: hasErr}">
              <span x-text="d" x-show="d !== ''"></span>
            </div>
          </template>
        </div>

        {{-- Timer & Resend --}}
        <div class="resend-row">
          <span x-show="timeLeft > 0">
            {{ __('auth.otp_resend_in') }}
            <strong x-text="fmtTime()"></strong>
          </span>
          <template x-if="timeLeft <= 0 && !resending">
            <button type="button" class="resend-btn" @click="resend()">
              {{ __('auth.otp_resend') }}
            </button>
          </template>
          <template x-if="resending">
            <span><i class="fas fa-circle-notch fa-spin" style="color:var(--cyan)"></i></span>
          </template>
        </div>
        <span class="resend-msg" :class="resendOk ? 'ok' : 'fail'" x-show="resendMsg" x-text="resendMsg"></span>

        {{-- Verify button --}}
        <button type="submit" class="obtn"
                :disabled="digits.join('').length < 6 || submitting"
                @click.prevent="doSubmit()">
          <template x-if="!submitting">
            <span><i class="fas fa-check" style="margin-right:.4rem"></i>{{ __('auth.otp_verify_btn') }}</span>
          </template>
          <template x-if="submitting">
            <span class="btn-spinner"></span>
          </template>
        </button>
      </form>

    </div>
  </div>


</div>{{-- /.page-shell --}}

{{-- ── Clavier numérique (fixé en bas, hors du flux flex) ── --}}
<div class="keypad-zone" x-data x-ref="kpad">
  <div class="keypad">
    @foreach([['1',''],['2','ABC'],['3','DEF'],['4','GHI'],['5','JKL'],['6','MNO'],['7','PQRS'],['8','TUV'],['9','WXYZ']] as [$n,$s])
    <button type="button" class="kbtn"
            @pointerdown.prevent="$dispatch('otp-press', '{{ $n }}')">
      <span class="knum">{{ $n }}</span>
      @if($s)<span class="ksub">{{ $s }}</span>@endif
    </button>
    @endforeach
    {{-- Ligne 4 : supprimer | 0 | vide --}}
    <button type="button" class="kbtn kbtn-del"
            @pointerdown.prevent="$dispatch('otp-del')">
      <i class="fas fa-delete-left"></i>
    </button>
    <button type="button" class="kbtn"
            @pointerdown.prevent="$dispatch('otp-press', '0')">
      <span class="knum">0</span>
    </button>
    <div class="kbtn kbtn-empty"></div>
  </div>
</div>

<script>
function otpApp() {
  return {
    digits:      ['','','','','',''],
    activeIndex: 0,
    timeLeft:    120,
    timer:       null,
    resending:   false,
    submitting:  false,
    resendMsg:   '',
    resendOk:    true,
    errorMsg:    '',
    hasErr:      false,

    init() {
      this.startTimer();
      document.addEventListener('keydown', (e) => {
        if (this.submitting || this.hasErr) return;
        if (e.key >= '0' && e.key <= '9') { e.preventDefault(); this.press(e.key); }
        if (e.key === 'Backspace')         { e.preventDefault(); this.del(); }
        if (e.key === 'Enter')             { e.preventDefault(); this.doSubmit(); }
      });
      window.addEventListener('otp-press', (e) => { if (!this.submitting && !this.hasErr) this.press(e.detail); });
      window.addEventListener('otp-del',   ()  => { if (!this.submitting && !this.hasErr) this.del(); });
    },

    startTimer() {
      clearInterval(this.timer);
      this.timeLeft = 120;
      this.timer = setInterval(() => { if (this.timeLeft > 0) this.timeLeft--; }, 1000);
    },

    fmtTime() {
      var m = Math.floor(this.timeLeft / 60);
      var s = this.timeLeft % 60;
      return (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
    },

    press(n) {
      if (this.activeIndex >= 6 || this.submitting) return;
      this.digits[this.activeIndex] = n;
      this.digits = [...this.digits];
      this.activeIndex = Math.min(this.activeIndex + 1, 6);
      if (this.activeIndex === 6) {
        this.$nextTick(() => { if (!this.submitting) this.doSubmit(); });
      }
    },

    del() {
      if (this.submitting) return;
      var idx = this.activeIndex - 1;
      if (idx < 0) return;
      this.digits[idx] = '';
      this.digits = [...this.digits];
      this.activeIndex = idx;
    },

    onSmsAutofill(e) {
      var val = (e.target.value || '').replace(/\D/g,'').slice(0,6);
      e.target.value = val;
      for (var i = 0; i < 6; i++) this.digits[i] = val[i] || '';
      this.digits = [...this.digits];
      this.activeIndex = Math.min(val.length, 6);
      if (val.length === 6) this.$nextTick(() => this.doSubmit());
    },

    showError(msg) {
      this.errorMsg   = msg;
      this.hasErr     = true;
      this.submitting = false;
      /* Après l'animation shake (350ms), on vide les cases et on remet le curseur */
      setTimeout(() => {
        this.hasErr     = false;
        this.digits     = ['','','','','',''];
        this.activeIndex = 0;
      }, 500);
    },

    /* Jeton CSRF courant : rafraîchi automatiquement si une requête échoue en
       419 (session expirée pendant que l'onglet restait ouvert), pour éviter
       que l'utilisateur ne voie une erreur "CSRF token mismatch" opaque. */
    csrfToken: '{{ csrf_token() }}',

    async refreshCsrfToken() {
      try {
        var r = await fetch('{{ route("csrf.refresh") }}', { headers: { 'Accept': 'application/json' } });
        var j = await r.json();
        if (j && j.token) this.csrfToken = j.token;
      } catch (e) { /* tant pis, on retentera avec l'ancien token */ }
    },

    /* Exécute un fetch POST JSON avec le token CSRF courant ; si la réponse
       est 419, rafraîchit le token et réessaie une seule fois. */
    async postJson(url, body) {
      var doFetch = () => fetch(url, {
        method:  'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept':       'application/json',
          'X-CSRF-TOKEN': this.csrfToken,
        },
        body: body ? JSON.stringify(body) : undefined,
      });

      var resp = await doFetch();
      if (resp.status === 419) {
        await this.refreshCsrfToken();
        resp = await doFetch();
      }
      return resp;
    },

    async doSubmit() {
      if (this.digits.join('').length < 6 || this.submitting) return;
      this.submitting = true;
      this.errorMsg   = '';
      try {
        var resp = await this.postJson('{{ route("otp.verify") }}', { code: this.digits.join('') });
        var data = await resp.json();

        if (data.status === 'success') {
          document.getElementById('ld').classList.add('on');
          window.location.href = data.url;
          return;
        }
        if (data.status === 'blocked' || data.status === 'redirect') {
          document.getElementById('ld').classList.add('on');
          window.location.href = data.url || '/login';
          return;
        }
        /* Mauvais code */
        this.showError(data.message || '{{ __("auth.otp_invalid", ["remaining" => 1]) }}');
      } catch(err) {
        this.showError('{{ __("auth.otp_send_failed") }}');
      }
    },

    async resend() {
      if (this.resending || this.timeLeft > 0) return;
      this.resending  = true;
      this.resendMsg  = '';
      try {
        var resp = await this.postJson('{{ route("otp.resend") }}');
        var data = await resp.json();
        if (resp.ok) {
          this.resendOk    = true;
          this.resendMsg   = data.message || '{{ __("auth.otp_resend_success") }}';
          this.digits      = ['','','','','',''];
          this.activeIndex = 0;
          this.errorMsg    = '';
          this.startTimer();
        } else {
          this.resendOk  = false;
          this.resendMsg = data.error || '{{ __("auth.otp_send_failed") }}';
        }
      } catch(err) {
        this.resendOk  = false;
        this.resendMsg = '{{ __("auth.otp_send_failed") }}';
      }
      this.resending = false;
      var self = this;
      setTimeout(function(){ self.resendMsg = ''; }, 4000);
    },
  };
}

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(function(){});
}
</script>
</body>
</html>
