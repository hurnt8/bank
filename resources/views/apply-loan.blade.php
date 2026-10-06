@extends('layouts.app')
@section('title', __('menu.loan'))

@php
    $loanSetting = \App\Models\LoanSetting::current();
    $siteContact = \App\Models\SiteContact::current();
    $currenciesForForm = \App\Models\Currency::enabledList();
@endphp

@push('styles')
<style>
/* ── Devise ── */
.currency-btn {
    display:flex; align-items:center; gap:.5rem;
    padding:.5rem .9rem; border-radius:10px; border:2px solid #e5e7eb;
    background:#fff; cursor:pointer; transition:all .18s; user-select:none; white-space:nowrap;
}
.currency-btn:hover { border-color:var(--accent); }
.currency-btn.active { background:var(--navy); border-color:var(--navy); color:#fff; box-shadow:0 3px 12px rgba(10,37,76,.2); }
.currency-btn__flag { font-size:1.1rem; line-height:1; }
.currency-btn__name { font-size:.79rem; font-weight:700; line-height:1.1; }
.currency-btn__sym  { font-size:.7rem; opacity:.65; }

/* ── Chips ── */
.chip-group { display:flex; flex-wrap:wrap; gap:.4rem; }
.chip {
    padding:.38rem .8rem; border-radius:999px; border:2px solid #d1d5db;
    background:#fff; color:#374151; font-size:.82rem; font-weight:600;
    cursor:pointer; transition:all .18s; white-space:nowrap;
}
.chip:hover { border-color:var(--accent); color:var(--accent); }
.chip.active { background:var(--navy); color:#fff; border-color:var(--navy); }

/* ── Champ libre montant/durée ── */
.free-input-row {
    display:flex; align-items:center; gap:.6rem;
    margin-top:.65rem; padding:.55rem .85rem;
    background:#f5f7fb; border:1.5px dashed #c8d3e8; border-radius:10px;
}
.free-input-row label { font-size:.76rem; font-weight:600; color:#6b7280; white-space:nowrap; margin:0; }
.free-input-row input {
    flex:1; border:none; background:transparent; font-size:.92rem; font-weight:700;
    color:var(--navy); outline:none; min-width:0;
}
.free-input-row input::placeholder { font-weight:400; color:#b0bec5; }
.free-input-row .sym { font-size:.88rem; font-weight:800; color:var(--navy); opacity:.7; }

/* ── Résumé devis ── */
.quote-result {
    background:linear-gradient(135deg,var(--navy) 0%,#183560 100%);
    border-radius:14px; padding:1.1rem 1.3rem; color:#fff;
}
.quote-result__row { display:flex; flex-wrap:wrap; gap:.8rem; justify-content:space-between; margin-bottom:.75rem; }
.quote-result__item { text-align:center; flex:1; min-width:80px; }
.quote-result__label { font-size:.62rem; text-transform:uppercase; letter-spacing:.08em; color:rgba(255,255,255,.5); display:block; margin-bottom:.2rem; }
.quote-result__value { font-size:1rem; font-weight:800; color:#fff; }
.quote-result__value.accent { color:var(--accent); font-size:1.25rem; }
.quote-result__sep { width:1px; height:32px; background:rgba(255,255,255,.15); }

/* ── Séparateurs de section ── */
.form-section { margin-bottom:1.5rem; }
.form-section-title {
    font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.07em;
    color:var(--navy); margin-bottom:.55rem; display:flex; align-items:center; gap:.5rem;
}
.form-section-title i { color:var(--accent); }
.form-section-title::after { content:''; flex:1; height:1px; background:#eaecf0; }

/* ── Sidebar raisons ── */
.reason-item { display:flex; gap:.75rem; padding:.8rem 0; }
.reason-item + .reason-item { border-top:1px solid #f0f0f0; }
.reason-icon { width:38px; height:38px; border-radius:9px; background:rgba(212,175,55,.1); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
.reason-title { font-size:.84rem; font-weight:700; color:var(--navy); margin-bottom:.15rem; }
.reason-desc  { font-size:.75rem; color:#6b7280; line-height:1.45; margin:0; }

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
// Codes pays ISO 3166-1 (territoires habités uniquement — Antarctique et îles
// inhabitées exclues, sans intérêt pour un pays de résidence).
const CREDIXA_COUNTRY_CODES = [
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
    Alpine.data('loanForm', () => ({
        submitting:  false,
        selCurrency: '{{ \App\Models\Currency::default() }}',
        selAmount:   null,
        customAmt:   '',
        selDuration: null,
        customDur:   '',
        rate: {{ (float) $loanSetting->annual_rate }},
        minAmount: {{ (float) $loanSetting->min_amount }},
        maxAmount: {{ (float) $loanSetting->max_amount }},

        monthsLabel: "{{ __('message.months') }}",
        monthAbbr:   "{{ __('message.month_abbr') }}",
        locale:      "{{ str_replace('_','-',app()->getLocale()) }}",

        country: '{{ old('country', '') }}',
        countries: [],
        countryDetected: false,

        init() {
            this.buildCountries();
            this.detectCountry();
        },

        buildCountries() {
            let displayNames = null;
            try { displayNames = new Intl.DisplayNames([this.locale, 'fr', 'en'], { type: 'region' }); }
            catch (e) { displayNames = null; }

            this.countries = CREDIXA_COUNTRY_CODES
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

        currencies: (() => {
            // Drapeau derive du code ISO 4217 : ses 2 premieres lettres correspondent
            // presque toujours au code pays ISO 3166-1 (USD->US, BRL->BR, EUR->EU...).
            // Ainsi toute devise ajoutee depuis l'admin obtient automatiquement son
            // drapeau, sans table a maintenir manuellement.
            const flagFromCode = (code) => code.slice(0, 2).toUpperCase()
                .replace(/./g, ch => String.fromCodePoint(127397 + ch.charCodeAt(0)));
            return @json($currenciesForForm->map(fn ($c) => ['code' => $c->code, 'symbol' => $c->symbol, 'name' => $c->name])->values())
                .map(c => ({ ...c, flag: flagFromCode(c.code) }));
        })(),

        amountsByCurrency: {
            EUR:[1000,3000,5000,10000,20000,50000,75000,95000],
            GBP:[1000,2500,5000,10000,20000,40000,65000,80000],
            CHF:[1000,3000,5000,10000,20000,50000,75000,95000],
            NOK:[10000,30000,50000,100000,200000,500000,750000,950000],
            SEK:[10000,30000,50000,100000,200000,500000,750000,950000],
            DKK:[7000,20000,35000,75000,150000,375000,550000,700000],
            PLN:[5000,10000,20000,50000,100000,200000,350000,500000],
            CZK:[25000,75000,125000,250000,500000,1000000,1500000,2000000],
            HUF:[500000,1000000,2000000,4000000,8000000,20000000,30000000,40000000],
            RON:[5000,15000,25000,50000,100000,250000,375000,475000],
        },

        get amounts()  { return this.amountsByCurrency[this.selCurrency] || this.amountsByCurrency['EUR']; },
        get currency() { return this.currencies.find(c => c.code === this.selCurrency) || this.currencies[0]; },

        get amount() {
            const c = parseFloat(this.customAmt);
            if (!isNaN(c) && c > 0) {
                return (c >= this.minAmount && c <= this.maxAmount) ? c : null;
            }
            return this.selAmount;
        },
        get amountOutOfRange() {
            const c = parseFloat(this.customAmt);
            return !isNaN(c) && c > 0 && (c < this.minAmount || c > this.maxAmount);
        },
        get duration() {
            const c = parseInt(this.customDur);
            return (!isNaN(c) && c > 0) ? c : this.selDuration;
        },
        get monthly() {
            const p = parseFloat(this.amount), n = parseInt(this.duration);
            const r = this.rate / 100 / 12;
            if (!p || !n || p <= 0 || n <= 0 || isNaN(p) || isNaN(n)) return null;
            return (p * r * Math.pow(1+r,n)) / (Math.pow(1+r,n) - 1);
        },
        get total()     { return this.monthly ? this.monthly * parseInt(this.duration) : null; },
        get interests() { return (this.total && this.amount) ? this.total - parseFloat(this.amount) : null; },
        get canProceed(){ return this.monthly !== null; },

        fmt(v, dec=2) {
            if (v === null || v === undefined || isNaN(v)) return '—';
            try {
                return new Intl.NumberFormat(this.locale, {
                    style:'currency', currency:this.selCurrency,
                    minimumFractionDigits:dec, maximumFractionDigits:dec,
                }).format(v);
            } catch(e) { return v.toFixed(dec) + ' ' + this.selCurrency; }
        },
        fmtAmt(v) {
            if (!v) return '—';
            try {
                return new Intl.NumberFormat(this.locale, {
                    style:'currency', currency:this.selCurrency,
                    minimumFractionDigits:0, maximumFractionDigits:0,
                }).format(v);
            } catch(e) { return v + ' ' + this.selCurrency; }
        },

        setCurrency(code) {
            if (this.selCurrency === code) return;
            this.selCurrency = code;
            this.selAmount = null; this.customAmt = '';
        },
        pickAmount(v)   { this.selAmount = v; this.customAmt = ''; },
        pickDuration(v) { this.selDuration = v; this.customDur = ''; },
    }));
});
</script>
@endpush

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.loan')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.loan')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row g-4 align-items-start">

            {{-- ══════════ FORMULAIRE PRINCIPAL ══════════ --}}
            <div class="col-lg-8" x-data="loanForm">
                <div class="form-card wow fadeInLeft" data-wow-duration="700ms"
                     style="border-top:4px solid var(--accent);">

                    {{-- En-tête --}}
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
                        <div>
                            <div class="section-label mb-1">{{ __('loan.step_of_2', ['step' => 1]) }}</div>
                            <h3 style="font-family:'Fraunces',serif;color:var(--navy);font-size:1.25rem;font-weight:700;margin:0 0 .15rem;">
                                @lang('loan.quote_step_title')
                            </h3>
                            <p style="font-size:.78rem;color:#6b7280;margin:0;">@lang('loan.quote_step_desc')</p>
                        </div>
                        <div style="display:inline-flex;align-items:center;gap:.4rem;background:var(--navy);color:#FFFFFF;padding:.35rem .9rem;border-radius:999px;font-weight:800;font-size:.82rem;white-space:nowrap;flex-shrink:0;">
                            <i class="fas fa-lock" style="font-size:.68rem;"></i>
                            @lang('loan.label_rate') : {{ number_format((float) $loanSetting->annual_rate, 2) }} %
                        </div>
                    </div>

                    @if (session('success'))
                    {{-- ══ PANNEAU DE CONFIRMATION ══ --}}
                    <div style="text-align:center;padding:1.5rem .5rem 2rem;">
                        <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#d1fae5,#a7f3d0);margin:0 auto 1.2rem;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(5,150,105,.2);">
                            <i class="fas fa-check" style="font-size:1.8rem;color:#059669;"></i>
                        </div>
                        <h3 style="color:var(--navy);font-size:1.2rem;font-weight:800;margin-bottom:.6rem;">
                            {{ session('success') }}
                        </h3>
                        <p style="color:#6b7280;font-size:.88rem;max-width:420px;margin:0 auto 1.75rem;line-height:1.6;">
                            @lang('message.loan_complete_intro')
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ route('home', ['locale' => $locale]) }}" class="btn-outline">
                                <i class="fas fa-home"></i> @lang('menu.home')
                            </a>
                            <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary">
                                <i class="fas fa-plus"></i> @lang('menu.loan')
                            </a>
                        </div>
                    </div>
                    @else

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif

                    {{-- ── ① Devise ── --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-globe"></i> @lang('loan.label_currency')
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <template x-for="c in currencies" :key="c.code">
                                <button type="button" class="currency-btn"
                                        :class="selCurrency === c.code ? 'active' : ''"
                                        @click="setCurrency(c.code)">
                                    <span class="currency-btn__flag" x-text="c.flag"></span>
                                    <div>
                                        <div class="currency-btn__name" x-text="c.name"></div>
                                        <div class="currency-btn__sym" x-text="c.code + ' ' + c.symbol"></div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- ── ② Montant ── --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-coins"></i> @lang('loan.label_amount')
                        </div>
                        <p style="font-size:.8rem;color:#6b7280;margin-bottom:.6rem;">
                            @lang('loan.preset_hint')
                        </p>
                        <div class="chip-group">
                            <template x-for="v in amounts" :key="v">
                                <button type="button" class="chip"
                                        :class="selAmount === v && customAmt === '' ? 'active' : ''"
                                        @click="pickAmount(v)"
                                        x-text="fmtAmt(v)"></button>
                            </template>
                        </div>
                        {{-- Champ libre toujours visible --}}
                        <div class="free-input-row mt-2">
                            <label>
                                <i class="fas fa-keyboard" style="margin-right:.3rem;color:var(--accent);"></i>
                                @lang('loan.label_other') :
                            </label>
                            <input type="number" x-model="customAmt" @input="selAmount = null"
                                   :min="minAmount" :max="maxAmount" step="100"
                                   placeholder="{{ __('loan.placeholder_amount') }}">
                            <span class="sym" x-text="currency.symbol"></span>
                        </div>
                        <p x-show="amountOutOfRange" x-cloak style="font-size:.75rem;color:#dc2626;margin:.4rem 0 0;">
                            <i class="fas fa-exclamation-circle" style="margin-right:.25rem;"></i>
                            {{ __('loan.amount_range_hint', ['min' => number_format((float) $loanSetting->min_amount, 0, ',', ' '), 'max' => number_format((float) $loanSetting->max_amount, 0, ',', ' ')]) }}
                        </p>
                    </div>

                    {{-- ── ③ Durée ── --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-calendar-alt"></i> @lang('loan.label_darly')
                        </div>
                        <p style="font-size:.8rem;color:#6b7280;margin-bottom:.6rem;">
                            @lang('loan.or_custom')
                        </p>
                        <div class="chip-group">
                            <template x-for="d in [12,24,36,48,60,84,120]" :key="d">
                                <button type="button" class="chip"
                                        :class="selDuration === d && customDur === '' ? 'active' : ''"
                                        @click="pickDuration(d)"
                                        x-text="d + ' ' + monthsLabel"></button>
                            </template>
                        </div>
                        {{-- Champ libre toujours visible --}}
                        <div class="free-input-row mt-2">
                            <label>
                                <i class="fas fa-keyboard" style="margin-right:.3rem;color:var(--accent);"></i>
                                @lang('loan.label_other') :
                            </label>
                            <input type="number" x-model="customDur" @input="selDuration = null"
                                   min="1" max="360" placeholder="Ex : 72">
                            <span class="sym" x-text="monthsLabel"></span>
                        </div>
                    </div>

                    {{-- ── Résumé devis (apparaît dès que montant + durée sont renseignés) ── --}}
                    <div x-show="canProceed" x-cloak x-transition
                         class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-calculator"></i> @lang('loan.quote_summary_title')
                        </div>
                        <div class="quote-result">
                            <div class="quote-result__row">
                                <div class="quote-result__item">
                                    <span class="quote-result__label">@lang('loan.quote_monthly')</span>
                                    <span class="quote-result__value accent" x-text="fmt(monthly)">—</span>
                                </div>
                                <div class="quote-result__sep d-none d-sm-block"></div>
                                <div class="quote-result__item">
                                    <span class="quote-result__label">@lang('loan.quote_total')</span>
                                    <span class="quote-result__value" x-text="fmt(total)">—</span>
                                </div>
                                <div class="quote-result__sep d-none d-sm-block"></div>
                                <div class="quote-result__item">
                                    <span class="quote-result__label">@lang('loan.quote_interest')</span>
                                    <span class="quote-result__value" style="color:rgba(255,255,255,.6);" x-text="fmt(interests)">—</span>
                                </div>
                            </div>
                            <p style="font-size:.68rem;color:rgba(255,255,255,.4);margin:0;">
                                <i class="fas fa-info-circle" style="margin-right:.25rem;"></i>{{ __('loan.quote_hint', ['rate' => number_format((float) $loanSetting->annual_rate, 2)]) }}
                            </p>
                        </div>
                    </div>

                    <hr style="border-color:#eaecf0;margin:0 0 1.5rem;">

                    {{-- ── Formulaire coordonnées ── --}}
                    <div class="form-section-title" style="margin-bottom:1rem;">
                        <i class="fas fa-user"></i> @lang('loan.form_title')
                    </div>
                    <p style="font-size:.79rem;color:#6b7280;margin-bottom:1.2rem;">@lang('loan.form_hint')</p>

                    <form method="POST" action="{{ route('loan.request') }}" @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="locale"   value="{{ app()->getLocale() }}">
                        <input type="hidden" name="amount"   :value="amount">
                        <input type="hidden" name="darly"    :value="duration">
                        <input type="hidden" name="currency" :value="selCurrency">

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('loan.label_name') <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name') }}"
                                           placeholder="@lang('loan.placeholder_name')" required>
                                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('loan.label_email') <span style="color:var(--accent);">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email') }}"
                                           placeholder="@lang('loan.placeholder_email')" required>
                                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('loan.label_phone') <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone') }}"
                                           placeholder="@lang('loan.placeholder_phone')" required>
                                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('loan.label_country') <span style="color:var(--accent);">*</span></label>
                                    <select name="country" x-model="country"
                                            class="form-control" required>
                                        <option value="">— @lang('loan.placeholder_country') —</option>
                                        <template x-for="c in countries" :key="c.code">
                                            <option :value="c.name" x-text="c.name"></option>
                                        </template>
                                    </select>
                                    @error('country')<span class="form-error">{{ $message }}</span>@enderror
                                    <p class="country-auto-note" x-show="countryDetected" x-cloak x-transition>
                                        <i class="fas fa-location-crosshairs"></i>
                                        @lang('loan.country_auto_hint')
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('contact.subject') <span style="color:var(--accent);">*</span></label>
                                    <select name="subject" class="form-control" required>
                                        <option value="">— @lang('contact.subject') —</option>
                                        <option value="Prêt personnel"  {{ old('subject')=='Prêt personnel'  ?'selected':'' }}>@lang('menu.personal')</option>
                                        <option value="Prêt immobilier" {{ old('subject')=='Prêt immobilier' ?'selected':'' }}>@lang('menu.home_loan')</option>
                                        <option value="Prêt commercial" {{ old('subject')=='Prêt commercial' ?'selected':'' }}>@lang('menu.business')</option>
                                        <option value="Prêt étudiant"   {{ old('subject')=='Prêt étudiant'   ?'selected':'' }}>@lang('menu.study')</option>
                                        <option value="Prêt auto"       {{ old('subject')=='Prêt auto'       ?'selected':'' }}>@lang('menu.auto')</option>
                                        <option value="Prêt vélo"       {{ old('subject')=='Prêt vélo'       ?'selected':'' }}>@lang('menu.bike')</option>
                                    </select>
                                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('loan.label_objet')
                                        <span style="font-size:.72rem;color:#9ca3af;font-weight:400;">({{ __('message.optional') }})</span>
                                    </label>
                                    <textarea name="objet" class="form-control" rows="3"
                                              placeholder="@lang('loan.placeholder_objet')">{{ old('objet') }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-1">
                                <button type="submit" class="btn-primary btn-primary--lg w-100 justify-content-center" :disabled="submitting">
                                    <i class="fas fa-spinner fa-spin" x-show="submitting" x-cloak></i>
                                    <i class="fas fa-paper-plane" x-show="!submitting"></i>
                                    <span x-text="submitting ? '{{ __('loan.button_sending') }}' : '{{ __('loan.button') }}'"></span>
                                </button>
                                <p style="font-size:.71rem;color:#9ca3af;text-align:center;margin-top:.55rem;">
                                    <i class="fas fa-lock" style="margin-right:.3rem;"></i>
                                    @lang('loan.form_security')
                                </p>
                            </div>
                        </div>
                    </form>

                    @endif {{-- /session('success') --}}

                </div>
            </div>{{-- /col-lg-8 --}}

            {{-- ══════════ SIDEBAR ══════════ --}}
            <div class="col-lg-4 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div style="position:sticky;top:110px;" class="service-sidebar">

                    <div class="contact-widget">
                        <div class="contact-widget__icon"><i class="fas fa-phone-alt"></i></div>
                        <h4>@lang('contact.phone_title')</h4>
                        <p>@lang('loan.sidebar_hours')</p>
                        @if($siteContact->phone_1)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $siteContact->phone_1) }}" class="contact-widget__phone">{{ $siteContact->phone_1 }}</a>
                        @endif
                        <a href="{{ route('contact', ['locale' => $locale]) }}"
                           class="btn-outline w-100 justify-content-center mt-2">
                            <i class="fas fa-envelope"></i> @lang('menu.contact')
                        </a>
                    </div>

                    <div class="service-sidebar__widget mt-3">
                        <h3 class="service-sidebar__title">@lang('home.loan_reasons.sectitle')</h3>
                        @php
                            $reasonIcons = [
                                1 => 'fa-car',
                                2 => 'fa-layer-group',
                                3 => 'fa-home',
                                4 => 'fa-graduation-cap',
                                5 => 'fa-plane',
                                6 => 'fa-heart',
                                7 => 'fa-stethoscope',
                                8 => 'fa-briefcase',
                            ];
                        @endphp
                        @foreach ($reasonIcons as $r => $icon)
                        <div class="reason-item">
                            <div class="reason-icon"><i class="fas {{ $icon }}"></i></div>
                            <div>
                                <div class="reason-title">@lang('home.loan_reasons.reasons.title' . $r)</div>
                                <p class="reason-desc">@lang('home.loan_reasons.reasons.desc' . $r)</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- Bande partenaires (signal de confiance) --}}
@push('styles')
<style>
.partners-marquee {
    overflow:hidden; position:relative;
    -webkit-mask-image:linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
    mask-image:linear-gradient(to right, transparent, #000 6%, #000 94%, transparent);
}
.partners-track {
    display:flex; align-items:center; width:max-content; gap:1.1rem;
    animation:partners-scroll 70s linear infinite;
}
.partners-marquee:hover .partners-track { animation-play-state:paused; }
@keyframes partners-scroll {
    from { transform:translateX(0); }
    to   { transform:translateX(-50%); }
}
.partner-logo {
    display:flex; align-items:center; justify-content:center;
    padding:.8rem 1.5rem; min-width:120px; height:66px;
    background:#fff; border:1.5px solid #e5e7eb; border-radius:12px;
    filter:grayscale(1); opacity:.6;
    transition:filter .3s ease, opacity .3s ease, border-color .3s ease, box-shadow .3s ease;
    cursor:default; flex-shrink:0;
}
.partner-logo:hover {
    filter:grayscale(0); opacity:1;
    border-color:var(--accent); box-shadow:0 4px 22px rgba(200,169,81,.2);
}
.partner-logo--text {
    font-size:.85rem; font-weight:700; color:var(--navy);
    text-align:center; line-height:1.3; white-space:nowrap;
}
@media (max-width:576px) {
    .partner-logo { min-width:100px; padding:.65rem 1rem; height:56px; }
    .partners-track { gap:.65rem; animation-duration:45s; }
}
@media (prefers-reduced-motion: reduce) {
    .partners-track { animation:none; flex-wrap:wrap; width:100%; justify-content:center; }
}
</style>
@endpush
@endsection
