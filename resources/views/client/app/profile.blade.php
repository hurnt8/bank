@extends('layouts.client-app')
@section('title', __('app.profile_title') . ' — ' . site_name())
@section('page_title', __('app.profile_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('main_class', 'ca-main--narrow')
@section('content')

{{-- Header profil --}}
<div class="ca-profile-header">
  <div class="ca-profile-avatar" style="background:url('/images/avatar.jpg') center/cover no-repeat;border:3px solid rgba(27,138,122,.4)"></div>
  <div class="ca-profile-name">{{ $user->name }}</div>
  <div class="ca-profile-badge">
    <i class="fas fa-crown" style="font-size:.65rem"></i>
    {{ __('app.premium_member') }}
  </div>
  <div class="ca-profile-since">
    {{ __('app.member_since') }} {{ $user->created_at->format('F Y') }}
  </div>
</div>

{{-- Balance rapide --}}
<a href="{{ route('client.app.movements') }}" style="text-decoration:none;display:block;margin:0 1.25rem .875rem">
<div style="background:linear-gradient(135deg,#1B4976,#0D2E52);border-radius:var(--ca-radius-md);padding:1rem 1.25rem;border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:space-between">
  <div>
    <div style="font-size:.65rem;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.25rem">{{ __('app.balance') }}</div>
    <div style="font-family:'Space Grotesk',sans-serif;font-size:1.5rem;font-weight:800;color:#fff">
      {{ $user->currency ?? \App\Models\Currency::default() }} {{ number_format((float)$user->balance, 2, ',', ' ') }}
    </div>
  </div>
  <div style="font-size:.75rem;color:rgba(255,255,255,.45);display:flex;align-items:center;gap:.35rem">
    {{ __('app.movements_title') }} <i class="fas fa-chevron-right" style="font-size:.6rem"></i>
  </div>
</div>
</a>

{{-- Parametres du compte --}}
<div class="ca-settings-group">
  <div class="ca-settings-label">{{ __('app.account_settings') }}</div>
  <div class="ca-settings-list">
    <div class="ca-settings-item">
      <div class="ca-settings-item__icon" style="background:rgba(27,138,122,.18);color:var(--ca-teal-l)">
        <i class="fas fa-user"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.personal_info') }}</div>
        <div class="ca-settings-item__sub">{{ $user->email }}</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </div>
    <a href="{{ route('client.app.payment-methods') }}" class="ca-settings-item" style="text-decoration:none">
      <div class="ca-settings-item__icon" style="background:rgba(220,190,135,.18);color:var(--ca-accent-l)">
        <i class="fas fa-credit-card"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">IBAN</div>
        <div class="ca-settings-item__sub">{{ $user->bankAccount ? $user->bankAccount->maskedIban() : __('app.not_configured') }}</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </a>
    <a href="{{ route('client.app.invoices') }}" class="ca-settings-item" style="text-decoration:none">
      <div class="ca-settings-item__icon" style="background:rgba(96,165,250,.15);color:#60a5fa">
        <i class="fas fa-file-invoice"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.invoices_title') }}</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </a>
  </div>
</div>

{{-- Actions rapides --}}
<div class="ca-settings-group">
  <div class="ca-settings-label">{{ __('app.account_settings') }}</div>
  <div class="ca-settings-list">
    <a href="{{ route('client.app.profile.edit') }}" class="ca-settings-item" style="text-decoration:none">
      <div class="ca-settings-item__icon" style="background:rgba(27,138,122,.18);color:var(--ca-teal-l)">
        <i class="fas fa-pen"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.edit_profile') }}</div>
        <div class="ca-settings-item__sub">{{ $user->name }}</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </a>
    <a href="{{ route('client.app.profile.password') }}" class="ca-settings-item" style="text-decoration:none">
      <div class="ca-settings-item__icon" style="background:rgba(220,190,135,.18);color:var(--ca-accent-l)">
        <i class="fas fa-lock"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.change_password') }}</div>
        <div class="ca-settings-item__sub">••••••••</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </a>
    <a href="{{ route('client.app.notifications') }}" class="ca-settings-item" style="text-decoration:none">
      <div class="ca-settings-item__icon" style="background:rgba(74,158,255,.18);color:var(--ca-blue)">
        <i class="fas fa-bell"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.notifications_title') }}</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </a>
    <a href="{{ route('client.app.support') }}" class="ca-settings-item" style="text-decoration:none">
      <div class="ca-settings-item__icon" style="background:rgba(139,92,246,.18);color:var(--ca-purple)">
        <i class="fas fa-headset"></i>
      </div>
      <div class="ca-settings-item__text">
        @php $supportUnread = \App\Models\SupportMessage::where('client_id', Auth::id())->where('sender_type','admin')->whereNull('read_at')->count(); @endphp
        <div class="ca-settings-item__label" style="display:flex;align-items:center;gap:.5rem">
          Support
          @if($supportUnread > 0)
          <span style="min-width:18px;height:18px;padding:0 5px;border-radius:999px;background:var(--ca-purple);color:#fff;font-size:.6rem;font-weight:800;display:inline-flex;align-items:center;justify-content:center">{{ $supportUnread }}</span>
          @endif
        </div>
        <div class="ca-settings-item__sub">Contacter votre conseiller</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </a>
  </div>
</div>

{{-- Securite et preferences --}}
<div class="ca-settings-group">
  <div class="ca-settings-label">{{ __('app.security') }}</div>
  <div class="ca-settings-list">

    {{-- Notifications toggle --}}
    <div class="ca-settings-item" x-data="togglePref()" :style="blocked ? 'opacity:.45;pointer-events:none' : ''">
      <div class="ca-settings-item__icon" style="background:rgba(74,158,255,.18);color:var(--ca-blue)">
        <i class="fas fa-bell" x-show="!loading"></i>
        <i class="fas fa-spinner fa-spin" x-show="loading" style="font-size:.85rem"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.notifications') }}</div>
        <div class="ca-settings-item__sub" x-show="blocked" style="color:var(--ca-negative);font-size:.68rem">Bloquées dans les paramètres du navigateur</div>
      </div>
      <div class="ca-settings-item__right">
        <label class="ca-toggle" @click.prevent="toggle()">
          <input type="checkbox" :checked="on" readonly tabindex="-1">
          <div class="ca-toggle__track"></div>
          <div class="ca-toggle__thumb"></div>
        </label>
      </div>
    </div>

    {{-- Dark / Light mode --}}
    <div class="ca-settings-item" x-data="themeToggle()">
      <div class="ca-settings-item__icon" style="background:rgba(139,92,246,.18);color:var(--ca-purple)">
        <i class="fas fa-moon" x-show="isDark"></i>
        <i class="fas fa-sun"  x-show="!isDark"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.dark_mode') }}</div>
        <div class="ca-settings-item__sub" x-text="isDark ? 'Dark' : 'Light'"></div>
      </div>
      <div class="ca-settings-item__right">
        <label class="ca-toggle">
          <input type="checkbox" :checked="isDark" @change="toggle()">
          <div class="ca-toggle__track"></div>
          <div class="ca-toggle__thumb"></div>
        </label>
      </div>
    </div>

    {{-- Langue --}}
    <div class="ca-settings-item" x-data="langMenu()" @click.stop>
      <div class="ca-settings-item__icon" style="background:rgba(245,158,11,.18);color:var(--ca-amber)">
        <i class="fas fa-globe"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.language_pref') }}</div>
        <div class="ca-settings-item__sub">{{ \App\Models\Language::enabledList()->firstWhere('code', app()->getLocale())->native_name ?? app()->getLocale() }}</div>
      </div>
      <div class="ca-settings-item__right" @click="toggle()">
        <i class="fas fa-chevron-right"></i>
      </div>
      <div class="ca-lang-pop" x-show="open" @click.outside="close()" x-transition
           style="position:fixed;bottom:calc(var(--ca-nav-h) + 1rem);left:1.25rem;right:1.25rem;background:var(--ca-bg4);border:1px solid var(--ca-border);border-radius:var(--ca-radius-md);overflow:hidden;z-index:600;box-shadow:0 -8px 32px rgba(0,0,0,.4)">
        @foreach(\App\Models\Language::enabledList() as $lang)
        @php [$lc, $label] = [$lang->code, $lang->native_name]; @endphp
        <form method="POST" action="{{ route('client.app.locale') }}">
          @csrf
          <input type="hidden" name="locale" value="{{ $lc }}">
          <button type="submit"
                  style="width:100%;padding:.875rem 1.25rem;background:none;border:none;border-bottom:1px solid var(--ca-border-2);color:{{ app()->getLocale()===$lc ? 'var(--ca-teal-l)' : 'var(--ca-text-2)' }};font-size:.875rem;text-align:left;cursor:pointer;font-family:inherit;font-weight:{{ app()->getLocale()===$lc ? '700' : '400' }};display:flex;justify-content:space-between;align-items:center">
            {{ $label }}
            @if(app()->getLocale() === $lc)
            <i class="fas fa-check" style="color:var(--ca-teal-l)"></i>
            @endif
          </button>
        </form>
        @endforeach
      </div>
    </div>

  </div>
</div>

{{-- Mon Conseiller --}}
@php
  $advisor = $user->created_by ? \App\Models\User::find($user->created_by) : null;
  if (!$advisor) $advisor = \App\Models\User::role('super-admin')->first();
@endphp
@if($advisor)
<div class="ca-settings-group">
  <div class="ca-settings-label">Mon conseiller</div>
  <div style="margin:0 1.25rem;background:var(--ca-bg3);border:1px solid var(--ca-border);border-radius:var(--ca-radius-md);padding:1rem 1.125rem;display:flex;align-items:center;gap:.875rem">
    <div style="width:46px;height:46px;border-radius:50%;flex-shrink:0;
      background:linear-gradient(135deg,rgba(27,138,122,.35),rgba(27,138,122,.12));
      border:1.5px solid rgba(27,138,122,.3);
      display:flex;align-items:center;justify-content:center;
      font-size:1rem;font-weight:800;color:var(--ca-teal-l)">
      {{ strtoupper(substr($advisor->name,0,1)) }}
    </div>
    <div style="flex:1;min-width:0">
      <div style="font-size:.875rem;font-weight:700;color:var(--ca-text)">{{ $advisor->name }}</div>
      <div style="font-size:.7rem;color:var(--ca-text-3);margin-top:.1rem">Votre conseiller {{ site_name() }}</div>
    </div>
    @if($advisor->phone)
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $advisor->phone) }}"
       target="_blank" rel="noopener"
       style="display:flex;flex-direction:column;align-items:center;gap:.2rem;text-decoration:none;flex-shrink:0">
      <div style="width:44px;height:44px;border-radius:50%;background:#25D366;
        display:flex;align-items:center;justify-content:center;font-size:1.25rem;color:#fff">
        <i class="fab fa-whatsapp"></i>
      </div>
      <span style="font-size:.6rem;color:var(--ca-text-3);font-weight:600">WhatsApp</span>
    </a>
    @else
    <div style="font-size:.72rem;color:var(--ca-text-3);text-align:center;line-height:1.4">
      <i class="fas fa-phone-slash" style="display:block;margin-bottom:.25rem;opacity:.4"></i>
      Non défini
    </div>
    @endif
  </div>
</div>
@endif

{{-- Deconnexion --}}
<div class="ca-settings-group">
  <div class="ca-settings-list">
    <form data-confirm="{{ __('app.confirm_logout') }}" data-confirm-title="{{ __('app.logout') }}" data-confirm-danger="1" method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="ca-settings-item" style="width:100%;background:none;border:none;cursor:pointer">
        <div class="ca-settings-item__icon" style="background:rgba(255,90,90,.15);color:var(--ca-negative)">
          <i class="fas fa-sign-out-alt"></i>
        </div>
        <div class="ca-settings-item__text">
          <div class="ca-settings-item__label" style="color:var(--ca-negative)">{{ __('app.logout') }}</div>
        </div>
      </button>
    </form>
  </div>
</div>

{{-- Version --}}
<div style="text-align:center;padding:1.5rem;font-size:.7rem;color:var(--ca-text-3)">
 {{ site_name() }} Mobile &nbsp;&bull;&nbsp; v2.0.0
</div>

@endsection
