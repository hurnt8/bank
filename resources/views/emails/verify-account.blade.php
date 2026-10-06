@php $locale = $user->locale ?? 'fr'; @endphp
<x-email-layout
    :title="__('onboarding.mail_title', [], $locale)"
    subtitle="{{ site_name() }}"
    :footerNote="__('onboarding.mail_footer', [], $locale)"
    :locale="$locale"
>

  <p class="greeting">
    {{ $user->name }},<br>
    {{ __('onboarding.mail_intro', ['site' => site_name()], $locale) }}
  </p>

  <div class="btn-wrap">
    <a href="{{ $activationUrl }}" class="btn">
      {{ __('onboarding.mail_btn', [], $locale) }}
    </a>
  </div>

  <p class="url-fallback">
    {{ __('onboarding.mail_fallback', [], $locale) }}<br>
    <a href="{{ $activationUrl }}">{{ $activationUrl }}</a>
  </p>

  <div class="alert alert-warn">
    <p>{{ __('onboarding.mail_notice', [], $locale) }}</p>
  </div>

</x-email-layout>
