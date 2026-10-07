@extends('layouts.app')
@section('title', __('menu.about'))

@section('content')
@php $locale = app()->getLocale(); @endphp

@push('styles')
<style>
.about-engage-card {
    display:flex; gap:1rem; padding:1rem 1.25rem;
    background:var(--cream); border-radius:12px;
    border-left:3px solid var(--accent); margin-bottom:.75rem;
}
.about-engage-icon {
    width:42px; height:42px; flex-shrink:0; border-radius:10px;
    background:var(--accent-pale); display:flex; align-items:center;
    justify-content:center; color:var(--accent-dark); font-size:1rem;
}
.about-engage-title { font-size:.875rem; font-weight:800; color:var(--navy); margin-bottom:.2rem; }
.about-engage-desc  { font-size:.78rem; color:#6b7280; margin:0; line-height:1.55; }

.about-page-partners-marquee {
    overflow:hidden;
    -webkit-mask-image:linear-gradient(to right, transparent, #000 8%, #000 92%, transparent);
    mask-image:linear-gradient(to right, transparent, #000 8%, #000 92%, transparent);
}
.about-page-partners-track {
    display:flex; align-items:center; width:max-content; gap:.5rem;
    animation:about-page-partners-scroll 60s linear infinite;
}
.about-page-partners-marquee:hover .about-page-partners-track { animation-play-state:paused; }
@keyframes about-page-partners-scroll {
    from { transform:translateX(0); }
    to   { transform:translateX(-50%); }
}
@media (prefers-reduced-motion: reduce) {
    .about-page-partners-track { animation:none; flex-wrap:wrap; width:100%; }
}
</style>
@endpush

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.about')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.about')</li>
            </ul>
        </div>
    </div>
</div>

{{-- About intro --}}
<section class="py-24 bg-white">
    <div class="container">
        <div class="row g-4 gutter-y-60 align-items-center">
            <div class="col-lg-6 wow fadeInLeft" data-wow-duration="900ms">
                <div class="about-image-wrap">
                    <img src="{{ asset('assets/images/about/about-1-1.jpg') }}"
                         alt="{{ site_name() }}" class="about-image-main">
                    <img src="{{ asset('assets/images/about/about-1-2.jpg') }}"
                         alt="" class="about-image-secondary"
                         style="width:38%;right:1rem;bottom:1rem;">
                    <div class="about-badge">
                        <span class="about-badge__number">15</span>
                        <span class="about-badge__label">{{ __('home.about.exptitle') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="section-label">{{ __('home.about.sectagline') }}</div>
                <h2 class="section-title">{{ __('home.about.sectitle') }}</h2>

                <p style="color:var(--gray-500);font-size:.9375rem;line-height:1.8;margin-bottom:1.5rem;">
                    {{ __('home.about.text2') }}
                </p>

                {{-- 3 engagements clés --}}
                <div class="about-engage-card">
                    <div class="about-engage-icon"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <div class="about-engage-title">{{ __('home.about.engage1_title') }}</div>
                        <p class="about-engage-desc">{{ __('home.about.engage1_desc') }}</p>
                    </div>
                </div>
                <div class="about-engage-card">
                    <div class="about-engage-icon"><i class="fas fa-bolt"></i></div>
                    <div>
                        <div class="about-engage-title">{{ __('home.about.engage2_title') }}</div>
                        <p class="about-engage-desc">{{ __('home.about.engage2_desc') }}</p>
                    </div>
                </div>
                <div class="about-engage-card" style="margin-bottom:1.5rem;">
                    <div class="about-engage-icon"><i class="fas fa-globe"></i></div>
                    <div>
                        <div class="about-engage-title">{{ __('home.about.engage3_title') }}</div>
                        <p class="about-engage-desc">{{ __('home.about.engage3_desc') }}</p>
                    </div>
                </div>

                {{-- Services proposés --}}
                <div style="margin-bottom:.5rem;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--navy);">
                    <i class="fas fa-tags" style="color:var(--accent);margin-right:.35rem;"></i>{{ __('home.services.sectitle') }}
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1.5rem;">
                    @foreach([
                        ['fas fa-wallet',              'menu.banking_account'],
                        ['fas fa-credit-card',         'menu.banking_card'],
                        ['fas fa-money-bill-transfer', 'menu.banking_transfer'],
                    ] as $t)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:999px;background:var(--cream);border:1px solid #e2ddd0;font-size:.75rem;font-weight:700;color:var(--navy);">
                        <i class="{{ $t[0] }}" style="color:var(--accent-dark);font-size:.7rem;"></i> @lang($t[1])
                    </span>
                    @endforeach
                </div>

                {{-- Partenaires --}}
                <div style="padding:.85rem 1.1rem;background:#f7f8fa;border:1px solid #eaecf0;border-radius:12px;margin-bottom:1.5rem;">
                    <div style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#9ca3af;margin-bottom:.75rem;">@lang('home.partners_title')</div>
                    <div class="about-page-partners-marquee">
                        <div class="about-page-partners-track">
                            @foreach (__('home.partners_list') as $bankName)
                            <span style="font-size:.72rem;font-weight:700;color:var(--navy);background:#fff;border:1px solid #e5e7eb;border-radius:999px;padding:.25rem .7rem;white-space:nowrap;flex-shrink:0;">{{ $bankName }}</span>
                            @endforeach
                            @foreach (__('home.partners_list') as $bankName)
                            <span style="font-size:.72rem;font-weight:700;color:var(--navy);background:#fff;border:1px solid #e5e7eb;border-radius:999px;padding:.25rem .7rem;white-space:nowrap;flex-shrink:0;" aria-hidden="true">{{ $bankName }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('signup', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                    <i class="fas fa-user-plus"></i> @lang('menu.open_account')
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section style="background:var(--navy);">
    <div class="container">
        <div class="row">
            @php
            $stats = [
                ['stop'=>'8500','suffix'=>'+','prefix'=>'', 'label'=> __('home.customer_satisfaction_rate')],
                ['stop'=>'5',    'suffix'=>' min','prefix'=>'','label'=> __('home.account_opening_time')],
                ['stop'=>'24',  'suffix'=>'h','prefix'=>'', 'label'=> __('home.average_approval_time')],
                ['stop'=>'15',   'suffix'=>'+','prefix'=>'', 'label'=> __('home.years_experience')],
            ];
            @endphp
            @foreach ($stats as $i => $stat)
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ $i*80 }}ms">
                <div class="stat-item {{ $i < 3 ? 'stat-item--sep' : '' }}">
                    <div class="stat-item__number">
                        @if($stat['prefix'])<span style="font-size:.6em;margin-right:2px;">{{ $stat['prefix'] }}</span>@endif
                        <span class="count-text" data-stop="{{ $stat['stop'] }}" data-speed="1500">{{ $stat['stop'] }}</span>
                        @if($stat['suffix'])<span style="font-size:.6em;margin-left:2px;">{{ $stat['suffix'] }}</span>@endif
                    </div>
                    <div class="stat-item__label">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why choose us --}}
<section class="py-24" style="background:var(--cream);">
    <div class="container">
        <div class="text-center mb-14">
            <div class="section-label justify-content-center">{{ __('home.why_us.sectagline') }}</div>
            <h2 class="section-title">{{ __('home.why_us.sectitle') }}</h2>
        </div>
        <div class="row g-4 gutter-y-30">
            @foreach ([1,2,3] as $r)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ ($r-1)*80 }}ms">
                <div class="card-glass p-8" style="padding:2rem;">
                    <div style="width:52px;height:52px;background:var(--accent-pale);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;color:var(--accent-dark);font-size:1.25rem;margin-bottom:1.25rem;">
                        <i class="fas fa-{{ $r===1 ? 'shield-alt' : ($r===2 ? 'bolt' : 'headset') }}"></i>
                    </div>
                    <h3 style="font-family:'Fraunces',serif;font-size:1.125rem;font-weight:700;color:var(--navy);margin-bottom:.625rem;">
                        {{ __('home.why_us.reasons.title' . $r) }}
                    </h3>
                    <p style="font-size:.875rem;color:var(--gray-500);line-height:1.75;margin:0;">
                        {{ __('home.why_us.reasons.desc' . $r) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
