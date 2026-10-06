<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="{{ site_name() }} Admin">
<meta name="theme-color" content="#C6A15B">
<link rel="manifest" href="/admin-manifest.json">
<link rel="apple-touch-icon" sizes="180x180" href="/site-icon-180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/site-icon-192.png">
<title>{{ __('auth.staff_login_title') }} | {{ site_name() }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Fraunces:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
:root{
  --navy:#0E3B2E;--nm:#14503D;--nl:#9A7736;
  --accent:#C6A15B;--gd:#9A7736;--gp:#F5EDDD;
}
html,body{height:100%;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column}

/* ════ LEFT PANEL — staff variant ════ */
.auth-left{
  /* Fond bleu profond : accent eclairci pour rester lisible (AA). */
  --accent:#DCBE87;--gd:#2B94F7;
  background:linear-gradient(160deg,#060f1e 0%,#0E3B2E 45%,#0e2240 100%);
  min-height:100vh; padding:2.5rem 3rem;
  display:flex;flex-direction:column;justify-content:space-between;
  position:relative;overflow:hidden;
}
.auth-left::before{
  content:'';position:absolute;inset:0;pointer-events:none;
  background-image:
    linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
  background-size:40px 40px;
}
.auth-left::after{
  content:'';position:absolute;top:-100px;right:-100px;
  width:380px;height:380px;border-radius:50%;
  background:radial-gradient(circle,rgba(198,161,91,.08) 0%,transparent 70%);
  pointer-events:none;
}
.auth-left__logo img{height:40px;position:relative;z-index:1}
.auth-left__body{position:relative;z-index:1}

.staff-badge{
  display:inline-flex;align-items:center;gap:.625rem;
  background:rgba(198,161,91,.08);border:1px solid rgba(198,161,91,.2);
  border-radius:12px;padding:.625rem 1rem;margin-bottom:1.75rem;
}
.staff-badge__ico{
  width:32px;height:32px;border-radius:8px;
  background:rgba(198,161,91,.12);display:flex;align-items:center;justify-content:center;
}
.staff-badge__ico i{color:var(--accent);font-size:.75rem}
.staff-badge__text{line-height:1.3}
.staff-badge__label{font-size:.72rem;font-weight:700;color:var(--accent);letter-spacing:.05em;text-transform:uppercase}
.staff-badge__sub{font-size:.68rem;color:rgba(255,255,255,.35)}

.auth-left__title{
  font-family:'Fraunces',serif;font-size:2.25rem;font-weight:800;
  color:#fff;line-height:1.2;margin-bottom:.875rem;
}
.auth-left__title span{color:var(--accent)}
.auth-left__sub{font-size:.875rem;color:rgba(255,255,255,.4);line-height:1.8;margin-bottom:2rem;max-width:340px}

.staff-alert{
  display:flex;align-items:flex-start;gap:.625rem;
  background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.15);
  border-left:3px solid rgba(239,68,68,.5);
  border-radius:10px;padding:.75rem 1rem;
}
.staff-alert i{color:#f87171;font-size:.7rem;margin-top:.15rem;flex-shrink:0}
.staff-alert__txt{font-size:.72rem;color:rgba(255,255,255,.45);line-height:1.7}

.auth-left__copy{font-size:.7rem;color:rgba(255,255,255,.2);position:relative;z-index:1}
.auth-left__copy a{color:rgba(255,255,255,.3);text-decoration:none}
.auth-left__copy a:hover{color:rgba(255,255,255,.55)}

/* ════ RIGHT PANEL ════ */
.auth-right{
  background:#fff;display:flex;flex-direction:column;min-height:100vh;
}
.auth-topbar{
  display:flex;align-items:center;justify-content:space-between;
  padding:1.25rem 2rem;border-bottom:1px solid #f0f2f5;flex-shrink:0;
}
.auth-topbar__back{
  display:inline-flex;align-items:center;gap:.45rem;
  font-size:.8rem;color:#6b7280;text-decoration:none;font-weight:500;transition:color .18s;
}
.auth-topbar__back:hover{color:var(--navy)}
.auth-topbar__logo img{height:34px}

.ls{position:relative}
.ls__btn{
  display:flex;align-items:center;gap:.5rem;cursor:pointer;
  background:#f8f9fb;border:1.5px solid #e8eaf0;border-radius:9px;
  padding:.4rem .85rem;font-size:.8rem;font-weight:600;color:var(--navy);
  transition:all .18s;
}
.ls__btn:hover{border-color:var(--accent);background:var(--gp)}
.ls__btn img{width:20px;height:14px;object-fit:cover;border-radius:2px}
.ls__chevron{font-size:.55rem;transition:transform .2s}
.ls__menu{
  position:absolute;right:0;top:calc(100% + .5rem);
  background:#fff;border:1.5px solid #e8eaf0;border-radius:12px;
  box-shadow:0 10px 40px rgba(0,0,0,.12);padding:.375rem;
  min-width:160px;z-index:1000;
}
.ls__opt{
  display:flex;align-items:center;gap:.625rem;
  padding:.5rem .75rem;border-radius:8px;
  font-size:.8rem;font-weight:600;color:#374151;
  text-decoration:none;transition:all .15s;
}
.ls__opt:hover{background:#f3f4f6;color:var(--navy)}
.ls__opt img{width:20px;height:14px;object-fit:cover;border-radius:2px}
.ls__opt.is-cur{background:var(--gp);color:var(--gd)}

.auth-form-wrap{
  flex:1;display:flex;align-items:center;justify-content:center;
  padding:2rem;
}
.auth-form-inner{width:100%;max-width:400px}

.form-eyebrow{
  font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--gd);display:flex;align-items:center;gap:.5rem;margin-bottom:.625rem;
}
.form-eyebrow::before{content:'';width:22px;height:2px;background:var(--accent);border-radius:2px}
.form-title{
  font-family:'Fraunces',serif;font-size:1.875rem;font-weight:800;
  color:var(--navy);line-height:1.15;margin-bottom:.375rem;
}
.form-sub{font-size:.8125rem;color:#6b7280;margin-bottom:1.625rem}

.restricted-notice{
  display:flex;align-items:flex-start;gap:.625rem;
  background:#fff7ed;border:1px solid #fed7aa;border-left:3px solid #f97316;
  border-radius:9px;padding:.7rem .9rem;font-size:.78rem;color:#9a3412;
  margin-bottom:1.125rem;line-height:1.6;
}
.restricted-notice i{font-size:.7rem;margin-top:.15rem;flex-shrink:0;color:#f97316}

.auth-error{
  display:flex;align-items:center;gap:.5rem;
  background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #ef4444;
  border-radius:9px;padding:.7rem .9rem;font-size:.8rem;color:#991b1b;
  margin-bottom:1rem;
}

.f-group{margin-bottom:1.125rem}
.f-label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.375rem}
.f-wrap{position:relative}
.f-icon{
  position:absolute;left:.9rem;top:50%;transform:translateY(-50%);
  color:#9ca3af;font-size:.75rem;pointer-events:none;z-index:2;
}
.f-input{
  width:100%;padding:.7rem .9rem .7rem 2.5rem;
  border:1.5px solid #e5e7eb;border-radius:9px;
  font-size:.875rem;font-family:'Outfit',sans-serif;color:#111827;
  outline:none;transition:border-color .2s,box-shadow .2s;background:#fff;
}
.f-input:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(14,59,46,.08)}
.f-input.is-err{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.08)}
.f-eye{
  position:absolute;right:.875rem;top:50%;transform:translateY(-50%);
  background:none;border:none;color:#9ca3af;cursor:pointer;
  font-size:.75rem;padding:.25rem;display:flex;align-items:center;
}
.f-eye:hover{color:var(--navy)}

.f-options{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.375rem}
.f-check{display:flex;align-items:center;gap:.45rem}
.f-check input{width:14px;height:14px;accent-color:var(--navy);cursor:pointer;flex-shrink:0}
.f-check label{font-size:.78rem;color:#6b7280;cursor:pointer;user-select:none}
.f-forgot{font-size:.78rem;color:var(--navy);font-weight:600;transition:color .15s}
.f-forgot:hover{color:var(--accent)}

.btn-auth{
  width:100%;padding:.8rem;border:none;border-radius:10px;
  font-size:.9rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
  background:var(--navy);color:#fff;
  transition:background .2s,transform .12s;letter-spacing:.01em;
}
.btn-auth:hover{background:#0E3B2E}
.btn-auth:active{transform:scale(.98)}

.auth-footer{
  padding:.875rem 2rem 1.25rem;text-align:center;
  font-size:.72rem;color:#9ca3af;flex-shrink:0;
}
.auth-footer a{color:#6b7280;text-decoration:none;transition:color .15s}
.auth-footer a:hover{color:var(--navy)}

@media (max-width:991.98px){
  .auth-left{display:none!important}
  .auth-right{min-height:100vh}
  .auth-topbar{padding:1rem 1.25rem}
  .auth-topbar__logo{display:block}
  .auth-form-wrap{padding:1.5rem 1.25rem}
}
@media (max-width:575.98px){
  .auth-form-inner{max-width:100%}
  .form-title{font-size:1.5rem}
  .auth-form-wrap{padding:1.25rem 1rem}
  .auth-topbar{padding:.875rem 1rem}
}
</style>
</head>
<body>
<div class="container-fluid p-0" style="min-height:100vh">
<div class="row g-0" style="min-height:100vh">

  {{-- ── LEFT PANEL ── --}}
  <div class="col-lg-5 d-none d-lg-flex">
    <div class="auth-left w-100">

      <div class="auth-left__logo">
        <x-logo variant="full" theme="dark" href="{{ url('/') }}" />
      </div>

      <div class="auth-left__body">
        <div class="staff-badge">
          <div class="staff-badge__ico"><i class="fas fa-shield-alt"></i></div>
          <div class="staff-badge__text">
            <div class="staff-badge__label">{{ __('auth.staff_restricted') }}</div>
            <div class="staff-badge__sub">{{ request()->getHost() }} &mdash; secure access</div>
          </div>
        </div>

        <h2 class="auth-left__title">{!! __('auth.staff_brand_title') !!}</h2>
        <p class="auth-left__sub">{{ __('auth.staff_brand_sub') }}</p>

        <div class="staff-alert">
          <i class="fas fa-exclamation-triangle"></i>
          <div class="staff-alert__txt">{{ __('auth.staff_notice') }}</div>
        </div>
      </div>

      <div class="auth-left__copy">
        &copy; {{ date('Y') }} {{ site_name() }} &nbsp;&middot;&nbsp;
        <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;&middot;&nbsp;
        <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
      </div>
    </div>
  </div>

  {{-- ── RIGHT PANEL ── --}}
  <div class="col-12 col-lg-7">
    <div class="auth-right">

      <div class="auth-topbar">
        <a href="{{ url('/') }}" class="auth-topbar__back">
          <i class="fas fa-arrow-left"></i> {{ __('auth.back_site') }}
        </a>

        <x-logo variant="icon" theme="light" size="sm" href="{{ url('/') }}" class="auth-topbar__logo d-lg-none" />

        @php
          $cur = app()->getLocale();
          $langs = \App\Models\Language::enabledList()->mapWithKeys(fn($l) => [$l->code => [$l->native_name, $l->flag_ext]])->all();
        @endphp
        <div class="ls" x-data="{ open: false }">
          <button class="ls__btn" type="button"
                  @click="open = !open" @click.outside="open = false">
            <img src="{{ asset('images/' . $cur . '.' . $langs[$cur][1]) }}" alt="{{ strtoupper($cur) }}">
            <span>{{ strtoupper($cur) }}</span>
            <i class="fas fa-chevron-down ls__chevron"
               :style="open ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'"></i>
          </button>
          <div class="ls__menu" x-show="open" x-transition style="display:none">
            @foreach($langs as $code => [$label, $ext])
            <a href="{{ route('lang.switch', $code) }}"
               class="ls__opt {{ $cur === $code ? 'is-cur' : '' }}">
              <img src="{{ asset('images/' . $code . '.' . $ext) }}" alt="{{ $code }}">
              {{ $label }}
              @if($cur === $code)
                <i class="fas fa-check ms-auto" style="font-size:.6rem"></i>
              @endif
            </a>
            @endforeach
          </div>
        </div>
      </div>

      <div class="auth-form-wrap">
        <div class="auth-form-inner">

          <div class="form-eyebrow">{{ __('auth.staff_login_title') }}</div>
          <h1 class="form-title">{{ __('auth.submit_staff') }}</h1>
          <p class="form-sub">{{ __('auth.staff_login_sub') }}</p>

          <div class="restricted-notice">
            <i class="fas fa-lock"></i>
            {{ __('auth.staff_restricted') }}
          </div>

          @if($errors->any())
          <div class="auth-error">
            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
            {{ $errors->first() }}
          </div>
          @endif

          <form id="staff-login-form" action="{{ route('staff.login.submit') }}" method="POST" novalidate>
            @csrf

            <div class="f-group">
              <label class="f-label" for="email">{{ __('auth.email_staff') }}</label>
              <div class="f-wrap">
                <i class="fas fa-envelope f-icon"></i>
                <input type="email" id="email" name="email"
                       class="f-input {{ $errors->has('email') ? 'is-err' : '' }}"
                       value="{{ old('email') }}"
                       @php
                           // La partie locale reste traduite ; le domaine suit l'email du site
                           // configure en admin, pour ne pas figer l'ancienne marque.
                           $phLocal  = \Illuminate\Support\Str::before(__('auth.email_ph_staff'), '@');
                           $phDomain = \Illuminate\Support\Str::after(site_email(), '@') ?: request()->getHost();
                           $phStaff  = $phLocal . '@' . $phDomain;
                       @endphp
                       placeholder="{{ $phStaff }}"
                       autocomplete="email" required>
              </div>
            </div>

            <div class="f-group">
              <label class="f-label" for="password">{{ __('auth.password_label') }}</label>
              <div class="f-wrap">
                <i class="fas fa-lock f-icon"></i>
                <input type="password" id="password" name="password"
                       class="f-input {{ $errors->has('password') ? 'is-err' : '' }}"
                       placeholder="••••••••"
                       autocomplete="current-password" required>
                <button type="button" class="f-eye" onclick="tglPwd('password','eye1')">
                  <i class="fas fa-eye" id="eye1"></i>
                </button>
              </div>
            </div>

            <div class="f-options">
              <div class="f-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">{{ __('auth.remember_staff') }}</label>
              </div>
              <a href="{{ route('staff.password.request') }}" class="f-forgot">{{ __('auth.forgot_password') }}</a>
            </div>

            <button type="submit" class="btn-auth">
              <i class="fas fa-unlock-alt"></i>
              {{ __('auth.submit_staff') }}
            </button>
          </form>

        </div>
      </div>

      <div class="auth-footer">
        &copy; {{ date('Y') }} {{ site_name() }} &nbsp;&middot;&nbsp;
        <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;&middot;&nbsp;
        <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
      </div>
    </div>
  </div>

</div>
</div>

<script>
function tglPwd(id, ico) {
  const f = document.getElementById(id), i = document.getElementById(ico);
  f.type = f.type === 'password' ? 'text' : 'password';
  i.classList.toggle('fa-eye'); i.classList.toggle('fa-eye-slash');
}

/* Si la page reste ouverte trop longtemps, la session (et le token CSRF) expire
   côté serveur — un submit sur une page périmée échoue de façon confuse. On
   recharge la page avant que ça arrive, pour repartir sur un token frais. */
(function () {
  var PAGE_LOADED_AT = Date.now();
  var STALE_MS = 100 * 60 * 1000; // marge sous SESSION_LIFETIME (120 min)
  var isStale = function () { return Date.now() - PAGE_LOADED_AT > STALE_MS; };

  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible' && isStale()) {
      window.location.reload();
    }
  });

  var form = document.getElementById('staff-login-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      if (isStale()) {
        e.preventDefault();
        window.location.reload();
      }
    });
  }
})();
</script>

{{-- ══ PWA Install ══ --}}

{{-- Bannière Android / Chrome --}}
<div id="pwa-banner" style="display:none;position:fixed;bottom:1rem;left:50%;transform:translateX(-50%);
  width:calc(100% - 2rem);max-width:400px;
  background:var(--navy);border:1px solid rgba(198,161,91,.35);
  border-radius:14px;padding:.875rem 1.125rem;
  box-shadow:0 8px 32px rgba(0,0,0,.3);z-index:9999;
  align-items:center;gap:.75rem">
  <img src="/site-icon-192.png" style="width:40px;height:40px;border-radius:10px;flex-shrink:0" alt="">
  <div style="flex:1;min-width:0">
    <div style="font-size:.825rem;font-weight:700;color:#fff">{{ site_name() }} Admin</div>
    <div style="font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.1rem">Installer sur votre écran d'accueil</div>
  </div>
  <button id="pwa-install-trigger"
    style="background:var(--accent);color:#FFFFFF;border:none;border-radius:8px;
      padding:.45rem .875rem;font-size:.78rem;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0">
    <i class="fas fa-download"></i> Installer
  </button>
  <button onclick="document.getElementById('pwa-banner').style.display='none';localStorage.setItem('cxa_admin_pwa_dismissed','1')"
    style="background:rgba(255,255,255,.1);border:none;color:#fff;width:28px;height:28px;border-radius:6px;cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:.9rem">
    &times;
  </button>
</div>

{{-- Bannière iOS Safari --}}
<div id="pwa-ios" style="display:none;position:fixed;bottom:1rem;left:50%;transform:translateX(-50%);
  width:calc(100% - 2rem);max-width:400px;
  background:var(--navy);border:1px solid rgba(198,161,91,.35);
  border-radius:14px;padding:1rem 1.125rem;
  box-shadow:0 8px 32px rgba(0,0,0,.3);z-index:9999;flex-direction:column;gap:.75rem">
  <div style="display:flex;align-items:center;justify-content:space-between">
    <div style="display:flex;align-items:center;gap:.625rem">
      <img src="/site-icon-192.png" style="width:36px;height:36px;border-radius:8px" alt="">
      <div>
        <div style="font-size:.8rem;font-weight:700;color:#fff">{{ site_name() }} Admin</div>
        <div style="font-size:.68rem;color:rgba(255,255,255,.45)">Installer l'application</div>
      </div>
    </div>
    <button onclick="document.getElementById('pwa-ios').style.display='none';localStorage.setItem('cxa_admin_pwa_dismissed','1')"
      style="background:rgba(255,255,255,.1);border:none;color:#fff;width:28px;height:28px;border-radius:6px;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center">
      &times;
    </button>
  </div>
  <div style="font-size:.73rem;color:rgba(255,255,255,.65);line-height:1.9">
    <span style="display:inline-flex;align-items:center;gap:.35rem">
      <b style="background:rgba(198,161,91,.2);color:var(--accent);padding:.05rem .35rem;border-radius:4px;font-size:.68rem">1</b>
      Appuyez sur <strong style="color:#fff">Partager</strong> <i class="fas fa-share-square" style="color:var(--accent)"></i>
    </span><br>
    <span style="display:inline-flex;align-items:center;gap:.35rem">
      <b style="background:rgba(198,161,91,.2);color:var(--accent);padding:.05rem .35rem;border-radius:4px;font-size:.68rem">2</b>
      Puis <strong style="color:#fff">Sur l'écran d'accueil</strong> <i class="fas fa-plus-square" style="color:var(--accent)"></i>
    </span><br>
    <span style="display:inline-flex;align-items:center;gap:.35rem">
      <b style="background:rgba(198,161,91,.2);color:var(--accent);padding:.05rem .35rem;border-radius:4px;font-size:.68rem">3</b>
      Appuyez sur <strong style="color:#fff">Ajouter</strong>
    </span>
  </div>
</div>

<script>
/* ── Service Worker ── */
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(function(){});
}

/* ── Android / Chrome install ── */
var _pwaStaffPrompt = null;
window.addEventListener('beforeinstallprompt', function(e) {
  e.preventDefault();
  _pwaStaffPrompt = e;
  if (!localStorage.getItem('cxa_admin_pwa_dismissed') && !localStorage.getItem('cxa_admin_pwa_installed')) {
    document.getElementById('pwa-banner').style.display = 'flex';
  }
});
window.addEventListener('appinstalled', function() {
  localStorage.setItem('cxa_admin_pwa_installed', '1');
  document.getElementById('pwa-banner').style.display = 'none';
});
document.getElementById('pwa-install-trigger').addEventListener('click', function() {
  if (!_pwaStaffPrompt) return;
  _pwaStaffPrompt.prompt();
  _pwaStaffPrompt.userChoice.then(function(r) {
    if (r.outcome === 'accepted') {
      localStorage.setItem('cxa_admin_pwa_installed', '1');
      document.getElementById('pwa-banner').style.display = 'none';
    }
    _pwaStaffPrompt = null;
  });
});

/* ── iOS Safari ── */
(function() {
  var isIos    = /iphone|ipad|ipod/i.test(navigator.userAgent);
  var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
  var standalone = window.navigator.standalone === true;
  if (isIos && isSafari && !standalone
      && !localStorage.getItem('cxa_admin_pwa_dismissed')
      && !localStorage.getItem('cxa_admin_pwa_installed')) {
    document.getElementById('pwa-ios').style.display = 'flex';
  }
})();
</script>
</body>
</html>
