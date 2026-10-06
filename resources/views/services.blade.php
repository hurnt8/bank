@extends('layouts.app')
@section('title', __('menu.services'))

@section('content')
@php
    $locale = app()->getLocale();
    $siteContact = \App\Models\SiteContact::current();
@endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.services')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.services')</li>
            </ul>
        </div>
    </div>
</div>

{{-- Services bancaires --}}
<section class="py-24 bg-white">
    <div class="container">
        <div class="text-center mb-14">
            <div class="section-label justify-content-center">{{ __('home.services.sectagline') }}</div>
            <h2 class="section-title">{{ __('home.services.sectitle') }}</h2>
        </div>

        @php
        $bankingServices = [
            ['img' => 'service-1-1.jpg', 'label' => 'menu.banking_account',  'icon' => 'fas fa-wallet',          'desc' => __('home.banking.account_desc')],
            ['img' => 'service-1-2.jpg', 'label' => 'menu.banking_card',     'icon' => 'fas fa-credit-card',     'desc' => __('home.banking.card_desc')],
            ['img' => 'service-1-3.jpg', 'label' => 'menu.banking_transfer', 'icon' => 'fas fa-money-bill-transfer', 'desc' => __('home.banking.transfer_desc')],
        ];
        @endphp

        <div class="row g-4 gutter-y-30">
            @foreach ($bankingServices as $i => $svc)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ $i * 80 }}ms">
                <div class="service-card">
                    <div class="service-card__image">
                        <img src="{{ asset('assets/images/services/' . $svc['img']) }}" alt="@lang($svc['label'])">
                        <div class="service-card__image__overlay"></div>
                    </div>
                    <div class="service-card__body">
                        <div class="service-card__icon"><i class="{{ $svc['icon'] }}"></i></div>
                        <h3 class="service-card__title">
                            <a href="{{ route('signup', ['locale' => $locale]) }}">@lang($svc['label'])</a>
                        </h3>
                        <p class="service-card__desc">{{ $svc['desc'] }}</p>
                        <a href="{{ route('signup', ['locale' => $locale]) }}" class="service-card__link">
                            @lang('menu.open_account') <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Prêts & financements (activité secondaire) --}}
<section class="py-24" style="background:var(--cream);">
    <div class="container">
        <div class="text-center mb-14">
            <div class="section-label justify-content-center">{{ __('menu.banking_loans') }}</div>
            <h2 class="section-title">{{ __('home.discover_our_loan_services') }}</h2>
        </div>

        @php
        $loanServices = [
            ['route' => 'services.personal', 'img' => 'service-3-1.jpg', 'label' => 'menu.personal',  'type' => 'personal_loan', 'icon' => 'fas fa-user-tie'],
            ['route' => 'services.home',     'img' => 'service-3-3.jpg', 'label' => 'menu.home_loan', 'type' => 'home_loan',     'icon' => 'fas fa-home'],
            ['route' => 'services.auto',     'img' => 'service-3-5.jpg', 'label' => 'menu.auto',      'type' => 'auto_loan',     'icon' => 'fas fa-car'],
            ['route' => 'services.business', 'img' => 'service-3-4.jpg', 'label' => 'menu.business',  'type' => 'business_loan', 'icon' => 'fas fa-briefcase'],
            ['route' => 'services.study',    'img' => 'service-3-2.jpg', 'label' => 'menu.study',     'type' => 'study_loan',    'icon' => 'fas fa-graduation-cap'],
            ['route' => 'services.bike',     'img' => 'service-3-6.jpg', 'label' => 'menu.bike',      'type' => 'bike_loan',     'icon' => 'fas fa-bicycle'],
        ];
        @endphp

        <div class="row g-4 gutter-y-30">
            @foreach ($loanServices as $i => $svc)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ $i * 80 }}ms">
                <div class="service-card">
                    <div class="service-card__image">
                        <img src="{{ asset('assets/images/services/' . $svc['img']) }}" alt="@lang($svc['label'])">
                        <div class="service-card__image__overlay"></div>
                    </div>
                    <div class="service-card__body">
                        <div class="service-card__icon"><i class="{{ $svc['icon'] }}"></i></div>
                        <h3 class="service-card__title">
                            <a href="{{ route($svc['route'], ['locale' => $locale]) }}">@lang($svc['label'])</a>
                        </h3>
                        <p class="service-card__desc">
                            {{ Str::limit(__('loan.' . $svc['type'] . '.description'), 120) }}
                        </p>
                        <a href="{{ route($svc['route'], ['locale' => $locale]) }}" class="service-card__link">
                            @lang('menu.read_more') <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="container">
        <div class="row align-items-center gutter-y-30">
            <div class="col-lg-7 wow fadeInLeft" data-wow-duration="900ms">
                <div class="section-label" style="color:var(--accent);">{{ __('home.services.sectagline') }}</div>
                <h2 class="section-title section-title--white mb-2">{{ __('home.services.cta_title') }}</h2>
                <p class="section-sub section-sub--white">{{ __('home.services.cta_text') }}</p>
            </div>
            <div class="col-lg-5 text-lg-end wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="d-flex flex-wrap justify-content-lg-end gap-3">
                    <a href="{{ route('loan',    ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                        <i class="fas fa-file-signature"></i> @lang('menu.loan')
                    </a>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-outline-white">
                        <i class="fas fa-envelope"></i> @lang('menu.contact')
                    </a>
                </div>

                <div class="d-flex flex-wrap justify-content-lg-end gap-4 mt-5">
                    @if($siteContact->address_1)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt" style="color:var(--accent);font-size:.9rem;"></i>
                        <span style="color:rgba(255,255,255,.6);font-size:.875rem;">{{ $siteContact->address_1 }}</span>
                    </div>
                    @endif
                    @if($siteContact->phone_1)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-phone-alt" style="color:var(--accent);font-size:.9rem;"></i>
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $siteContact->phone_1) }}" style="color:rgba(255,255,255,.6);font-size:.875rem;">{{ $siteContact->phone_1 }}</a>
                    </div>
                    @endif
                    @if($siteContact->email)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-envelope" style="color:var(--accent);font-size:.9rem;"></i>
                        <a href="mailto:{{ $siteContact->email }}" style="color:rgba(255,255,255,.6);font-size:.875rem;">{{ $siteContact->email }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
