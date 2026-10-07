@extends('layouts.dashboard')
@section('title', 'Vérification KYC — ' . $kyc->user->name)
@section('page_title', 'Vérification d\'identité')

@push('styles')
<style>
.kyc-doc-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:1.25rem; margin-bottom:1.5rem }
.kyc-doc-card { background:var(--c-bg2); border:1px solid var(--c-border); border-radius:12px; overflow:hidden }
.kyc-doc-card__label { padding:.6rem .9rem; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--c-muted); border-bottom:1px solid var(--c-border) }
.kyc-doc-card img, .kyc-doc-card iframe { width:100%; height:320px; object-fit:contain; background:#f5f5f5; border:none; display:block }
</style>
@endpush

@section('content')

<div class="page-hdr-row">
  <div class="page-hdr">
    <h1>{{ $kyc->user->name }}</h1>
    <p>{{ $kyc->user->email }} — <span class="badge-status">{{ $kyc->statusLabel() }}</span></p>
  </div>
  <a href="{{ route('admin.kyc.index') }}" class="btn-ghost btn-sm-pro"><i class="fas fa-arrow-left"></i> Retour</a>
</div>


<div class="kyc-doc-grid">
  @if($kyc->id_document_front_path)
  <div class="kyc-doc-card">
    <div class="kyc-doc-card__label">Pièce d'identité — recto ({{ $kyc->id_document_type }})</div>
    <img src="{{ route('admin.kyc.document', [$kyc, 'front']) }}" alt="Pièce recto">
  </div>
  @endif
  @if($kyc->id_document_back_path)
  <div class="kyc-doc-card">
    <div class="kyc-doc-card__label">Pièce d'identité — verso</div>
    <img src="{{ route('admin.kyc.document', [$kyc, 'back']) }}" alt="Pièce verso">
  </div>
  @endif
  @if($kyc->selfie_path)
  <div class="kyc-doc-card">
    <div class="kyc-doc-card__label">Selfie</div>
    <img src="{{ route('admin.kyc.document', [$kyc, 'selfie']) }}" alt="Selfie">
  </div>
  @endif
</div>

@if($kyc->status === \App\Models\KycVerification::STATUS_EN_ATTENTE)
<div class="card-pro" style="padding:1.25rem">
  <div style="display:flex;gap:1rem;flex-wrap:wrap">
    <form method="POST" action="{{ route('admin.kyc.approve', $kyc) }}" style="flex:1;min-width:200px">
      @csrf
      <button type="submit" class="btn-navy" style="width:100%;background:var(--c-green)">
        <i class="fas fa-check"></i> Approuver
      </button>
    </form>
    <form method="POST" action="{{ route('admin.kyc.reject', $kyc) }}" style="flex:2;min-width:300px;display:flex;gap:.6rem" x-data="{ reason: '' }">
      @csrf
      <input type="text" name="reason" x-model="reason" placeholder="Motif du rejet (obligatoire)" class="form-control-pro" required style="flex:1">
      <button type="submit" class="btn-ghost" style="border-color:var(--c-red);color:var(--c-red)" :disabled="!reason.trim()">
        <i class="fas fa-times"></i> Rejeter
      </button>
    </form>
  </div>
</div>
@endif

@if($kyc->reviews->isNotEmpty())
<div class="card-pro" style="margin-top:1.25rem;padding:1.25rem">
  <h3 style="font-size:.95rem;margin-bottom:.75rem">Historique des revues</h3>
  <div style="display:flex;flex-direction:column;gap:.6rem">
    @foreach($kyc->reviews as $review)
    <div style="font-size:.82rem;color:var(--c-muted)">
      <strong>{{ $review->action === 'approved' ? 'Approuvé' : 'Rejeté' }}</strong>
      par {{ $review->reviewer->name ?? '—' }}
      le {{ $review->created_at->format('d/m/Y H:i') }}
      @if($review->reason) — {{ $review->reason }} @endif
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection
