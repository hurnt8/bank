@extends('layouts.app')
@section('title', __('menu.faq'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.faq')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.faq')</li>
            </ul>
        </div>
    </div>
</div>

{{-- FAQ --}}
<section class="py-24 bg-white">
    <div class="container">
        <div class="row gutter-y-50 align-items-start">

            {{-- Sticky sidebar --}}
            <div class="col-lg-4 d-none d-lg-block wow fadeInLeft" data-wow-duration="900ms">
                <div style="position:sticky;top:110px;">
                    <img src="{{ asset('assets/images/resources/why-choose-1-1.jpg') }}"
                         alt="FAQ"
                         style="width:100%;border-radius:var(--radius-xl);height:360px;object-fit:cover;box-shadow:var(--shadow-hover);">
                    <div class="contact-widget mt-4">
                        <div class="contact-widget__icon"><i class="fas fa-headset"></i></div>
                        <h4>Besoin d'aide ?</h4>
                        <p>Notre équipe répond à toutes vos questions, du lundi au samedi.</p>
                        <a href="tel:{{ site_phone_href() }}" class="contact-widget__phone">{{ site_phone() }}</a>
                        <a href="{{ route('contact', ['locale' => $locale]) }}"
                           class="btn-primary w-100 justify-content-center">
                            <i class="fas fa-envelope"></i> @lang('menu.contact')
                        </a>
                    </div>
                </div>
            </div>

            {{-- FAQ accordions (Alpine.js) --}}
            <div class="col-lg-8 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="100ms">
                <div class="section-label mb-2">@lang('menu.faq')</div>
                <h2 class="section-title mb-8">Questions fréquentes</h2>

                @php
                $bankingFaqs = __('banking.faqs');
                $types = ['personal_loan','home_loan','auto_loan','business_loan','study_loan','bike_loan'];
                @endphp

                @if (is_array($bankingFaqs) && isset($bankingFaqs['question1']))
                <div class="faq-category-title">{{ __('banking.section_title') }}</div>
                <div x-data="{ open: 1 }" class="mb-6">
                    @for ($q = 1; $q <= 3; $q++)
                    @if (isset($bankingFaqs['question' . $q]))
                    <div class="accordion-item mb-1" style="border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-card);">
                        <button type="button"
                                class="faq-btn"
                                :class="open === {{ $q }} ? 'is-open' : ''"
                                @click="open = (open === {{ $q }}) ? null : {{ $q }}">
                            <span>{{ $bankingFaqs['question' . $q] }}</span>
                            <span class="faq-btn__icon">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </button>
                        <div class="faq-body"
                             x-show="open === {{ $q }}"
                             x-transition:enter="transition ease-out duration-250"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="{{ $q !== 1 ? 'display:none' : '' }}">
                            {{ $bankingFaqs['answer' . $q] }}
                        </div>
                    </div>
                    @endif
                    @endfor
                </div>
                @endif

                @foreach ($types as $type)
                @php $faqs = __('loan.' . $type . '.details.faqs'); @endphp
                @if (is_array($faqs) && isset($faqs['question1']))

                <div class="faq-category-title">{{ __('loan.' . $type . '.section_title') }}</div>

                {{-- Alpine scope: one open item per category, default first open --}}
                <div x-data="{ open: 1 }" class="mb-6">
                    @for ($q = 1; $q <= 3; $q++)
                    @if (isset($faqs['question' . $q]))
                    @php $qn = $q; @endphp
                    <div class="accordion-item mb-1" style="border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-card);">
                        <button type="button"
                                class="faq-btn"
                                :class="open === {{ $qn }} ? 'is-open' : ''"
                                @click="open = (open === {{ $qn }}) ? null : {{ $qn }}">
                            <span>{{ $faqs['question' . $q] }}</span>
                            <span class="faq-btn__icon">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </button>
                        <div class="faq-body"
                             x-show="open === {{ $qn }}"
                             x-transition:enter="transition ease-out duration-250"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="{{ $q !== 1 ? 'display:none' : '' }}">
                            {{ $faqs['answer' . $q] }}
                        </div>
                    </div>
                    @endif
                    @endfor
                </div>

                @endif
                @endforeach
            </div>

        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="container">
        <div class="row align-items-center gutter-y-30">
            <div class="col-lg-8 wow fadeInLeft" data-wow-duration="900ms">
                <div class="section-label" style="color:var(--accent);">{{ __('home.faq_cta.label') }}</div>
                <h2 class="section-title section-title--white mb-2">
                    {{ __('home.faq_cta.title') }}
                </h2>
                <p class="section-sub section-sub--white">
                    {{ __('home.faq_cta.text') }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end wow fadeInRight" data-wow-duration="900ms" data-wow-delay="100ms">
                <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                    <i class="fas fa-envelope"></i> @lang('menu.contact')
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
