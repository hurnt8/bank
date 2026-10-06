{{-- Bulle flottante d'installation de l'application (PWA) --}}
<style>
.pwa-fab{position:fixed;right:1rem;bottom:calc(1rem + env(safe-area-inset-bottom,0px));z-index:900;display:none;flex-direction:column;align-items:flex-end;gap:.6rem}
.pwa-fab__btn{display:flex;align-items:center;gap:.55rem;min-height:48px;padding:.55rem 1.1rem .55rem .6rem;border:none;border-radius:999px;cursor:pointer;
  font-family:inherit;font-size:.82rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#DCBE87,#C6A15B);
  box-shadow:0 8px 28px rgba(198,161,91,.45),0 2px 8px rgba(2,24,46,.5);transition:transform .15s}
.pwa-fab__btn:active{transform:scale(.97)}
.pwa-fab__ico{width:34px;height:34px;border-radius:50%;background:rgba(2,24,46,.28);display:flex;align-items:center;justify-content:center;font-size:.9rem}
.pwa-fab__x{position:absolute;top:-8px;right:-4px;width:22px;height:22px;border-radius:50%;border:1px solid var(--bdr);background:var(--card);color:var(--sub);
  font-size:.6rem;cursor:pointer;display:flex;align-items:center;justify-content:center}
.pwa-fab__wrap{position:relative}
.pwa-fab__panel{width:min(320px,calc(100vw - 2rem));background:var(--card);border:1.5px solid var(--bdr);border-radius:16px;padding:1rem 1rem .9rem;
  box-shadow:0 16px 48px rgba(0,0,0,.5);font-size:.8rem;color:var(--sub);line-height:1.6;display:none}
.pwa-fab__panel.is-open{display:block}
.pwa-fab__panel strong{color:var(--accent)}
.pwa-fab__ttl{font-size:.86rem;font-weight:700;color:var(--text);margin-bottom:.5rem;display:flex;align-items:center;gap:.5rem}
.pwa-fab__ttl i{color:var(--accent)}
.pwa-fab__st{display:flex;align-items:flex-start;gap:.55rem;margin-top:.35rem}
.pwa-fab__st i{color:var(--accent);width:16px;text-align:center;margin-top:.2rem}
@media (max-width:420px){.pwa-fab__lbl{display:none}.pwa-fab__btn{padding:.55rem}}
</style>

<div class="pwa-fab" id="pwa-fab">
  <div class="pwa-fab__panel" id="pwa-panel" role="dialog" aria-label="{{ __('auth.pwa_install_title') }}">
    <div class="pwa-fab__ttl"><i class="fas fa-mobile-screen"></i>{{ __('auth.pwa_install_title') }}</div>
    <div id="pwa-ios-steps" style="display:none">
      <div class="pwa-fab__st"><i class="fas fa-arrow-up-from-bracket"></i><span>{!! __('auth.pwa_ios_step1') !!}</span></div>
      <div class="pwa-fab__st"><i class="fas fa-plus-square"></i><span>{!! __('auth.pwa_ios_step2') !!}</span></div>
      <div class="pwa-fab__st"><i class="fas fa-check-circle"></i><span>{!! __('auth.pwa_ios_step3') !!}</span></div>
    </div>
    <div id="pwa-generic-steps" style="display:none">{{ __('onboarding.pwa_generic') }}</div>
  </div>

  <div class="pwa-fab__wrap">
    <button type="button" class="pwa-fab__btn" id="pwa-fab-btn">
      <span class="pwa-fab__ico"><i class="fas fa-download"></i></span>
      <span class="pwa-fab__lbl">{{ __('auth.pwa_install_title') }}</span>
    </button>
    <button type="button" class="pwa-fab__x" id="pwa-fab-x" aria-label="{{ __('auth.pwa_close') }}"><i class="fas fa-xmark"></i></button>
  </div>
</div>

<script>
(function () {
  var KEY = 'cxa_pwa_dismissed';
  var fab = document.getElementById('pwa-fab'), panel = document.getElementById('pwa-panel');
  var standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  var dismissed = false;
  try { dismissed = !!localStorage.getItem(KEY); } catch (e) {}
  if (standalone || dismissed) return;

  if ('serviceWorker' in navigator) { navigator.serviceWorker.register('/sw.js').catch(function () {}); }

  var ua = navigator.userAgent;
  var isIOS = /iphone|ipad|ipod/i.test(ua);
  var deferred = null;

  function hide(remember) {
    fab.style.display = 'none';
    if (remember) { try { localStorage.setItem(KEY, '1'); } catch (e) {} }
  }

  window.addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); deferred = e; });
  window.addEventListener('appinstalled', function () { hide(true); });

  document.getElementById('pwa-fab-btn').addEventListener('click', async function () {
    if (deferred) {
      deferred.prompt();
      var choice = await deferred.userChoice;
      deferred = null;
      if (choice.outcome === 'accepted') hide(true);
      return;
    }
    // Pas de prompt natif (iOS, Firefox, déjà refusé…) : on affiche les étapes manuelles.
    document.getElementById('pwa-ios-steps').style.display = isIOS ? 'block' : 'none';
    document.getElementById('pwa-generic-steps').style.display = isIOS ? 'none' : 'block';
    panel.classList.toggle('is-open');
  });
  document.getElementById('pwa-fab-x').addEventListener('click', function () { hide(true); });

  fab.style.display = 'flex';
})();
</script>
