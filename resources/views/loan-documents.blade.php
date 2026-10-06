@extends('layouts.app')
@section('title', __('loan.complete_title'))

@push('styles')
<style>
.doc-type-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .6rem;
}
@media (max-width: 576px) {
    .doc-type-grid { grid-template-columns: repeat(2, 1fr); }
}
.doc-type-card {
    border: 2px solid #d8e4f4;
    border-radius: 10px;
    padding: .9rem .6rem .75rem;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s, box-shadow .15s;
    background: #fff;
    user-select: none;
}
.doc-type-card:hover { border-color: #1a4080; background: #f0f5ff; }
.doc-type-card.selected {
    border-color: #1a4080;
    background: #e8f0ff;
    box-shadow: 0 0 0 3px rgba(26,64,128,.1);
}
.doc-type-icon { font-size: 1.6rem; display: block; margin-bottom: .4rem; color: #4a6fa5; }
.doc-type-card.selected .doc-type-icon { color: #1a4080; }
.doc-type-label { font-size: .75rem; font-weight: 700; color: #1a4080; line-height: 1.2; }

.upload-zone {
    position: relative;
    border: 2px dashed #c5d3e8;
    border-radius: 10px;
    padding: 1.4rem 1rem;
    text-align: center;
    background: #f8faff;
    overflow: hidden;
    transition: border-color .15s, background .15s;
    cursor: pointer;
}
.upload-zone:hover { border-color: #1a4080; background: #eef3ff; }
.upload-zone.has-file { border-color: #28a745; background: #f0fff4; }
.upload-zone input[type="file"] {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    opacity: 0;
    cursor: pointer;
}
.step-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .75rem;
    font-weight: 700;
    color: #1a4080;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: .6rem;
}
.step-badge .num {
    background: #1a4080;
    color: #fff;
    border-radius: 50%;
    width: 1.4rem;
    height: 1.4rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
}
.recap-box {
    background: #f0f5ff;
    border: 1px solid #d8e4f4;
    border-radius: 10px;
    padding: .9rem 1.1rem;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: .5rem 1.4rem;
}
.recap-box__item { font-size: .82rem; color: #4a6fa5; }
.recap-box__item strong { color: #1a4080; }
[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">{{ __('loan.complete_title') }}</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>{{ __('loan.complete_title') }}</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row g-4 align-items-start">

            {{-- ══════════ FORMULAIRE PRINCIPAL ══════════ --}}
            <div class="col-lg-8 order-2 order-lg-1 wow fadeInLeft" data-wow-duration="700ms">

                <div class="form-card" x-data="docUploadForm">

                    <div class="section-label mb-2">{{ __('loan.step_of_2', ['step' => 2]) }}</div>
                    <h2 class="section-title mb-2">{{ __('loan.complete_title') }}</h2>
                    <p style="color:#666;margin-bottom:1.5rem;">{{ __('loan.complete_desc') }}</p>

                    @if (($recap ?? null) && !session('success') && !session('docs_already_sent'))
                    <div class="recap-box">
                        <div class="recap-box__item">
                            <i class="fas fa-check-circle" style="color:#28a745;margin-right:.3rem;"></i>
                            <strong>{{ __('loan.recap_title') }}</strong>
                        </div>
                        @if(!empty($recap['amount']))
                        <div class="recap-box__item">{{ __('loan.label_amount') }}: <strong>{{ number_format((float) $recap['amount'], 0, ',', ' ') }} {{ $recap['currency'] ?? '' }}</strong></div>
                        @endif
                        @if(!empty($recap['darly']))
                        <div class="recap-box__item">{{ __('loan.label_darly') }}: <strong>{{ $recap['darly'] }} {{ __('message.months') }}</strong></div>
                        @endif
                        @if(!empty($recap['subject']))
                        <div class="recap-box__item">{{ __('contact.subject') }}: <strong>{{ $recap['subject'] }}</strong></div>
                        @endif
                        @if(!empty($recap['country']))
                        <div class="recap-box__item">{{ __('loan.label_country') }}: <strong>{{ $recap['country'] }}</strong></div>
                        @endif
                    </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle" style="margin-right:.4rem;"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('docs_already_sent'))
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle" style="margin-right:.4rem;"></i>
                            @lang('message.docs_already_sent')
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0" style="padding-left:1.1rem;">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success') || session('docs_already_sent'))
                        <div class="text-center py-3">
                            <a href="{{ route('home', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                                <i class="fas fa-home" style="margin-right:.5rem;"></i>
                                @lang('menu.home')
                            </a>
                        </div>
                    @else
                    <form method="POST" action="{{ route('loan.documents') }}" enctype="multipart/form-data" @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $locale }}">
                        <input type="hidden" name="submission_token" value="{{ $submissionToken }}">

                        {{-- ① Coordonnées --}}
                        <div class="mb-4">
                            <div class="step-badge">
                                <span class="num">1</span>
                                {{ __('loan.label_name') }} &amp; {{ __('loan.label_email') }}
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('loan.label_name') }} *</label>
                                        <input type="text" name="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $prefillName ?? '') }}"
                                               placeholder="{{ __('loan.placeholder_name') }}" required>
                                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('loan.label_email') }} *</label>
                                        <input type="email" name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $prefillEmail ?? '') }}"
                                               placeholder="{{ __('loan.placeholder_email') }}" required>
                                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>{{ __('loan.label_address') }} *</label>
                                        <textarea name="address" rows="2"
                                                  class="form-control @error('address') is-invalid @enderror"
                                                  placeholder="{{ __('loan.placeholder_address') }}" required>{{ old('address') }}</textarea>
                                        @error('address')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                {{-- Pays : déjà sélectionné à l'étape 1, transmis tel quel --}}
                                <input type="hidden" name="country" value="{{ old('country', $recap['country'] ?? '') }}">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('loan.label_tax_number') }}</label>
                                        <input type="text" name="tax_number"
                                               class="form-control @error('tax_number') is-invalid @enderror"
                                               value="{{ old('tax_number') }}"
                                               placeholder="{{ __('loan.placeholder_tax_number') }}">
                                        @error('tax_number')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('loan.label_activity') }}</label>
                                        <input type="text" name="activity"
                                               class="form-control @error('activity') is-invalid @enderror"
                                               value="{{ old('activity') }}"
                                               placeholder="{{ __('loan.placeholder_activity') }}">
                                        @error('activity')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr style="border-color:#e8eef8;margin:1.5rem 0;">

                        {{-- ② Type de document --}}
                        <div class="mb-4">
                            <div class="step-badge">
                                <span class="num">2</span>
                                {{ __('message.docs_doc_type') }} *
                            </div>
                            <p style="color:#888;font-size:.85rem;margin-bottom:.85rem;">
                                {{ __('loan.complete_desc') }}
                            </p>

                            <input type="hidden" name="doc_type" :value="docType">

                            <div class="doc-type-grid">
                                <div class="doc-type-card" :class="{ selected: docType === 'id_card' }" @click="setDocType('id_card')">
                                    <i class="fas fa-id-card doc-type-icon"></i>
                                    <span class="doc-type-label">{{ __('message.doc_type_id_card') }}</span>
                                </div>
                                <div class="doc-type-card" :class="{ selected: docType === 'passport' }" @click="setDocType('passport')">
                                    <i class="fas fa-passport doc-type-icon"></i>
                                    <span class="doc-type-label">{{ __('message.doc_type_passport') }}</span>
                                </div>
                                <div class="doc-type-card" :class="{ selected: docType === 'license' }" @click="setDocType('license')">
                                    <i class="fas fa-car doc-type-icon"></i>
                                    <span class="doc-type-label">{{ __('message.doc_type_license') }}</span>
                                </div>
                                <div class="doc-type-card" :class="{ selected: docType === 'residence' }" @click="setDocType('residence')">
                                    <i class="fas fa-home doc-type-icon"></i>
                                    <span class="doc-type-label">{{ __('message.doc_type_residence') }}</span>
                                </div>
                                <div class="doc-type-card" :class="{ selected: docType === 'other' }" @click="setDocType('other')">
                                    <i class="fas fa-file-alt doc-type-icon"></i>
                                    <span class="doc-type-label">{{ __('message.doc_type_other') }}</span>
                                </div>
                            </div>
                            @error('doc_type')<span class="form-error d-block mt-2">{{ $message }}</span>@enderror
                        </div>

                        {{-- ③ Photos --}}
                        <div x-show="docType !== null" x-transition>
                            <hr style="border-color:#e8eef8;margin:1.5rem 0;">

                            <div class="step-badge mb-3">
                                <span class="num">3</span>
                                {{ __('message.docs_id_photo') }}
                            </div>

                            {{-- Note : verso non requis --}}
                            <p x-show="!needsVerso" x-transition
                               style="font-size:.82rem;color:#666;background:#f0f5ff;border-radius:7px;padding:.6rem .9rem;margin-bottom:1rem;">
                                <i class="fas fa-info-circle" style="color:#1a4080;margin-right:.35rem;"></i>
                                @lang('message.docs_single_photo')
                            </p>

                            <div class="row g-3">
                                {{-- Recto --}}
                                <div :class="needsVerso ? 'col-md-6' : 'col-12'">
                                    <label style="font-weight:600;display:block;margin-bottom:.45rem;">
                                        <span x-text="needsVerso ? '{{ __('message.docs_recto') }}' : '{{ __('message.docs_id_photo') }}'"></span>
                                        <span style="font-weight:400;font-size:.76rem;color:#999;"> *  (JPG, PNG, PDF — max 5 Mo)</span>
                                    </label>
                                    <div class="upload-zone" :class="{ 'has-file': rectoName }">
                                        <input type="file" name="id_photo_recto"
                                               accept=".jpg,.jpeg,.png,.pdf"
                                               @change="rectoName = $event.target.files[0]?.name ?? null"
                                               required>
                                        <template x-if="!rectoName">
                                            <div>
                                                <i class="fas fa-cloud-upload-alt" style="font-size:1.8rem;color:#a0b4d0;display:block;margin-bottom:.4rem;"></i>
                                                <div style="font-size:.82rem;color:#8096b0;">@lang('message.docs_upload_hint')</div>
                                            </div>
                                        </template>
                                        <template x-if="rectoName">
                                            <div>
                                                <i class="fas fa-check-circle" style="font-size:1.6rem;color:#28a745;display:block;margin-bottom:.35rem;"></i>
                                                <div style="font-size:.8rem;color:#28a745;word-break:break-all;" x-text="rectoName"></div>
                                            </div>
                                        </template>
                                    </div>
                                    @error('id_photo_recto')<span class="form-error d-block mt-1">{{ $message }}</span>@enderror
                                </div>

                                {{-- Verso --}}
                                <div class="col-md-6" x-show="needsVerso" x-transition>
                                    <label style="font-weight:600;display:block;margin-bottom:.45rem;">
                                        {{ __('message.docs_verso') }}
                                        <span style="font-weight:400;font-size:.76rem;color:#999;"> *  (JPG, PNG, PDF — max 5 Mo)</span>
                                    </label>
                                    <div class="upload-zone" :class="{ 'has-file': versoName }">
                                        <input type="file" name="id_photo_verso"
                                               accept=".jpg,.jpeg,.png,.pdf"
                                               @change="versoName = $event.target.files[0]?.name ?? null">
                                        <template x-if="!versoName">
                                            <div>
                                                <i class="fas fa-cloud-upload-alt" style="font-size:1.8rem;color:#a0b4d0;display:block;margin-bottom:.4rem;"></i>
                                                <div style="font-size:.82rem;color:#8096b0;">@lang('message.docs_upload_hint')</div>
                                            </div>
                                        </template>
                                        <template x-if="versoName">
                                            <div>
                                                <i class="fas fa-check-circle" style="font-size:1.6rem;color:#28a745;display:block;margin-bottom:.35rem;"></i>
                                                <div style="font-size:.8rem;color:#28a745;word-break:break-all;" x-text="versoName"></div>
                                            </div>
                                        </template>
                                    </div>
                                    @error('id_photo_verso')<span class="form-error d-block mt-1">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-primary btn-primary--lg w-100 justify-content-center"
                                    :disabled="submitting || !docType || !rectoName">
                                <i class="fas fa-spinner fa-spin" style="margin-right:.5rem;" x-show="submitting"></i>
                                <i class="fas fa-upload" style="margin-right:.5rem;" x-show="!submitting"></i>
                                {{ __('loan.complete_btn') }}
                            </button>
                            <p x-show="!docType || !rectoName" x-transition
                               style="font-size:.75rem;color:#9ca3af;text-align:center;margin-top:.55rem;">
                                <i class="fas fa-info-circle" style="margin-right:.3rem;"></i>
                                {{ __('message.docs_complete_hint') }}
                            </p>
                        </div>

                    </form>
                    @endif
                </div>

            </div>

            {{-- ══════════ CONDITIONS ══════════ --}}
            <div class="col-lg-4 order-1 order-lg-2 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div style="position:sticky;top:110px;">
                    <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:1rem 1.25rem;">
                        <div style="font-weight:700;color:#7c5800;margin-bottom:.35rem;">
                            <i class="fas fa-info-circle" style="margin-right:.4rem;"></i>
                            {{ __('message.loan_conditions_title') }}
                        </div>
                        <p style="margin:0;color:#7c5800;font-size:.88rem;">{{ __('message.loan_conditions_text') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('docUploadForm', () => ({
        docType: '{{ old('doc_type', '') }}' || null,
        rectoName: null,
        versoName: null,
        submitting: false,

        get needsVerso() {
            return ['id_card', 'license', 'residence'].includes(this.docType);
        },
        setDocType(type) {
            this.docType = type;
            if (!['id_card', 'license', 'residence'].includes(type)) {
                this.versoName = null;
                const el = document.querySelector('[name="id_photo_verso"]');
                if (el) el.value = '';
            }
        },
    }));
});
</script>
@endpush
