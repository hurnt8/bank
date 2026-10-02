@extends('layouts.client-app')
@section('title', __('kyc.title') . ' — ' . site_name())
@section('page_title', __('kyc.title'))
@section('back_btn', true)
@section('back_url', route('client.app.profile'))

@push('styles')
<style>
.kyc-body { padding:1.25rem 1.25rem 2.5rem }

.kyc-status {
  display:flex; align-items:center; gap:.75rem;
  padding:1rem 1.125rem; border-radius:var(--ca-radius-md);
  margin-bottom:1.5rem; border:1px solid var(--ca-border);
}
.kyc-status--non_soumis { background:var(--ca-bg2) }
.kyc-status--en_attente { background:rgba(245,158,11,.1); border-color:rgba(245,158,11,.3) }
.kyc-status--approuve   { background:rgba(0,200,150,.1); border-color:rgba(0,200,150,.3) }
.kyc-status--rejete     { background:rgba(239,68,68,.1); border-color:rgba(239,68,68,.3) }

.kyc-status__ico { font-size:1.3rem }
.kyc-status--non_soumis .kyc-status__ico { color:var(--ca-text-3) }
.kyc-status--en_attente .kyc-status__ico { color:#f59e0b }
.kyc-status--approuve   .kyc-status__ico { color:var(--ca-positive) }
.kyc-status--rejete     .kyc-status__ico { color:#ef4444 }

.kyc-status__title { font-size:.9rem; font-weight:700; color:var(--ca-text) }
.kyc-status__sub   { font-size:.78rem; color:var(--ca-text-3); margin-top:.1rem }

.kyc-form-group { margin-bottom:1.1rem }
.kyc-form-group label { display:block; font-size:.8rem; font-weight:600; color:var(--ca-text-2); margin-bottom:.4rem }
.kyc-form-group select,
.kyc-form-group input[type=file] {
  width:100%; padding:.7rem .9rem; border-radius:var(--ca-radius-sm);
  border:1px solid var(--ca-border); background:var(--ca-bg2); color:var(--ca-text);
  font-size:.85rem;
}
.kyc-hint { font-size:.74rem; color:var(--ca-text-3); margin-top:.3rem }
.kyc-submit-btn {
  width:100%; padding:.9rem; margin-top:.5rem;
  background:var(--ca-accent); color:#fff; border:none; border-radius:var(--ca-radius-sm);
  font-size:.92rem; font-weight:700; cursor:pointer;
}
.kyc-selfie-preview {
  width:100%; max-height:220px; object-fit:cover; border-radius:var(--ca-radius-md);
  margin-top:.5rem; display:none;
}
</style>
@endpush

@section('content')
<div class="kyc-body">

@php $status = $kyc->status ?? \App\Models\KycVerification::STATUS_NON_SOUMIS; @endphp

<div class="kyc-status kyc-status--{{ $status }}">
  <div class="kyc-status__ico">
    @switch($status)
      @case(\App\Models\KycVerification::STATUS_EN_ATTENTE) <i class="fas fa-hourglass-half"></i> @break
      @case(\App\Models\KycVerification::STATUS_APPROUVE)   <i class="fas fa-check-circle"></i> @break
      @case(\App\Models\KycVerification::STATUS_REJETE)     <i class="fas fa-times-circle"></i> @break
      @default <i class="fas fa-id-card"></i>
    @endswitch
  </div>
  <div>
    <div class="kyc-status__title">{{ __('kyc.status_' . $status) }}</div>
    @if($status === \App\Models\KycVerification::STATUS_REJETE && $kyc->rejection_reason)
      <div class="kyc-status__sub">{{ __('kyc.rejection_reason_label') }} : {{ $kyc->rejection_reason }}</div>
    @endif
  </div>
</div>

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:1rem">{{ session('error') }}</div>
@endif
@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1rem">{{ session('success') }}</div>
@endif

@if(in_array($status, [\App\Models\KycVerification::STATUS_NON_SOUMIS, \App\Models\KycVerification::STATUS_REJETE]))

<form method="POST" action="{{ route('client.app.kyc.store') }}" enctype="multipart/form-data">
  @csrf

  <div class="kyc-form-group">
    <label>{{ __('kyc.label_id_document_type') }}</label>
    <select name="id_document_type" required>
      <option value="cni">{{ __('kyc.option_cni') }}</option>
      <option value="passeport">{{ __('kyc.option_passeport') }}</option>
      <option value="permis">{{ __('kyc.option_permis') }}</option>
    </select>
    @error('id_document_type')<span class="form-error">{{ $message }}</span>@enderror
  </div>

  <div class="kyc-form-group">
    <label>{{ __('kyc.label_id_front') }}</label>
    <input type="file" name="id_document_front" accept="image/jpeg,image/png,application/pdf" required>
    @error('id_document_front')<span class="form-error">{{ $message }}</span>@enderror
  </div>

  <div class="kyc-form-group">
    <label>{{ __('kyc.label_id_back') }}</label>
    <input type="file" name="id_document_back" accept="image/jpeg,image/png,application/pdf">
    @error('id_document_back')<span class="form-error">{{ $message }}</span>@enderror
  </div>

  <div class="kyc-form-group">
    <label>{{ __('kyc.label_selfie') }}</label>
    <input type="file" name="selfie" accept="image/jpeg,image/png" capture="user" id="kyc-selfie-input" required>
    <div class="kyc-hint">{{ __('kyc.selfie_hint') }}</div>
    <img id="kyc-selfie-preview" class="kyc-selfie-preview" alt="">
    @error('selfie')<span class="form-error">{{ $message }}</span>@enderror
  </div>

  <button type="submit" class="kyc-submit-btn">
    {{ $status === \App\Models\KycVerification::STATUS_REJETE ? __('kyc.resubmit') : __('kyc.submit') }}
  </button>
</form>

@endif

</div>
@endsection

@push('scripts')
<script>
document.getElementById('kyc-selfie-input')?.addEventListener('change', function (e) {
  const file = e.target.files[0];
  const preview = document.getElementById('kyc-selfie-preview');
  if (file) {
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
  }
});
</script>
@endpush
