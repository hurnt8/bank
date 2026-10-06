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
<title>Mot de passe oublié — {{ site_name() }} Admin</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Fraunces:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">

<style>
:root{
  --navy:#0E3B2E;--nm:#14503D;--nl:#9A7736;
  --accent:#DCBE87;--gd:#2B94F7;--gp:#F5EDDD;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{
  height:100%;background:var(--navy);color:#fff;
  font-family:'Outfit', system-ui, sans-serif;font-size:15px;
  -webkit-font-smoothing:antialiased;
}
body{min-height:100vh;overflow-x:hidden}
a{text-decoration:none;color:inherit}

.bg-grid{position:fixed;inset:0;pointer-events:none;z-index:0;
  background-image:linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
  background-size:40px 40px;
}
.bg-orb{position:fixed;top:-100px;right:-100px;width:380px;height:380px;border-radius:50%;
  background:radial-gradient(circle,rgba(220,190,135,.08) 0%,transparent 70%);pointer-events:none;z-index:0}

.topbar{position:relative;z-index:10;display:flex;align-items:center;
  padding:.9rem 1.5rem;padding-top:calc(.9rem + env(safe-area-inset-top,0px));}
.topbar__back{display:inline-flex;align-items:center;gap:.45rem;
  font-size:.78rem;font-weight:500;color:rgba(255,255,255,.5);transition:color .18s;}
.topbar__back:hover{color:#fff}
.topbar__back i{font-size:.65rem}

.page-shell{display:flex;flex-direction:column;min-height:100vh}
.page-wrap{position:relative;z-index:1;flex:1;display:flex;align-items:center;justify-content:center;padding:1.5rem 1.25rem 2rem}
.card{width:100%;max-width:400px;text-align:center}

.icon-badge{
  width:72px;height:72px;border-radius:50%;
  background:rgba(220,190,135,.1);border:1.5px solid rgba(220,190,135,.28);
  display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;
  box-shadow:0 0 28px rgba(220,190,135,.15);
}
.icon-badge i{font-size:1.75rem;color:var(--accent)}

.card-title{font-family:'Outfit',sans-serif;font-size:1.625rem;font-weight:800;color:#fff;margin-bottom:.45rem}
.card-sub{font-size:.82rem;color:rgba(255,255,255,.5);line-height:1.65;margin-bottom:1.875rem;max-width:320px;margin-left:auto;margin-right:auto}

.success-box{
  background:rgba(74,222,128,.08);border:1.5px solid rgba(74,222,128,.2);
  border-radius:14px;padding:1.5rem 1.25rem;margin-bottom:1.5rem;
}
.success-box i{font-size:2rem;color:#4ade80;margin-bottom:.875rem;display:block}
.success-box p{font-size:.84rem;color:rgba(255,255,255,.7);line-height:1.65}
.success-box strong{color:#4ade80;font-weight:600}

.ferr{
  display:flex;align-items:flex-start;gap:.55rem;
  background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);
  border-left:3px solid #ef4444;border-radius:10px;
  padding:.7rem .9rem;font-size:.79rem;color:#FCA5A5;margin-bottom:1.125rem;text-align:left;
}
.ferr i{margin-top:.1rem;flex-shrink:0}

.fgrp{margin-bottom:1rem;text-align:left}
.flabel{display:block;font-size:.75rem;font-weight:600;color:rgba(255,255,255,.5);margin-bottom:.4rem}
.frel{position:relative}
.ficon{position:absolute;left:.95rem;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:.75rem;pointer-events:none;transition:color .18s}
.finput{
  width:100%;padding:.85rem 1rem .85rem 2.6rem;
  background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.1);border-radius:12px;
  font-size:.88rem;font-family:'Outfit',sans-serif;color:#fff;
  outline:none;transition:border-color .2s,box-shadow .2s,background .2s;
}
.finput::placeholder{color:rgba(255,255,255,.25)}
.finput:focus{border-color:var(--accent);background:rgba(255,255,255,.08);box-shadow:0 0 0 3.5px rgba(220,190,135,.15)}
.finput:focus ~ .ficon,.frel:focus-within .ficon{color:var(--accent)}
.finput.err{border-color:#ef4444}

.fbtn{
  width:100%;padding:.92rem 1.5rem;border:none;border-radius:10px;
  font-size:.95rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:.625rem;
  background:var(--accent);color:#0E3B2E;letter-spacing:.01em;
  box-shadow:0 6px 24px rgba(220,190,135,.3);
  transition:filter .2s,box-shadow .2s,transform .1s;margin-top:.25rem;
}
.fbtn:hover{filter:brightness(1.08)}
.fbtn:active{transform:scale(.975)}
.fbtn:disabled{opacity:.6;cursor:not-allowed;filter:none}

.back-link{margin-top:1.375rem;font-size:.78rem;color:rgba(255,255,255,.3)}
.back-link a{color:var(--accent);font-weight:600;transition:opacity .18s}
.back-link a:hover{opacity:.75}

.pg-foot{
  padding:.75rem 1.5rem 1.25rem;text-align:center;
  padding-bottom:calc(1.25rem + env(safe-area-inset-bottom,0px));
  font-size:.68rem;color:rgba(255,255,255,.22);position:relative;z-index:1;
}
.pg-foot a{color:rgba(255,255,255,.28)}.pg-foot a:hover{color:rgba(255,255,255,.55)}

@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.icon-badge{animation:fadeUp .4s ease .05s both}
.card-title {animation:fadeUp .4s ease .1s  both}
.card-sub   {animation:fadeUp .4s ease .15s both}
.fgrp       {animation:fadeUp .4s ease .18s both}
.fbtn       {animation:fadeUp .4s ease .22s both}
.back-link  {animation:fadeUp .4s ease .26s both}
.success-box{animation:fadeUp .4s ease .05s both}
</style>
</head>
<body>

<div class="bg-grid" aria-hidden="true"></div>
<div class="bg-orb" aria-hidden="true"></div>

<div class="page-shell">

  <div class="topbar">
    <a href="{{ route('staff.login') }}" class="topbar__back">
      <i class="fas fa-arrow-left"></i> Retour à la connexion
    </a>
  </div>

  <div class="page-wrap">
    <div class="card">

      <div class="icon-badge">
        <i class="fas fa-shield-halved"></i>
      </div>

      <h1 class="card-title">Mot de passe oublié ?</h1>
      <p class="card-sub">Entrez l'adresse email de votre compte administrateur. Nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

      @if(session('sent'))

        <div class="success-box">
          <i class="fas fa-circle-check"></i>
          <p>Lien envoyé ! Vérifiez votre boîte mail<br><strong>et suivez les instructions.</strong></p>
        </div>
        <a href="{{ route('staff.login') }}" class="fbtn" style="display:flex">
          <i class="fas fa-arrow-left"></i> Retour à la connexion
        </a>

      @else

        @if($errors->has('email'))
        <div class="ferr">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ $errors->first('email') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('staff.password.email') }}"
              onsubmit="this.querySelector('button[type=submit]').disabled=true">
          @csrf

          <div class="fgrp">
            <label class="flabel" for="email">Adresse email</label>
            <div class="frel">
              <i class="fas fa-envelope ficon"></i>
              <input type="email" id="email" name="email"
                     class="finput {{ $errors->has('email') ? 'err' : '' }}"
                     value="{{ old('email') }}"
                     @php
                         // Domaine issu de l'email du site configure en admin (pas de marque figee).
                         $phAdmin = 'admin@' . (\Illuminate\Support\Str::after(site_email(), '@') ?: request()->getHost());
                     @endphp
                     placeholder="{{ $phAdmin }}"
                     autocomplete="email" required>
            </div>
          </div>

          <button type="submit" class="fbtn">
            <i class="fas fa-paper-plane"></i>
            Envoyer le lien
          </button>
        </form>

        <div class="back-link">
          Vous vous souvenez ? <a href="{{ route('staff.login') }}">Se connecter</a>
        </div>

      @endif

    </div>
  </div>

  <div class="pg-foot">
    &copy; {{ date('Y') }} ' . site_name() . ' &nbsp;·&nbsp;
    <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;·&nbsp;
    <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
  </div>

</div>
</body>
</html>
