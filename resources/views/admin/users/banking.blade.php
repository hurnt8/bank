@extends('layouts.dashboard')
@section('title', 'Coordonnées bancaires — ' . $user->name)
@section('page_title', 'Attribuer IBAN / Carte')

@section('content')

<div class="page-hdr-row">
  <div class="page-hdr">
    <h1>{{ $user->name }}</h1>
    <p>{{ $user->email }}</p>
  </div>
  <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost btn-sm-pro"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:1rem">
  <ul style="margin:0;padding-left:1.1rem">
    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
  </ul>
</div>
@endif

@php $kyc = $user->kycVerification; @endphp
@if(! $kyc || $kyc->status !== \App\Models\KycVerification::STATUS_APPROUVE)
<div class="alert alert-warning" style="margin-bottom:1rem">
  <i class="fas fa-triangle-exclamation"></i>
  Ce client n'a pas de vérification d'identité (KYC) approuvée
  @if($kyc && $kyc->status === \App\Models\KycVerification::STATUS_EN_ATTENTE)
    — une demande est en attente de traitement.
  @elseif($kyc && $kyc->status === \App\Models\KycVerification::STATUS_REJETE)
    — sa dernière demande a été rejetée.
  @else
    — aucune demande n'a encore été soumise (compte créé directement par un admin, ou sans passer par une demande de prêt).
  @endif
  Vous pouvez tout de même attribuer ses coordonnées bancaires ci-dessous.
</div>
@endif

<div class="card-pro" style="padding:1.5rem;max-width:640px">
  <form data-confirm="Enregistrer les coordonnées bancaires et la carte de ce client ?" data-confirm-title="Coordonnées bancaires" data-confirm-ok="Enregistrer" method="POST" action="{{ route('admin.users.banking.store', $user) }}">
    @csrf

    <h3 style="font-size:.9rem;margin-bottom:1rem">Compte bancaire</h3>
    <div class="form-group">
      <label>IBAN *</label>
      <input type="text" name="iban" class="form-control-pro" value="{{ old('iban', $user->bankAccount?->iban) }}" required>
    </div>
    <div class="form-group">
      <label>BIC</label>
      <input type="text" name="bic" class="form-control-pro" maxlength="11" style="text-transform:uppercase;font-family:monospace;letter-spacing:.06em"
             value="{{ old('bic', $user->bankAccount?->bic ?? app(\App\Services\BankingProvisioner::class)->defaultBic()) }}">
      <div style="font-size:.74rem;color:var(--c-muted);margin-top:.3rem">8 ou 11 caractères. Valeur par défaut : réglable dans « Coordonnées du site ».</div>
    </div>

    <h3 style="font-size:.9rem;margin:1.5rem 0 1rem">Carte</h3>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="form-group">
          <label>Titulaire *</label>
          <input type="text" name="card_holder" class="form-control-pro" value="{{ old('card_holder', $user->card?->holder_name ?? $user->name) }}" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>4 derniers chiffres *</label>
          <input type="text" name="card_last_four" maxlength="4" class="form-control-pro" value="{{ old('card_last_four', $user->card?->last_four) }}" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Réseau *</label>
          <select name="card_network" class="form-control-pro" required>
            <option value="visa" {{ old('card_network', $user->card?->network) === 'visa' ? 'selected' : '' }}>Visa</option>
            <option value="mastercard" {{ old('card_network', $user->card?->network) === 'mastercard' ? 'selected' : '' }}>Mastercard</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Date d'expiration *</label>
          <input type="date" name="card_expires_at" class="form-control-pro" value="{{ old('card_expires_at', $user->card?->expires_at?->format('Y-m-d')) }}" required>
        </div>
      </div>
    </div>

    <button type="submit" class="btn-navy" style="margin-top:1.5rem">
      <i class="fas fa-check"></i> {{ $user->bankAccount ? 'Mettre à jour' : 'Attribuer' }}
    </button>
  </form>

  @if($user->bankAccount)
  <div style="display:flex;gap:.75rem;margin-top:1rem;border-top:1px solid var(--c-border);padding-top:1rem">
    <form data-confirm="Bloquer ou débloquer le compte et la carte de ce client ?" data-confirm-title="Bloquer / débloquer" data-confirm-ok="Confirmer" data-confirm-danger="1" method="POST" action="{{ route('admin.users.banking.toggle-block', $user) }}">
      @csrf
      <button type="submit" class="btn-ghost btn-sm-pro">
        @if($user->bankAccount->status === \App\Models\BankAccount::STATUS_ACTIVE)
          <i class="fas fa-lock"></i> Bloquer
        @else
          <i class="fas fa-lock-open"></i> Débloquer
        @endif
      </button>
    </form>
    <form method="POST" action="{{ route('admin.users.banking.destroy', $user) }}" onsubmit="return confirm('Retirer les coordonnées bancaires de ce client ?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-ghost btn-sm-pro" style="border-color:var(--c-red);color:var(--c-red)">
        <i class="fas fa-trash"></i> Retirer
      </button>
    </form>
  </div>
  @endif
</div>

@endsection
