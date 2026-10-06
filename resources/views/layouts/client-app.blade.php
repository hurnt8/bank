<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="{{ site_name() }}">
  <meta name="theme-color" content="#C6A15B">
  <meta name="description" content="{{ site_name() }} — Espace client mobile">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', site_name())</title>

  <link rel="manifest" href="{{ route('pwa.manifest') }}">
  {{-- Icônes PWA --}}
  <link rel="apple-touch-icon" sizes="180x180" href="/site-icon-180.png">
  <link rel="icon" type="image/png" sizes="512x512" href="/site-icon-512.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/site-icon-192.png">
  {{-- Couvre favicon.ico vide pour les navigateurs/crawlers qui le demandent --}}
  <link rel="shortcut icon" href="/site-icon-192.png" type="image/png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  @vite(['resources/css/client-app.css', 'resources/js/client-app.js'])

  {{-- Init theme AVANT le rendu pour éviter le flash blanc/noir.
       Thème sombre par défaut (le clair fatiguait trop les yeux) ; l'utilisateur
       garde la main via le sélecteur de thème, stocké sous la même clé 'v2'. --}}
  <script>
    (function(){
      var t = localStorage.getItem('solberg-theme-v2') || 'dark';
      document.documentElement.dataset.theme = t;
    })();
  </script>
  <script>window._copiedLabel = '{{ __("app.copied") }}';</script>

  @stack('styles')
</head>
<body x-data>

{{-- ══ SPLASH SCREEN ══ --}}
<div id="cxa-splash" aria-hidden="true">
  <x-logo variant="full" theme="dark" size="lg" id="cxa-splash-logo" />
</div>
<style>
#cxa-splash{
  position:fixed;inset:0;z-index:9999;
  background:#0E3B2E;
  display:flex;align-items:center;justify-content:center;
  animation:splashFade 0.4s ease 1.4s forwards;
  pointer-events:none;
}
#cxa-splash-logo{
  animation:splashLogo 0.55s cubic-bezier(.22,1,.36,1) 0.1s both;
}
@keyframes splashLogo{
  from{opacity:0;transform:scale(.7)}
  to  {opacity:1;transform:scale(1)}
}
@keyframes splashFade{
  to{opacity:0;visibility:hidden}
}
</style>
<script>
(function(){
  /* Ne montrer le splash qu'au lancement PWA standalone ou premier chargement */
  var shown = sessionStorage.getItem('cxa_splash');
  var isStandalone = window.matchMedia('(display-mode: standalone)').matches
                   || window.navigator.standalone === true;
  if (shown && !isStandalone) {
    document.getElementById('cxa-splash').style.display = 'none';
  } else {
    sessionStorage.setItem('cxa_splash', '1');
    setTimeout(function(){
      var s = document.getElementById('cxa-splash');
      if (s) s.remove();
    }, 2000);
  }
})();
</script>

{{-- ══ SHELL (scroll container — sans overflow:hidden) ══ --}}
<div class="ca-shell">

  {{-- ── TOPBAR ── --}}
  @hasSection('topbar')
    @yield('topbar')
  @else
  <header class="ca-topbar @yield('topbar_class')">
    @hasSection('back_btn')
    <a href="@yield('back_url', route('client.app.home'))" class="ca-topbar__back" aria-label="Retour">
      <i class="fas fa-arrow-left"></i>
    </a>
    @else
    <div style="width:38px"></div>
    @endif

    <span class="ca-topbar__title">@yield('page_title', site_name())</span>

    @hasSection('topbar_action')
    @yield('topbar_action')
    @else
    <div style="width:38px"></div>
    @endif
  </header>
  @endif

  {{-- Flash messages --}}
  @if(session('success'))
  <div class="ca-flash ca-flash--ok" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div class="ca-flash ca-flash--err" role="alert">
    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
  </div>
  @endif

  {{-- ── CONTENU PRINCIPAL ── --}}
  <main class="ca-main" id="ca-main-content">
    @yield('content')
  </main>

</div>{{-- /.ca-shell --}}

{{-- ══════════════════════════════════════════════════════════════════
     BOTTOM NAVIGATION — hors du shell pour eviter le clip iOS Safari
     ══════════════════════════════════════════════════════════════════ --}}
@if (!View::hasSection('no_bottom_nav'))
<nav class="ca-nav" role="navigation" aria-label="{{ __('app.nav_label', [], app()->getLocale()) ?? 'Navigation' }}">

  {{-- Accueil --}}
  <a href="{{ route('client.app.home') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.home') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_home') }}">
    <i class="fas fa-house"></i>
    <span>{{ __('app.nav_home') }}</span>
  </a>

  {{-- Dossiers --}}
  <a href="{{ route('client.app.dossiers') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.dossiers', 'client.app.loans*') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_loans') }}">
    <i class="fas fa-folder-open"></i>
    <span>{{ __('app.nav_loans') }}</span>
  </a>

  {{-- Transfert — bouton FAB central --}}
  <a href="{{ route('client.app.transfers') }}"
     class="ca-nav-item ca-nav-item--center {{ request()->routeIs('client.app.transfer*', 'client.app.transfers') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_transfer') }}">
    <div class="ca-nav-center-btn" aria-hidden="true">
      <i class="fas fa-right-left"></i>
    </div>
    <span>{{ __('app.nav_transfer') }}</span>
  </a>

  {{-- Factures --}}
  <a href="{{ route('client.app.invoices') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.invoices*') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_invoices') }}">
    <i class="fas fa-file-invoice"></i>
    <span>{{ __('app.nav_invoices') }}</span>
  </a>

  {{-- Profil --}}
  <a href="{{ route('client.app.profile') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.profile') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_profile') }}">
    <i class="fas fa-circle-user"></i>
    <span>{{ __('app.nav_profile') }}</span>
  </a>

</nav>
@endif

{{-- ══ BANNIERE PWA ══ --}}
<div class="ca-install-banner" id="ca-install-banner" role="complementary">
  <div style="width:42px;height:42px;border-radius:14px;background:rgba(220,190,135,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
    <i class="fas fa-mobile-screen" style="color:var(--ca-accent-l);font-size:1.25rem"></i>
  </div>
  <div style="flex:1;min-width:0">
    <div style="font-size:.875rem;font-weight:700;color:var(--ca-text);margin-bottom:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
      {{ __('app.install') }}
    </div>
    <div style="font-size:.72rem;color:var(--ca-text-3)">{{ __('app.install_hint') }}</div>
  </div>
  <div style="display:flex;gap:.5rem;flex-shrink:0">
    <button id="ca-install-btn"
            style="background:linear-gradient(135deg,var(--ca-teal-l),var(--ca-teal));color:var(--ca-navy);border:none;padding:.45rem .9rem;border-radius:var(--ca-radius-sm);font-size:.8rem;font-weight:700;cursor:pointer;white-space:nowrap">
      {{ __('app.install_btn') }}
    </button>
    <button onclick="document.getElementById('ca-install-banner').style.display='none'"
            aria-label="Fermer"
            style="background:none;border:1px solid var(--ca-border);color:var(--ca-text-3);width:32px;height:32px;border-radius:50%;font-size:.75rem;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="fas fa-xmark"></i>
    </button>
  </div>
</div>

@stack('scripts')

{{-- ══ Push Notifications ══ --}}
<div id="cxa-push-banner" style="display:none;position:fixed;bottom:calc(62px + env(safe-area-inset-bottom,0px) + .75rem);left:.875rem;right:.875rem;z-index:9000;background:#0E3B2E;border:1px solid rgba(220,190,135,.35);border-radius:16px;padding:.875rem 1rem;box-shadow:0 8px 32px rgba(14,59,46,.5);display:none;align-items:center;gap:.875rem">
  <div style="width:42px;height:42px;border-radius:13px;background:rgba(220,190,135,.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
    <i class="fas fa-bell" style="color:#F5EDDD;font-size:1.1rem"></i>
  </div>
  <div style="flex:1;min-width:0">
    <div style="font-size:.84rem;font-weight:700;color:#fff;margin-bottom:.15rem">Activer les notifications</div>
    <div style="font-size:.72rem;color:rgba(255,255,255,.45);line-height:1.4">Recevez vos virements, factures et mises à jour en temps réel.</div>
  </div>
  <div style="display:flex;flex-direction:column;gap:.4rem;flex-shrink:0">
    <button id="cxa-push-allow" style="background:linear-gradient(90deg,#F5EDDD,#DCBE87);color:#0E3B2E;border:none;padding:.42rem .875rem;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;white-space:nowrap">Activer</button>
    <button id="cxa-push-later" style="background:none;border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.45);padding:.38rem .875rem;border-radius:8px;font-size:.72rem;cursor:pointer;white-space:nowrap">Plus tard</button>
  </div>
</div>

<script>window.SOLBERG_VAPID_KEY = '{{ config("services.vapid.public_key") }}';</script>
<script>
(function () {
  const CSRF        = '{{ csrf_token() }}';
  const STORAGE_KEY = 'cxa_push_asked';

  document.addEventListener('DOMContentLoaded', async function () {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

    const reg = await navigator.serviceWorker.ready;

    // Si les clés VAPID ont changé, invalider l'ancienne souscription
    const storedVapid = localStorage.getItem('cxa_vapid_pub');
    const currentVapid = window.SOLBERG_VAPID_KEY || '';
    if (storedVapid && storedVapid !== currentVapid) {
      const oldSub = await reg.pushManager.getSubscription();
      if (oldSub) {
        await fetch('/app/push/unsubscribe', {
          method:  'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
          body:    JSON.stringify({ endpoint: oldSub.endpoint }),
        }).catch(() => {});
        await oldSub.unsubscribe().catch(() => {});
      }
      localStorage.removeItem('cxa_vapid_pub');
      localStorage.setItem(STORAGE_KEY, 'reset'); // Forcer ré-affichage bannière
    }

    // Déjà abonné dans le navigateur avec les bonnes clés → re-sync DB
    const existing = await reg.pushManager.getSubscription();
    if (existing && (!storedVapid || storedVapid === currentVapid)) {
      if (typeof pushSubscribe === 'function') {
        await pushSubscribe(reg, CSRF).catch(() => {});
      }
      return;
    }

    if (Notification.permission === 'denied') return;

    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === 'denied') return;

    if (stored === 'granted') {
      if (typeof pushSubscribe === 'function') {
        await pushSubscribe(reg, CSRF).catch(() => {});
      }
      return;
    }

    // "Plus tard" → vérifier si les 7 jours sont écoulés
    if (stored && stored.startsWith('later:')) {
      const retryAt = parseInt(stored.split(':')[1], 10);
      if (Date.now() < retryAt) return;
      localStorage.removeItem(STORAGE_KEY);
    }

    // Montrer la bannière après 3 secondes
    setTimeout(function () {
      const banner = document.getElementById('cxa-push-banner');
      if (banner) banner.style.display = 'flex';
    }, 3000);

    document.getElementById('cxa-push-allow')?.addEventListener('click', async function () {
      document.getElementById('cxa-push-banner').style.display = 'none';
      const perm = await Notification.requestPermission();
      if (perm === 'granted') {
        if (typeof pushSubscribe === 'function') {
          await pushSubscribe(reg, CSRF).catch(() => {});
        }
      } else {
        localStorage.setItem(STORAGE_KEY, 'denied');
      }
    });

    document.getElementById('cxa-push-later')?.addEventListener('click', function () {
      document.getElementById('cxa-push-banner').style.display = 'none';
      const retry = Date.now() + 7 * 24 * 3600 * 1000;
      localStorage.setItem(STORAGE_KEY, 'later:' + retry);
    });
  });
})();
</script>

{{-- ══ Son & Polling notifications ══ --}}
<script>
// Synthese sonore Web Audio API (aucun fichier externe)
window.SolbergSound = (function () {
  let ctx = null;
  function ac() {
    if (!ctx) ctx = new (window.AudioContext || window.webkitAudioContext)();
    return ctx;
  }
  return {
    coin() {
      try {
        const c = ac();
        for (let i = 0; i < 3; i++) {
          const t = c.currentTime + i * 0.13;
          const o = c.createOscillator();
          const g = c.createGain();
          o.connect(g); g.connect(c.destination);
          o.type = 'triangle';
          o.frequency.setValueAtTime(1400, t);
          o.frequency.exponentialRampToValueAtTime(900, t + 0.07);
          g.gain.setValueAtTime(0.28, t);
          g.gain.exponentialRampToValueAtTime(0.001, t + 0.11);
          o.start(t); o.stop(t + 0.12);
        }
      } catch(e) {}
    },
    bell() {
      try {
        const c = ac();
        const t = c.currentTime;
        const o = c.createOscillator();
        const g = c.createGain();
        o.connect(g); g.connect(c.destination);
        o.type = 'sine';
        o.frequency.setValueAtTime(880, t);
        o.frequency.exponentialRampToValueAtTime(620, t + 0.35);
        g.gain.setValueAtTime(0.32, t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.55);
        o.start(t); o.stop(t + 0.56);
      } catch(e) {}
    }
  };
})();

// Polling des notifications toutes les 30 secondes
(function () {
  const POLL_MS = 30000;
  let lastCount = parseInt(sessionStorage.getItem('cxa_notif_count') || '0', 10);

  function updateDot(count) {
    const dot = document.getElementById('notif-dot');
    if (!dot) return;
    dot.style.display = count > 0 ? '' : 'none';
  }

  async function poll() {
    try {
      const r = await fetch('/app/notifications/unread-count', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      if (!r.ok) return;
      const data = await r.json();
      const count = data.count || 0;

      updateDot(count);

      if (count > lastCount) {
        if (data.type === 'transfer') {
          window.SolbergSound.coin();
        } else {
          window.SolbergSound.bell();
        }
      }
      lastCount = count;
      sessionStorage.setItem('cxa_notif_count', String(count));
    } catch (e) {}
  }

  document.addEventListener('DOMContentLoaded', function () {
    poll();
    setInterval(poll, POLL_MS);
  });
})();
</script>
</body>
</html>
