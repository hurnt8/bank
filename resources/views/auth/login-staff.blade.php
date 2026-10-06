@extends('layouts.auth-flow')
@section('title', __('auth.staff_login_title'))

@section('aside')
  <h2 class="aside__title" style="margin-top:0">{!! __('auth.staff_brand_title') !!}</h2>
  <p class="aside__text" style="margin-bottom:1.25rem">{{ __('auth.staff_brand_sub') }}</p>
  <ul class="aside__list">
    <li><i class="fas fa-shield-halved"></i>{{ __('auth.staff_restricted') }}</li>
    <li><i class="fas fa-triangle-exclamation"></i>{{ __('auth.staff_notice') }}</li>
  </ul>
@endsection

@push('styles')
<style>
.frow{display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap;margin:.35rem 0 1.4rem}
.fcheck{display:flex;align-items:center;gap:.5rem}
.fcheck input{width:18px;height:18px;accent-color:var(--accent);cursor:pointer}
.fcheck label{font-size:.8rem;color:var(--sub);cursor:pointer}
.fforgot{font-size:.8rem;font-weight:600;color:var(--accent)}
.fforgot:hover{text-decoration:underline}
</style>
@endpush

@section('content')
  <div class="card-head">
    <h1 class="card-title">{{ __('auth.submit_staff') }}</h1>
    <p class="card-sub">{{ __('auth.staff_login_sub') }}</p>
  </div>

  <div class="msg msg--info"><i class="fas fa-lock"></i><span>{{ __('auth.staff_restricted') }}</span></div>

  @if($errors->any())
  <div class="msg msg--err" role="alert"><i class="fas fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
  @endif

  @php
    // La partie locale reste traduite ; le domaine suit l'email du site configuré en admin.
    $phLocal  = \Illuminate\Support\Str::before(__('auth.email_ph_staff'), '@');
    $phDomain = \Illuminate\Support\Str::after(site_email(), '@') ?: request()->getHost();
  @endphp

  <form id="staff-login-form" action="{{ route('staff.login.submit') }}" method="POST" novalidate>
    @csrf

    <div class="fgrp">
      <label class="flabel" for="email">{{ __('auth.email_staff') }}</label>
      <div class="frel"><i class="fas fa-envelope ficon"></i>
        <input type="email" id="email" name="email" class="finput {{ $errors->has('email') ? 'err' : '' }}"
               value="{{ old('email') }}" placeholder="{{ $phLocal . '@' . $phDomain }}" autocomplete="email" inputmode="email" required>
      </div>
    </div>

    <div class="fgrp">
      <label class="flabel" for="password">{{ __('auth.password_label') }}</label>
      <div class="frel has-eye"><i class="fas fa-lock ficon"></i>
        <input type="password" id="password" name="password" class="finput {{ $errors->has('password') ? 'err' : '' }}"
               placeholder="••••••••" autocomplete="current-password" required>
        <button type="button" class="feye" onclick="tglPwd(this)" aria-label="{{ __('onboarding.show_password') }}"><i class="fas fa-eye"></i></button>
      </div>
    </div>

    <div class="frow">
      <div class="fcheck"><input type="checkbox" id="remember" name="remember"><label for="remember">{{ __('auth.remember_staff') }}</label></div>
      <a href="{{ route('staff.password.request') }}" class="fforgot">{{ __('auth.forgot_password') }}</a>
    </div>

    <button type="submit" id="submit-btn" class="fbtn"><i class="fas fa-unlock-alt"></i><span id="btn-text">{{ __('auth.submit_staff') }}</span></button>
  </form>
@endsection

@push('scripts')
<script>
/* Si la page reste ouverte trop longtemps, la session (et le token CSRF) expire côté serveur.
   On recharge avant que le submit n'échoue de façon confuse. */
(function () {
  var LOADED = Date.now(), STALE_MS = 100 * 60 * 1000; // marge sous SESSION_LIFETIME (120 min)
  var stale = function () { return Date.now() - LOADED > STALE_MS; };
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible' && stale()) window.location.reload();
  });
  var form = document.getElementById('staff-login-form');
  form.addEventListener('submit', function (e) {
    if (stale()) { e.preventDefault(); window.location.reload(); return; }
    document.getElementById('submit-btn').disabled = true;
    document.getElementById('btn-text').innerHTML = '<span class="spin"></span>';
  });
})();
</script>
@endpush
