@extends('layouts.auth-flow')
@section('title', __('onboarding.register_title'))
@section('card_class', 'card--wide')

@section('aside')
  <h2 class="aside__title" style="margin-top:0">{{ __('onboarding.aside_title') }}</h2>
  <ul class="aside__list">
    <li><i class="fas fa-user-plus"></i>{{ __('onboarding.aside_s1') }}</li>
    <li><i class="fas fa-envelope-circle-check"></i>{{ __('onboarding.aside_s2') }}</li>
    <li><i class="fas fa-id-card"></i>{{ __('onboarding.aside_s3') }}</li>
  </ul>
@endsection

@section('content')
  <div class="card-head">
    <h1 class="card-title">{{ __('onboarding.register_title') }}</h1>
    <p class="card-sub">{{ __('onboarding.register_sub') }}</p>
  </div>

  @if($errors->any())
  <div class="msg msg--err" role="alert"><i class="fas fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
  @endif

  <form id="register-form" method="POST" action="{{ route('signup.store') }}" novalidate>
    @csrf
    <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

    <div class="fgrid fgrid--2">
      <div class="fgrp span-2">
        <label class="flabel" for="name">{{ __('onboarding.label_name') }} *</label>
        <div class="frel"><i class="fas fa-user ficon"></i>
          <input class="finput {{ $errors->has('name') ? 'err' : '' }}" type="text" id="name" name="name" value="{{ old('name') }}"
                 placeholder="{{ __('onboarding.ph_name') }}" autocomplete="name" required>
        </div>
        @error('name')<span class="ferr-txt">{{ $message }}</span>@enderror
      </div>

      <div class="fgrp">
        <label class="flabel" for="email">{{ __('onboarding.label_email') }} *</label>
        <div class="frel"><i class="fas fa-envelope ficon"></i>
          <input class="finput {{ $errors->has('email') ? 'err' : '' }}" type="email" id="email" name="email" value="{{ old('email') }}"
                 placeholder="{{ __('onboarding.ph_email') }}" autocomplete="email" inputmode="email" required>
        </div>
        @error('email')<span class="ferr-txt">{{ $message }}</span>@enderror
      </div>

      <div class="fgrp">
        <label class="flabel" for="phone">{{ __('onboarding.label_phone') }} *</label>
        <div class="frel"><i class="fas fa-phone ficon"></i>
          <input class="finput {{ $errors->has('phone') ? 'err' : '' }}" type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                 placeholder="{{ __('onboarding.ph_phone') }}" autocomplete="tel" inputmode="tel" required>
        </div>
        @error('phone')<span class="ferr-txt">{{ $message }}</span>@enderror
      </div>

      <div class="fgrp">
        <label class="flabel" for="password">{{ __('onboarding.label_password') }} *</label>
        <div class="frel has-eye"><i class="fas fa-lock ficon"></i>
          <input class="finput {{ $errors->has('password') ? 'err' : '' }}" type="password" id="password" name="password"
                 placeholder="{{ __('onboarding.ph_password') }}" autocomplete="new-password" minlength="8" required>
          <button type="button" class="feye" onclick="tglPwd(this)" aria-label="{{ __('onboarding.show_password') }}"><i class="fas fa-eye"></i></button>
        </div>
        @error('password')<span class="ferr-txt">{{ $message }}</span>@else<span class="fhint">{{ __('onboarding.password_hint') }}</span>@enderror
      </div>

      <div class="fgrp">
        <label class="flabel" for="password_confirmation">{{ __('onboarding.label_password_confirm') }} *</label>
        <div class="frel has-eye"><i class="fas fa-lock ficon"></i>
          <input class="finput" type="password" id="password_confirmation" name="password_confirmation"
                 placeholder="{{ __('onboarding.ph_password_confirm') }}" autocomplete="new-password" required>
          <button type="button" class="feye" onclick="tglPwd(this)" aria-label="{{ __('onboarding.show_password') }}"><i class="fas fa-eye"></i></button>
        </div>
        <span class="ferr-txt" id="pwd-mismatch" style="display:none">{{ __('onboarding.password_mismatch') }}</span>
      </div>
    </div>

    <button type="submit" id="submit-btn" class="fbtn"><span id="btn-text">{{ __('onboarding.register_submit') }}</span></button>
  </form>

  <p class="alt-link">{{ __('onboarding.have_account') }} <a href="{{ route('login') }}">{{ __('onboarding.login_link') }}</a></p>
@endsection

@push('scripts')
<script>
(function () {
  var form = document.getElementById('register-form');
  var p1 = document.getElementById('password'), p2 = document.getElementById('password_confirmation');
  var warn = document.getElementById('pwd-mismatch');
  function check() {
    var bad = p2.value !== '' && p1.value !== p2.value;
    warn.style.display = bad ? 'block' : 'none';
    p2.classList.toggle('err', bad);
    return !bad;
  }
  p1.addEventListener('input', check); p2.addEventListener('input', check);
  form.addEventListener('submit', function (e) {
    if (!check()) { e.preventDefault(); return; }
    var btn = document.getElementById('submit-btn');
    btn.disabled = true;
    document.getElementById('btn-text').innerHTML = '<span class="spin"></span>';
  });
})();
</script>
@endpush
