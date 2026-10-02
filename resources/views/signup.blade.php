@extends('layouts.app')
@section('title', __('signup.title'))

@section('content')
@php
    $locale = app()->getLocale();
    $currenciesForForm = \App\Models\Currency::enabledList();
@endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">{{ __('signup.title') }}</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>{{ __('signup.title') }}</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="section-label mb-2">{{ __('signup.form_label') }}</div>
                    <h2 class="section-title mb-6">{{ __('signup.form_title') }}</h2>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('signup.store') }}">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $locale }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_name') }} *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_email') }} *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_phone') }} *</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_birth_date') }}</label>
                                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                                    @error('birth_date')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('signup.label_address') }}</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                                    @error('address')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_id_type') }}</label>
                                    <select name="id_type" class="form-control">
                                        <option value="">— {{ __('signup.label_id_type') }} —</option>
                                        <option value="cni" {{ old('id_type') == 'cni' ? 'selected' : '' }}>{{ __('kyc.option_cni') }}</option>
                                        <option value="passeport" {{ old('id_type') == 'passeport' ? 'selected' : '' }}>{{ __('kyc.option_passeport') }}</option>
                                        <option value="permis" {{ old('id_type') == 'permis' ? 'selected' : '' }}>{{ __('kyc.option_permis') }}</option>
                                    </select>
                                    @error('id_type')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_id_number') }}</label>
                                    <input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}">
                                    @error('id_number')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_date_delivre') }}</label>
                                    <input type="date" name="date_delivre" class="form-control" value="{{ old('date_delivre') }}">
                                    @error('date_delivre')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_tax_number') }}</label>
                                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number') }}">
                                    @error('tax_number')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_activity') }}</label>
                                    <input type="text" name="activity" class="form-control" value="{{ old('activity') }}">
                                    @error('activity')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('signup.label_currency') }}</label>
                                    <select name="currency" class="form-control">
                                        @foreach ($currenciesForForm as $currency)
                                            <option value="{{ $currency->code }}" {{ old('currency') == $currency->code ? 'selected' : '' }}>
                                                {{ $currency->code }} — {{ $currency->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100">{{ __('signup.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
