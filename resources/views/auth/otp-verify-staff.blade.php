<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#C6A15B">
<link rel="apple-touch-icon" sizes="180x180" href="/site-icon-180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/site-icon-192.png">
<title>{{ __('auth.otp_title') }} | {{ site_name() }} Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
<style>
:root{
  --navy:#0E3B2E;--nm:#14503D;--nl:#9A7736;
  --accent:#C6A15B;--gd:#9A7736;--gp:#F5EDDD;
}
html,body{height:100%;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column}

.auth-left{
  /* Fond bleu profond : accent eclairci pour rester lisible (AA). */
  --accent:#DCBE87;--gd:#2B94F7;
  background:linear-gradient(160deg,#0E3B2E 0%,#0E3B2E 45%,#112237 100%);
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

.auth-left__copy{font-size:.7rem;color:rgba(255,255,255,.2);position:relative;z-index:1}

.auth-right{background:#fff;display:flex;flex-direction:column;min-height:100vh}
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

.auth-form-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:2rem}
.auth-form-inner{width:100%;max-width:400px;text-align:center}

.otp-icon{
  width:64px;height:64px;border-radius:18px;
  background:linear-gradient(135deg,rgba(198,161,91,.16),rgba(150,112,47,.16));
  border:1.5px solid rgba(198,161,91,.3);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.25rem;
}
.otp-icon i{font-size:1.75rem;color:var(--gd)}

.form-title{
  font-family:'Fraunces',serif;font-size:1.75rem;font-weight:800;
  color:var(--navy);line-height:1.15;margin-bottom:.5rem;
}
.form-sub{font-size:.82rem;color:#6b7280;margin-bottom:1.75rem;line-height:1.6}
.form-sub strong{color:var(--navy)}

.auth-error{
  display:flex;align-items:center;gap:.5rem;text-align:left;
  background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #ef4444;
  border-radius:9px;padding:.7rem .9rem;font-size:.8rem;color:#991b1b;
  margin-bottom:1rem;
}

.odigits{display:flex;gap:.6rem;justify-content:center;margin-bottom:1.25rem}
.odigit{
  box-sizing:border-box;
  width:54px;height:60px;border-radius:12px;
  background:#fff;border:2px solid #ccd2db;
  text-align:center;font-size:1.6rem;font-weight:800;color:var(--navy);
  font-family:'Outfit',sans-serif;
  outline:none;caret-color:var(--navy);
  transition:border-color .2s,box-shadow .2s,background .2s;
  box-shadow:0 1px 3px rgba(14,59,46,.06);
}
.odigit::placeholder{color:#c7ccd4}
.odigit.filled{border-color:var(--navy);background:rgba(14,59,46,.045)}
.odigit:focus{border-color:var(--navy);box-shadow:0 0 0 4px rgba(14,59,46,.12)}
.odigit.is-err{border-color:#ef4444;background:rgba(239,68,68,.06);box-shadow:0 0 0 4px rgba(239,68,68,.1)}

.btn-auth{
  width:100%;padding:.8rem;border:none;border-radius:10px;
  font-size:.9rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
  background:var(--navy);color:#fff;
  transition:background .2s,transform .12s;letter-spacing:.01em;margin-bottom:.75rem;
}
.btn-auth:hover{background:#0E3B2E}
.btn-auth:active{transform:scale(.98)}
.btn-auth:disabled{opacity:.5;cursor:not-allowed}

.resend-row{font-size:.79rem;color:#6b7280;margin-top:.5rem}
.resend-btn{background:none;border:none;cursor:pointer;padding:0;font-size:.79rem;font-weight:700;color:var(--gd)}
.resend-btn:disabled{opacity:.4;cursor:not-allowed}
.resend-msg{display:block;font-size:.74rem;font-weight:600;margin-top:.35rem}
.resend-msg.ok{color:#059669}.resend-msg.fail{color:#dc2626}

.auth-footer{padding:.875rem 2rem 1.25rem;text-align:center;font-size:.72rem;color:#9ca3af;flex-shrink:0}

@media (max-width:991.98px){
  .auth-left{display:none!important}
  .auth-topbar{padding:1rem 1.25rem}
  .auth-form-wrap{padding:1.5rem 1.25rem}
}
</style>
</head>
<body>
<div class="container-fluid p-0" style="min-height:100vh">
<div class="row g-0" style="min-height:100vh">

  <div class="col-lg-5 d-none d-lg-flex">
    <div class="auth-left w-100">
      <div class="auth-left__logo">
        <x-logo variant="full" theme="dark" href="{{ url('/') }}" />
      </div>
      <div class="auth-left__body">
        <div class="staff-badge">
          <div class="staff-badge__ico"><i class="fas fa-shield-halved"></i></div>
          <div class="staff-badge__text">
            <div class="staff-badge__label">{{ __('auth.staff_restricted') }}</div>
            <div class="staff-badge__sub">{{ request()->getHost() }} &mdash; secure access</div>
          </div>
        </div>
        <h2 class="auth-left__title">{{ __('auth.otp_heading') }}</h2>
        <p class="auth-left__sub">{{ __('auth.otp_subtitle') }}</p>
      </div>
      <div class="auth-left__copy">&copy; {{ date('Y') }} {{ site_name() }}</div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="auth-right">

      <div class="auth-topbar">
        <a href="{{ $backUrl ?? route('staff.login') }}" class="auth-topbar__back">
          <i class="fas fa-arrow-left"></i> {{ __('auth.otp_back') }}
        </a>
        <x-logo variant="icon" theme="light" size="sm" href="{{ url('/') }}" class="auth-topbar__logo d-lg-none" />
      </div>

      <div class="auth-form-wrap">
        <div class="auth-form-inner">

          <div class="otp-icon"><i class="fas fa-shield-halved"></i></div>
          <h1 class="form-title">{{ __('auth.otp_heading') }}</h1>
          <p class="form-sub">{{ __('auth.otp_subtitle') }}<br><strong>{{ $masked }}</strong></p>

          <div class="auth-error" id="otp-error" style="display:none">
            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
            <span id="otp-error-text"></span>
          </div>

          <form id="otp-form">
            <div class="odigits" id="odigits-row">
              <input type="text" inputmode="numeric" maxlength="1" placeholder="•" class="odigit" data-idx="0">
              <input type="text" inputmode="numeric" maxlength="1" placeholder="•" class="odigit" data-idx="1">
              <input type="text" inputmode="numeric" maxlength="1" placeholder="•" class="odigit" data-idx="2">
              <input type="text" inputmode="numeric" maxlength="1" placeholder="•" class="odigit" data-idx="3">
              <input type="text" inputmode="numeric" maxlength="1" placeholder="•" class="odigit" data-idx="4">
              <input type="text" inputmode="numeric" maxlength="1" placeholder="•" class="odigit" data-idx="5">
            </div>

            <button type="submit" class="btn-auth" id="otp-submit" disabled>
              <span id="otp-submit-label"><i class="fas fa-check me-1"></i>{{ __('auth.otp_verify_btn') }}</span>
              <span id="otp-submit-spinner" style="display:none"><i class="fas fa-circle-notch fa-spin"></i></span>
            </button>
          </form>

          <div class="resend-row">
            <span id="otp-timer">{{ __('auth.otp_resend_in') }} <strong id="otp-timer-val">02:00</strong></span>
            <button type="button" class="resend-btn" id="otp-resend-btn" style="display:none">{{ __('auth.otp_resend') }}</button>
            <span id="otp-resend-spinner" style="display:none"><i class="fas fa-circle-notch fa-spin"></i></span>
            <span class="resend-msg" id="otp-resend-msg" style="display:none"></span>
          </div>

        </div>
      </div>

      <div class="auth-footer">&copy; {{ date('Y') }} {{ site_name() }}</div>
    </div>
  </div>

</div>
</div>

<script>
(function () {
  var VERIFY_URL   = '{{ route("otp.verify") }}';
  var RESEND_URL   = '{{ route("otp.resend") }}';
  var CSRF_REFRESH_URL = '{{ route("csrf.refresh") }}';
  var CSRF_TOKEN   = '{{ csrf_token() }}';
  var MSG_INVALID  = '{{ __("auth.otp_invalid", ["remaining" => 1]) }}';
  var MSG_FAILED   = '{{ __("auth.otp_send_failed") }}';
  var MSG_RESENT   = '{{ __("auth.otp_resend_success") }}';

  /* Si la session a expiré pendant que l'onglet restait ouvert, le jeton CSRF
     embarqué au chargement devient invalide (419). On le rafraîchit et on
     retente une seule fois, plutôt que de laisser échouer silencieusement. */
  function postJson(url, body) {
    function doFetch() {
      return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: body ? JSON.stringify(body) : undefined,
      });
    }
    return doFetch().then(function (r) {
      if (r.status === 419) {
        return fetch(CSRF_REFRESH_URL, { headers: { 'Accept': 'application/json' } })
          .then(function (rr) { return rr.json(); })
          .then(function (j) { if (j && j.token) CSRF_TOKEN = j.token; })
          .catch(function () {})
          .then(doFetch);
      }
      return r;
    });
  }

  var inputs      = Array.prototype.slice.call(document.querySelectorAll('.odigit'));
  var form        = document.getElementById('otp-form');
  var submitBtn   = document.getElementById('otp-submit');
  var submitLabel = document.getElementById('otp-submit-label');
  var submitSpin  = document.getElementById('otp-submit-spinner');
  var errorBox    = document.getElementById('otp-error');
  var errorText   = document.getElementById('otp-error-text');
  var timerBox    = document.getElementById('otp-timer');
  var timerVal    = document.getElementById('otp-timer-val');
  var resendBtn   = document.getElementById('otp-resend-btn');
  var resendSpin  = document.getElementById('otp-resend-spinner');
  var resendMsg   = document.getElementById('otp-resend-msg');

  var timeLeft   = 120;
  var timer      = null;
  var submitting = false;
  var resending  = false;

  function digits() {
    return inputs.map(function (inp) { return inp.value; }).join('');
  }

  function updateSubmitState() {
    submitBtn.disabled = digits().length < 6 || submitting;
  }

  function setFilled(inp) {
    if (inp.value) inp.classList.add('filled'); else inp.classList.remove('filled');
  }

  function clearError() {
    inputs.forEach(function (inp) { inp.classList.remove('is-err'); });
    errorBox.style.display = 'none';
  }

  function showError(msg) {
    inputs.forEach(function (inp) { inp.classList.add('is-err'); });
    errorText.textContent = msg;
    errorBox.style.display = 'flex';
    setTimeout(function () {
      inputs.forEach(function (inp) { inp.value = ''; inp.classList.remove('is-err', 'filled'); });
      updateSubmitState();
      inputs[0].focus();
    }, 500);
  }

  function fmtTime(t) {
    var m = Math.floor(t / 60), s = t % 60;
    return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
  }

  function startTimer() {
    clearInterval(timer);
    timeLeft = 120;
    timerVal.textContent = fmtTime(timeLeft);
    timerBox.style.display = '';
    resendBtn.style.display = 'none';
    timer = setInterval(function () {
      timeLeft--;
      if (timeLeft <= 0) {
        clearInterval(timer);
        timerBox.style.display = 'none';
        resendBtn.style.display = '';
      } else {
        timerVal.textContent = fmtTime(timeLeft);
      }
    }, 1000);
  }

  inputs.forEach(function (inp, i) {
    inp.addEventListener('input', function () {
      inp.value = (inp.value || '').replace(/\D/g, '').slice(0, 1);
      setFilled(inp);
      clearError();
      updateSubmitState();
      if (inp.value && i < inputs.length - 1) inputs[i + 1].focus();
      if (digits().length === 6) doSubmit();
    });

    inp.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !inp.value && i > 0) {
        inputs[i - 1].focus();
        inputs[i - 1].value = '';
        setFilled(inputs[i - 1]);
        updateSubmitState();
      }
    });

    inp.addEventListener('paste', function (e) {
      e.preventDefault();
      var val = ((e.clipboardData || window.clipboardData).getData('text') || '').replace(/\D/g, '').slice(0, 6);
      for (var k = 0; k < inputs.length; k++) {
        inputs[k].value = val[k] || '';
        setFilled(inputs[k]);
      }
      updateSubmitState();
      if (val.length === 6) doSubmit();
    });
  });

  function doSubmit() {
    if (digits().length < 6 || submitting) return;
    submitting = true;
    clearError();
    submitLabel.style.display = 'none';
    submitSpin.style.display  = '';
    updateSubmitState();

    postJson(VERIFY_URL, { code: digits() })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.status === 'success' || data.status === 'blocked' || data.status === 'redirect') {
          window.location.href = data.url;
          return;
        }
        submitting = false;
        submitLabel.style.display = '';
        submitSpin.style.display  = 'none';
        updateSubmitState();
        showError(data.message || MSG_INVALID);
      })
      .catch(function () {
        submitting = false;
        submitLabel.style.display = '';
        submitSpin.style.display  = 'none';
        updateSubmitState();
        showError(MSG_FAILED);
      });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    doSubmit();
  });

  resendBtn.addEventListener('click', function () {
    if (resending || timeLeft > 0) return;
    resending = true;
    resendBtn.style.display = 'none';
    resendSpin.style.display = '';
    resendMsg.style.display = 'none';

    postJson(RESEND_URL)
      .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
      .then(function (res) {
        resending = false;
        resendSpin.style.display = 'none';
        resendMsg.className = 'resend-msg ' + (res.ok ? 'ok' : 'fail');
        resendMsg.textContent = res.ok ? (res.data.message || MSG_RESENT) : (res.data.error || MSG_FAILED);
        resendMsg.style.display = 'block';
        if (res.ok) {
          inputs.forEach(function (inp) { inp.value = ''; inp.classList.remove('filled', 'is-err'); });
          updateSubmitState();
          clearError();
          startTimer();
          inputs[0].focus();
        } else {
          resendBtn.style.display = '';
        }
        setTimeout(function () { resendMsg.style.display = 'none'; }, 4000);
      })
      .catch(function () {
        resending = false;
        resendSpin.style.display = 'none';
        resendBtn.style.display = '';
        resendMsg.className = 'resend-msg fail';
        resendMsg.textContent = MSG_FAILED;
        resendMsg.style.display = 'block';
        setTimeout(function () { resendMsg.style.display = 'none'; }, 4000);
      });
  });

  startTimer();
  inputs[0].focus();
})();
</script>
</body>
</html>
