<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#C6A15B">
<link rel="icon" type="image/png" sizes="192x192" href="/site-icon-192.png">
<title>Réinitialiser le mot de passe — {{ site_name() }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Fraunces:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
  --bg:   #02182E;
  --inp:  #06304F;
  --cyan: #2B94F7;
  --cyan2:#DCBE87;
  --text: #FFFFFF;
  --sub:  rgba(255,255,255,.52);
  --muted:rgba(255,255,255,.28);
  --bdr:  rgba(220,190,135,.16);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{
  height:100%;background:var(--bg);color:var(--text);
  font-family:'Outfit', system-ui, sans-serif;font-size:15px;
  -webkit-font-smoothing:antialiased;
}
body{min-height:100vh;overflow-x:hidden}
a{text-decoration:none;color:inherit}

.bg-orbs{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.orb{position:absolute;border-radius:50%;filter:blur(90px)}
.orb-1{width:480px;height:480px;top:-10%;right:-8%;background:radial-gradient(circle,rgba(13,207,220,.1) 0%,transparent 65%);animation:orbf 10s ease-in-out infinite alternate}
.orb-2{width:360px;height:360px;bottom:-15%;left:-8%;background:radial-gradient(circle,rgba(13,207,220,.06) 0%,transparent 65%);animation:orbf 14s ease-in-out infinite alternate-reverse}
@keyframes orbf{from{transform:scale(1)}to{transform:scale(1.1) translate(2%,3%)}}

.topbar{
  position:relative;z-index:10;
  display:flex;align-items:center;
  padding:.9rem 1.5rem;
  padding-top:calc(.9rem + env(safe-area-inset-top,0px));
}
.topbar__back{
  display:inline-flex;align-items:center;gap:.45rem;
  font-size:.78rem;font-weight:500;color:var(--sub);transition:color .18s;
}
.topbar__back:hover{color:var(--text)}
.topbar__back i{font-size:.65rem}

.page-shell{display:flex;flex-direction:column;min-height:100vh}
.page-wrap{
  position:relative;z-index:1;flex:1;
  display:flex;align-items:center;justify-content:center;
  padding:1.5rem 1.25rem 2rem;
}

.card{width:100%;max-width:400px;text-align:center}

.icon-badge{
  width:72px;height:72px;border-radius:50%;
  background:rgba(13,207,220,.1);border:1.5px solid rgba(13,207,220,.25);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.5rem;box-shadow:0 0 28px rgba(13,207,220,.15);
}
.icon-badge i{font-size:1.75rem;color:var(--cyan)}

.card-title{font-family:'Outfit',sans-serif;font-size:1.625rem;font-weight:800;color:var(--text);margin-bottom:.45rem}
.card-sub{font-size:.82rem;color:var(--sub);line-height:1.65;margin-bottom:1.875rem;max-width:320px;margin-left:auto;margin-right:auto}

.ferr{
  display:flex;align-items:flex-start;gap:.55rem;
  background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);
  border-left:3px solid #ef4444;border-radius:10px;
  padding:.7rem .9rem;font-size:.79rem;color:#FCA5A5;margin-bottom:1.125rem;text-align:left;
}
.ferr i{margin-top:.1rem;flex-shrink:0}

.fgrp{margin-bottom:.875rem;text-align:left}
.flabel{display:block;font-size:.75rem;font-weight:600;color:rgba(255,255,255,.5);margin-bottom:.4rem}
.frel{position:relative}
.ficon{position:absolute;left:.95rem;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:.75rem;pointer-events:none;transition:color .18s}
.finput{
  width:100%;padding:.85rem 1rem .85rem 2.6rem;
  background:var(--inp);border:1.5px solid rgba(255,255,255,.08);border-radius:12px;
  font-size:.88rem;font-family:'Outfit',sans-serif;color:var(--text);
  outline:none;transition:border-color .2s,box-shadow .2s,background .2s;
}
.finput::placeholder{color:rgba(255,255,255,.2)}
.finput:focus{border-color:var(--cyan);background:#161E30;box-shadow:0 0 0 3.5px rgba(13,207,220,.15)}
.finput:focus ~ .ficon,.frel:focus-within .ficon{color:var(--cyan)}
.finput.err{border-color:#ef4444}
.feye{
  position:absolute;right:.9rem;top:50%;transform:translateY(-50%);
  background:none;border:none;color:rgba(255,255,255,.28);cursor:pointer;
  font-size:.78rem;padding:.3rem;display:flex;align-items:center;transition:color .18s;
}
.feye:hover{color:rgba(255,255,255,.7)}

/* Password strength bar */
.strength-bar{
  height:3px;border-radius:2px;background:rgba(255,255,255,.08);
  margin-top:.45rem;overflow:hidden;
}
.strength-fill{height:100%;border-radius:2px;transition:width .3s,background .3s;width:0}

.fbtn{
  width:100%;padding:.92rem 1.5rem;border:none;border-radius:999px;
  font-size:.97rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:.625rem;
  background:linear-gradient(135deg,#DCBE87 0%,#C6A15B 100%);
  color:#080C18;letter-spacing:.01em;
  box-shadow:0 6px 28px rgba(13,207,220,.35),0 2px 8px rgba(0,0,0,.3);
  transition:filter .2s,box-shadow .2s,transform .1s;margin-top:.5rem;
}
.fbtn:hover{filter:brightness(1.08);box-shadow:0 8px 36px rgba(13,207,220,.5)}
.fbtn:active{transform:scale(.975)}
.fbtn:disabled{opacity:.6;cursor:not-allowed;filter:none}

.back-link{margin-top:1.375rem;font-size:.78rem;color:var(--muted)}
.back-link a{color:var(--cyan);font-weight:600;transition:opacity .18s}
.back-link a:hover{opacity:.75}

.pg-foot{
  padding:.75rem 1.5rem 1.25rem;text-align:center;
  padding-bottom:calc(1.25rem + env(safe-area-inset-bottom,0px));
  font-size:.68rem;color:rgba(255,255,255,.22);position:relative;z-index:1;
}
.pg-foot a{color:rgba(255,255,255,.28)}.pg-foot a:hover{color:rgba(255,255,255,.55)}

@keyframes spin{to{transform:rotate(360deg)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.icon-badge{animation:fadeUp .4s ease .05s both}
.card-title {animation:fadeUp .4s ease .1s  both}
.card-sub   {animation:fadeUp .4s ease .15s both}
.fgrp:nth-child(1){animation:fadeUp .4s ease .18s both}
.fgrp:nth-child(2){animation:fadeUp .4s ease .21s both}
.fgrp:nth-child(3){animation:fadeUp .4s ease .24s both}
.fbtn       {animation:fadeUp .4s ease .27s both}
</style>
</head>
<body>

<div class="bg-orbs" aria-hidden="true">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
</div>

<div class="page-shell">

  <div class="topbar">
    <a href="/login" class="topbar__back">
      <i class="fas fa-arrow-left"></i> Retour à la connexion
    </a>
  </div>

  <div class="page-wrap">
    <div class="card">

      <div class="icon-badge">
        <i class="fas fa-key"></i>
      </div>

      <h1 class="card-title">Nouveau mot de passe</h1>
      <p class="card-sub">Choisissez un nouveau mot de passe sécurisé pour votre compte {{ site_name() }}.</p>

      @if($errors->any())
      <div class="ferr">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
      </div>
      @endif

      <form method="POST" action="{{ route('password.update') }}"
            onsubmit="this.querySelector('button[type=submit]').disabled=true">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="fgrp">
          <label class="flabel" for="email">Adresse email</label>
          <div class="frel">
            <i class="fas fa-envelope ficon"></i>
            <input type="email" id="email" name="email"
                   class="finput {{ $errors->has('email') ? 'err' : '' }}"
                   value="{{ old('email', $email) }}"
                   placeholder="votre@email.com"
                   autocomplete="email" required>
          </div>
        </div>

        <div class="fgrp">
          <label class="flabel" for="password">Nouveau mot de passe</label>
          <div class="frel">
            <i class="fas fa-lock ficon"></i>
            <input type="password" id="password" name="password"
                   class="finput {{ $errors->has('password') ? 'err' : '' }}"
                   placeholder="Minimum 8 caractères"
                   autocomplete="new-password" required
                   oninput="updateStrength(this.value)">
            <button type="button" class="feye" onclick="tglPwd('password',this)">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <div class="strength-bar">
            <div class="strength-fill" id="strength-fill"></div>
          </div>
        </div>

        <div class="fgrp">
          <label class="flabel" for="password_confirmation">Confirmer le mot de passe</label>
          <div class="frel">
            <i class="fas fa-lock ficon"></i>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="finput"
                   placeholder="Répétez le mot de passe"
                   autocomplete="new-password" required>
            <button type="button" class="feye" onclick="tglPwd('password_confirmation',this)">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="fbtn">
          <i class="fas fa-check-circle"></i>
          Réinitialiser le mot de passe
        </button>
      </form>

      <div class="back-link">
        Vous vous souvenez ? <a href="/login">Se connecter</a>
      </div>

    </div>
  </div>

  <div class="pg-foot">
    &copy; {{ date('Y') }} {{ site_name() }} &nbsp;·&nbsp;
    <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;·&nbsp;
    <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
  </div>

</div>

<script>
function tglPwd(id, btn) {
  var inp = document.getElementById(id);
  var ico = btn.querySelector('i');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.className = 'fas fa-eye-slash';
  } else {
    inp.type = 'password';
    ico.className = 'fas fa-eye';
  }
}

function updateStrength(val) {
  var fill = document.getElementById('strength-fill');
  var score = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  var colors = ['#ef4444','#f97316','#eab308','#22c55e','#0DCFDC'];
  var widths  = ['20%','40%','60%','80%','100%'];
  fill.style.width      = widths[Math.max(score-1,0)] || '0';
  fill.style.background = colors[Math.max(score-1,0)] || 'transparent';
}
</script>
</body>
</html>
