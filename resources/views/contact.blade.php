@extends('layouts.app')
@section('title', __('menu.contact'))

@push('styles')
<style>[x-cloak]{display:none !important}</style>
@endpush

@section('content')
@php
    $locale = app()->getLocale();
    $siteContact = \App\Models\SiteContact::current();
    $socialLinks = \App\Models\SocialLink::where('is_visible', true)->orderBy('sort_order')->get();
    $addresses = collect([$siteContact->address_1, $siteContact->address_2, $siteContact->address_3])->filter();
    $phones    = collect([$siteContact->phone_1, $siteContact->phone_2])->filter();
@endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.contact')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.contact')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row g-4 gutter-y-40 align-items-start">
            {{-- Contact form --}}
            <div class="col-lg-7 wow fadeInLeft" data-wow-duration="900ms" x-data="{ submitting: false }">
                <div class="form-card">
                    <div class="section-label mb-2">{{ __('contact.form_title') }}</div>
                    <h2 class="section-title mb-6">{{ __('contact.detail_title') }}</h2>

                    @if (session('success'))
                        <div class="alert alert-success">{{ __('message.success_contact') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ __('message.error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}" @submit="submitting = true">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('contact.placeholder_name') }} *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                           placeholder="{{ __('contact.placeholder_name') }}" required>
                                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('contact.placeholder_email') }} *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                           placeholder="{{ __('contact.placeholder_email') }}" required>
                                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('contact.subject') }} *</label>
                                    <select name="subject" class="form-control" required>
                                        <option value="">— {{ __('contact.subject') }} —</option>
                                        <option value="Ouverture de compte" {{ old('subject')=='Ouverture de compte' ?'selected':'' }}>@lang('menu.banking_account')</option>
                                        <option value="Carte bancaire"      {{ old('subject')=='Carte bancaire'      ?'selected':'' }}>@lang('menu.banking_card')</option>
                                        <option value="Virements"           {{ old('subject')=='Virements'           ?'selected':'' }}>@lang('menu.banking_transfer')</option>
                                        <option value="Prêt personnel"  {{ old('subject')=='Prêt personnel'  ?'selected':'' }}>@lang('menu.personal')</option>
                                        <option value="Prêt immobilier" {{ old('subject')=='Prêt immobilier' ?'selected':'' }}>@lang('menu.home_loan')</option>
                                        <option value="Prêt commercial" {{ old('subject')=='Prêt commercial' ?'selected':'' }}>@lang('menu.business')</option>
                                        <option value="Prêt étudiant"   {{ old('subject')=='Prêt étudiant'   ?'selected':'' }}>@lang('menu.study')</option>
                                        <option value="Prêt auto"       {{ old('subject')=='Prêt auto'       ?'selected':'' }}>@lang('menu.auto')</option>
                                        <option value="Prêt moto"       {{ old('subject')=='Prêt moto'       ?'selected':'' }}>@lang('menu.bike')</option>
                                        <option value="Autre"           {{ old('subject')=='Autre'           ?'selected':'' }}>Autre</option>
                                    </select>
                                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Message *</label>
                                    <textarea name="message" class="form-control" rows="6"
                                              placeholder="{{ __('contact.placeholder_message') }}" required>{{ old('message') }}</textarea>
                                    @error('message')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-primary btn-primary--lg w-100 justify-content-center" :disabled="submitting">
                                    <i class="fas fa-spinner fa-spin" x-show="submitting" x-cloak></i>
                                    <i class="fas fa-paper-plane" x-show="!submitting"></i>
                                    <span x-text="submitting ? '{{ __('contact.sending') }}' : '{{ __('contact.button') }}'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Coordonnees : cartes empilees, sans photo, comme le modele --}}
            <div class="col-lg-5 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="contact-cards">

                    @if ($addresses->isNotEmpty())
                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <p class="contact-card__title">{{ __('contact.address_title') }}</p>
                            @foreach ($addresses as $adresse)
                            <p class="contact-card__value">{{ $adresse }}</p>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if ($phones->isNotEmpty())
                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <p class="contact-card__title">{{ __('contact.phone_title') }}</p>
                            @foreach ($phones as $telephone)
                            <p class="contact-card__value">
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $telephone) }}">{{ $telephone }}</a>
                            </p>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if ($siteContact->email)
                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <p class="contact-card__title">{{ __('contact.mail_title') }}</p>
                            <p class="contact-card__value">
                                <a href="mailto:{{ $siteContact->email }}">{{ $siteContact->email }}</a>
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-building"></i></div>
                        <div>
                            <p class="contact-card__title">{{ $siteContact->name }}</p>
                            <p class="contact-card__value contact-card__value--plain">{{ __('contact.detail_desc') }}</p>
                        </div>
                    </div>

                    @if ($socialLinks->isNotEmpty())
                    <div class="contact-cards__social">
                        @foreach ($socialLinks as $link)
                        <a href="{{ $link->url }}" aria-label="{{ $link->label }}"
                           @if(str_starts_with($link->url, 'http')) target="_blank" rel="noopener" @endif>
                            <i class="{{ $link->icon_class }}"></i>
                        </a>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</section>

@endsection
