@php $locale = app()->getLocale(); $currentRoute = Route::currentRouteName(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.' . $menuKey)</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li><a href="{{ route('services', ['locale' => $locale]) }}">@lang('menu.services')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.' . $menuKey)</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row g-4 gutter-y-50 align-items-start">

            {{-- Sidebar (2e sur mobile, 1re sur desktop) --}}
            <div class="col-lg-4 order-2 order-lg-1 wow fadeInLeft" data-wow-duration="900ms">
                <div style="position:sticky;top:110px;">
                    @include('partials.service_sidebar')
                </div>
            </div>

            {{-- Main content (1er sur mobile, 2e sur desktop) --}}
            <div class="col-lg-8 order-1 order-lg-2 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="100ms">

                <div class="service-detail__thumbnail mb-6">
                    <img src="{{ asset('assets/images/services/' . $image) }}"
                         alt="@lang('menu.' . $menuKey)">
                </div>

                <h2 class="service-detail__title">@lang('loan.' . $loanKey . '.section_title')</h2>
                <p class="service-detail__text">{{ __('loan.' . $loanKey . '.description') }}</p>

                <div class="service-detail__advantages mb-6">
                    <h4 class="service-detail__advantages__title">Avantages</h4>
                    <ul class="advantage-list">
                        @foreach ([1,2,3,4] as $n)
                        <li>
                            <i class="fas fa-check advantage-list__icon"></i>
                            {{ __('loan.' . $loanKey . '.details.advantage' . $n) }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <h3 class="service-detail__title" style="font-size:1.25rem;">
                    {{ __('loan.' . $loanKey . '.details.faq_title') }}
                </h3>

                <div x-data="{ open: 1 }">
                    @foreach ([1,2,3] as $n)
                    <div class="accordion-item mb-1" style="border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-card);">
                        <button type="button"
                                class="faq-btn"
                                :class="open === {{ $n }} ? 'is-open' : ''"
                                @click="open = (open === {{ $n }}) ? null : {{ $n }}">
                            <span>{{ __('loan.' . $loanKey . '.details.faqs.question' . $n) }}</span>
                            <span class="faq-btn__icon">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </button>
                        <div class="faq-body"
                             x-show="open === {{ $n }}"
                             x-transition:enter="transition ease-out duration-250"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="{{ $n !== 1 ? 'display:none' : '' }}">
                            {{ __('loan.' . $loanKey . '.details.faqs.answer' . $n) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="service-detail__cta">
                    <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-outline btn-outline--lg">
                        <i class="fas fa-envelope"></i> @lang('menu.contact')
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>

{{-- Calculator strip --}}
<section class="calc-section py-16">
    <div class="container">
        <div class="row g-4 gutter-y-50 align-items-center">
            <div class="col-lg-6 wow fadeInLeft" data-wow-duration="900ms">
                <div class="section-label" style="color:var(--accent);">Simulation</div>
                <h2 class="section-title section-title--white mb-4">@lang('home.simulate.sectitle')</h2>
                <p class="section-sub section-sub--white">{{ __('loan.' . $loanKey . '.description') }}</p>
            </div>
            <div class="col-lg-6 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                @include('partials.simulate')
            </div>
        </div>
    </div>
</section>
