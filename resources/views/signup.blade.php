@extends('layouts.app')
@section('title', __('signup.title'))

@section('content')
@php
    $locale = app()->getLocale();
    $currenciesForForm = \App\Models\Currency::enabledList();
@endphp

@push('styles')
<style>
.country-auto-note {
    font-size: .74rem;
    color: #1a8047;
    margin-top: .35rem;
    display: flex;
    align-items: center;
    gap: .3rem;
}
[x-cloak] { display:none !important; }
</style>
@endpush

@push('scripts')
<script>
// Codes pays ISO 3166-1 (territoires habités uniquement).
const CREDIXA_SIGNUP_COUNTRY_CODES = [
    'AD','AE','AF','AG','AI','AL','AM','AO','AR','AS','AT','AU','AW','AX','AZ',
    'BA','BB','BD','BE','BF','BG','BH','BI','BJ','BL','BM','BN','BO','BQ','BR','BS','BT','BW','BY','BZ',
    'CA','CC','CD','CF','CG','CH','CI','CK','CL','CM','CN','CO','CR','CU','CV','CW','CX','CY','CZ',
    'DE','DJ','DK','DM','DO','DZ',
    'EC','EE','EG','EH','ER','ES','ET',
    'FI','FJ','FK','FM','FO','FR',
    'GA','GB','GD','GE','GF','GG','GH','GI','GL','GM','GN','GP','GQ','GR','GT','GU','GW','GY',
    'HK','HN','HR','HT','HU',
    'ID','IE','IL','IM','IN','IO','IQ','IR','IS','IT',
    'JE','JM','JO','JP',
    'KE','KG','KH','KI','KM','KN','KP','KR','KW','KY','KZ',
    'LA','LB','LC','LI','LK','LR','LS','LT','LU','LV','LY',
    'MA','MC','MD','ME','MF','MG','MH','MK','ML','MM','MN','MO','MP','MQ','MR','MS','MT','MU','MV','MW','MX','MY','MZ',
    'NA','NC','NE','NF','NG','NI','NL','NO','NP','NR','NU','NZ',
    'OM',
    'PA','PE','PF','PG','PH','PK','PL','PM','PN','PR','PS','PT','PW','PY',
    'QA',
    'RE','RO','RS','RU','RW',
    'SA','SB','SC','SD','SE','SG','SH','SI','SK','SL','SM','SN','SO','SR','SS','ST','SV','SX','SY','SZ',
    'TC','TD','TG','TH','TJ','TK','TL','TM','TN','TO','TR','TT','TV','TW','TZ',
    'UA','UG','US','UY','UZ',
    'VA','VC','VE','VG','VI','VN','VU',
    'WF','WS',
    'YE','YT',
    'ZA','ZM','ZW',
];

document.addEventListener('alpine:init', () => {
    Alpine.data('signupForm', () => ({
        country: '{{ old('country', '') }}',
        countries: [],
        countryDetected: false,
        locale: "{{ str_replace('_','-',app()->getLocale()) }}",

        init() {
            this.buildCountries();
            this.detectCountry();
        },

        buildCountries() {
            let displayNames = null;
            try { displayNames = new Intl.DisplayNames([this.locale, 'fr', 'en'], { type: 'region' }); }
            catch (e) { displayNames = null; }

            this.countries = CREDIXA_SIGNUP_COUNTRY_CODES
                .map(code => {
                    let name = code;
                    if (displayNames) {
                        try { name = displayNames.of(code) || code; } catch (e) { /* garde le code */ }
                    }
                    return { code, name };
                })
                .sort((a, b) => a.name.localeCompare(b.name, this.locale));
        },

        async detectCountry() {
            if (this.country) return; // déjà rempli (retour arrière / old())

            // 1) Détection par IP (service gratuit, sans clé) — repli silencieux si indisponible.
            try {
                const controller = new AbortController();
                const timer = setTimeout(() => controller.abort(), 3000);
                const res = await fetch('https://get.geojs.io/v1/ip/country.json', { signal: controller.signal });
                clearTimeout(timer);
                if (res.ok) {
                    const json = await res.json();
                    const found = this.countries.find(c => c.code === (json.country || '').toUpperCase());
                    if (found && !this.country) {
                        this.country = found.name;
                        this.countryDetected = true;
                        return;
                    }
                }
            } catch (e) { /* pas de réseau / service bloqué : on tente le repli navigateur */ }

            // 2) Repli : langue du navigateur (ex. "pt-PT" → "PT").
            if (!this.country) {
                const nav = navigator.language || (navigator.languages && navigator.languages[0]) || '';
                const region = nav.split('-')[1];
                if (region) {
                    const found = this.countries.find(c => c.code === region.toUpperCase());
                    if (found) { this.country = found.name; this.countryDetected = true; }
                }
            }
        },
    }));
});
</script>
@endpush

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

                    <form method="POST" action="{{ route('signup.store') }}" x-data="signupForm()">
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
                                    <label>{{ __('loan.label_country') }} *</label>
                                    <select name="country" x-model="country" class="form-control" required>
                                        <option value="">— {{ __('loan.placeholder_country') }} —</option>
                                        <template x-for="c in countries" :key="c.code">
                                            <option :value="c.name" x-text="c.name"></option>
                                        </template>
                                    </select>
                                    @error('country')<span class="form-error">{{ $message }}</span>@enderror
                                    <p class="country-auto-note" x-show="countryDetected" x-cloak x-transition>
                                        <i class="fas fa-location-crosshairs"></i>
                                        {{ __('loan.country_auto_hint') }}
                                    </p>
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
