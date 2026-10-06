@extends('layouts.auth-flow')
@section('title', __('onboarding.forgot_title'))

@section('aside')
  <h2 class="aside__title" style="margin-top:0">{{ __('onboarding.recovery_aside_title') }}</h2>
  <ul class="aside__list">
    <li><i class="fas fa-envelope-circle-check"></i>{{ __('onboarding.recovery_aside_1') }}</li>
    <li><i class="fas fa-key"></i>{{ __('onboarding.recovery_aside_2') }}</li>
    <li><i class="fas fa-right-to-bracket"></i>{{ __('onboarding.recovery_aside_3') }}</li>
  </ul>
@endsection

@section('content')
  <div class="card-head">
    <h1 class="card-title">{{ __('onboarding.forgot_title') }}</h1>
    <p class="card-sub">{{ __('onboarding.forgot_sub') }}</p>
  </div>

  @if(session('sent'))
    <div class="msg msg--ok" role="status"><i class="fas fa-circle-check"></i><span>{{ __('onboarding.forgot_sent') }}</span></div>
    <a href="{{ route('login') }}" class="fbtn"><i class="fas fa-arrow-left"></i>{{ __('onboarding.back_login') }}</a>
  @else
    @if($errors->has('email'))
    <div class="msg msg--err" role="alert"><i class="fas fa-circle-exclamation"></i><span>{{ $errors->first('email') }}</span></div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" onsubmit="this.querySelector('button[type=submit]').disabled=true">
      @csrf
      <div class="fgrp">
        <label class="flabel" for="email">{{ __('onboarding.label_email') }}</label>
        <div class="frel"><i class="fas fa-envelope ficon"></i>
          <input type="email" id="email" name="email" class="finput {{ $errors->has('email') ? 'err' : '' }}"
                 value="{{ old('email') }}" placeholder="{{ __('onboarding.ph_email') }}" autocomplete="email" inputmode="email" required>
        </div>
      </div>
      <button type="submit" class="fbtn"><i class="fas fa-paper-plane"></i>{{ __('onboarding.send_link') }}</button>
    </form>

    <p class="alt-link">{{ __('onboarding.remembered') }} <a href="{{ route('login') }}">{{ __('onboarding.login_link') }}</a></p>
  @endif
@endsection
