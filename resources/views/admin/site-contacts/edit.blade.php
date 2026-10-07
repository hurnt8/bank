@extends('layouts.dashboard')
@section('title', 'Coordonnées du site — ' . site_name())
@section('page_title', 'Coordonnées du site')

@section('content')
<style>
.sw-row{display:flex;align-items:flex-start;gap:.9rem;padding:.8rem 0;border-bottom:1px solid var(--c-border);cursor:pointer}
.sw-row:last-child{border-bottom:0}
.sw-input{position:absolute;opacity:0;pointer-events:none}
.sw-track{flex:none;width:46px;height:26px;border-radius:999px;background:var(--c-border);position:relative;transition:background .2s;margin-top:.1rem}
.sw-thumb{position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.3);transition:transform .2s}
.sw-input:checked + .sw-track{background:#16a34a}
.sw-input:checked + .sw-track .sw-thumb{transform:translateX(20px)}
.sw-input:focus-visible + .sw-track{outline:2px solid var(--c-accent);outline-offset:2px}
.sw-text{display:flex;flex-direction:column;gap:.15rem;font-size:.85rem}
.sw-text small{color:var(--c-muted);font-size:.75rem;line-height:1.45}
</style>


<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Coordonnées du site</h4>
    <p>Adresses, téléphones et email affichés dans le pied de page et la page contact du site public</p>
  </div>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.site-contacts.update') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-4">

  <div class="col-12">
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Identité du site</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Nom du site *</label>
            <input type="text" name="name" class="form-control-pro" value="{{ old('name', $contact->name) }}" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Logo — fond clair</label>
            @if($contact->logo_light_path)
            <div class="mb-2">
              <img src="{{ Storage::url($contact->logo_light_path) }}" alt="Logo fond clair" style="max-height:48px;background:#f0f2f5;padding:.5rem;border-radius:8px">
              <label class="ms-2" style="font-size:.8rem"><input type="checkbox" name="remove_logo_light" value="1"> Supprimer</label>
            </div>
            @endif
            <input type="file" name="logo_light" accept="image/*" class="form-control-pro">
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Logo — fond sombre</label>
            @if($contact->logo_dark_path)
            <div class="mb-2">
              <img src="{{ Storage::url($contact->logo_dark_path) }}" alt="Logo fond sombre" style="max-height:48px;background:#0E3B2E;padding:.5rem;border-radius:8px">
              <label class="ms-2" style="font-size:.8rem"><input type="checkbox" name="remove_logo_dark" value="1"> Supprimer</label>
            </div>
            @endif
            <input type="file" name="logo_dark" accept="image/*" class="form-control-pro">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Signature email</div>
      </div>
      <div class="card-pro-body">
        <p style="font-size:.8rem;color:var(--c-muted);margin-bottom:.75rem">
          Image de signature (manuscrite/scannée) affichée en bas de tous les emails envoyés par l'application, avec l'adresse et l'email ci-dessous.
        </p>
        @if($contact->email_signature_path)
        <div class="mb-2">
          <img src="{{ Storage::url($contact->email_signature_path) }}" alt="Signature email" style="max-height:64px;background:#f0f2f5;padding:.5rem;border-radius:8px">
          <label class="ms-2" style="font-size:.8rem"><input type="checkbox" name="remove_email_signature" value="1"> Supprimer</label>
        </div>
        @endif
        <input type="file" name="email_signature" accept="image/*" class="form-control-pro">
      </div>
    </div>
  </div>

  <div class="col-xl-6">
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Adresses</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Adresse 1 *</label>
            <input type="text" name="address_1" class="form-control-pro" value="{{ old('address_1', $contact->address_1) }}">
          </div>
          <div class="col-12">
            <label class="form-label-pro">Adresse 2</label>
            <input type="text" name="address_2" class="form-control-pro" value="{{ old('address_2', $contact->address_2) }}">
          </div>
          <div class="col-12">
            <label class="form-label-pro">Adresse 3</label>
            <input type="text" name="address_3" class="form-control-pro" value="{{ old('address_3', $contact->address_3) }}">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-6">
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Téléphones</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Téléphone 1</label>
            <input type="text" name="phone_1" class="form-control-pro" value="{{ old('phone_1', $contact->phone_1) }}">
          </div>
          <div class="col-12">
            <label class="form-label-pro">Téléphone 2</label>
            <input type="text" name="phone_2" class="form-control-pro" value="{{ old('phone_2', $contact->phone_2) }}">
          </div>
        </div>
      </div>
    </div>

    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Email</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Adresse e-mail</label>
            <input type="email" name="email" class="form-control-pro" value="{{ old('email', $contact->email) }}" placeholder="contact@exemple.com">
          </div>
        </div>
      </div>
    </div>

    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Connexion et e-mails</div>
      </div>
      <div class="card-pro-body">
      <label class="sw-row">
        <input type="hidden" name="otp_clients_enabled" value="0">
        <input type="checkbox" name="otp_clients_enabled" value="1" class="sw-input" {{ old('otp_clients_enabled', $contact->otp_clients_enabled) ? 'checked' : '' }}>
        <span class="sw-track" aria-hidden="true"><span class="sw-thumb"></span></span>
        <span class="sw-text"><strong>Code OTP à la connexion — clients</strong><small>Activé : chaque client reçoit un code par e-mail à chaque connexion. Désactivé : connexion directe, aucun e-mail OTP.</small></span>
      </label>
      <label class="sw-row">
        <input type="hidden" name="otp_staff_enabled" value="0">
        <input type="checkbox" name="otp_staff_enabled" value="1" class="sw-input" {{ old('otp_staff_enabled', $contact->otp_staff_enabled) ? 'checked' : '' }}>
        <span class="sw-track" aria-hidden="true"><span class="sw-thumb"></span></span>
        <span class="sw-text"><strong>Code OTP à la connexion — personnel</strong><small>Activé : administrateurs et super-administrateurs reçoivent un code par e-mail. Désactivé : connexion directe (moins sécurisé).</small></span>
      </label>
      <label class="sw-row">
        <input type="hidden" name="activation_mail_enabled" value="0">
        <input type="checkbox" name="activation_mail_enabled" value="1" class="sw-input" {{ old('activation_mail_enabled', $contact->activation_mail_enabled) ? 'checked' : '' }}>
        <span class="sw-track" aria-hidden="true"><span class="sw-thumb"></span></span>
        <span class="sw-text"><strong>E-mail d’activation du compte</strong><small>Activé : tout nouvel inscrit reçoit un e-mail d’activation. Désactivé : aucun e-mail n’est envoyé et le compte est activé automatiquement.</small></span>
      </label>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Coordonnées bancaires par défaut</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label-pro">BIC par défaut</label>
            <input type="text" name="default_bic" class="form-control-pro" maxlength="11" style="text-transform:uppercase;font-family:monospace;letter-spacing:.06em"
                   value="{{ old('default_bic', $contact->default_bic) }}" placeholder="{{ app(\App\Services\BankingProvisioner::class)->defaultBic() }}">
            <div style="font-size:.74rem;color:var(--c-muted);margin-top:.3rem">8 ou 11 caractères (ex. SOLBDEFF). Attribué aux IBAN générés automatiquement ; laissez vide pour utiliser la valeur proposée.</div>
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Code banque (BLZ) des IBAN générés</label>
            <input type="text" name="iban_bank_code" class="form-control-pro" maxlength="8" inputmode="numeric" style="font-family:monospace;letter-spacing:.06em"
                   value="{{ old('iban_bank_code', $contact->iban_bank_code) }}" placeholder="{{ app(\App\Services\BankingProvisioner::class)->bankCode() }}">
            <div style="font-size:.74rem;color:var(--c-muted);margin-top:.3rem">8 chiffres (IBAN allemand, DE). Le BIC de chaque client reste modifiable dans « Coordonnées bancaires » de sa fiche.</div>
          </div>
          <div class="col-sm-8">
            <label class="form-label-pro">IBAN de règlement des factures (par défaut)</label>
            <input type="text" name="payment_iban" class="form-control-pro" maxlength="40" style="font-family:monospace;text-transform:uppercase"
                   value="{{ old('payment_iban', $contact->payment_iban) }}" placeholder="DE00 0000 0000 0000 0000 00">
            <div style="font-size:.74rem;color:var(--c-muted);margin-top:.3rem">Pré-rempli dans les nouvelles factures et dans « Facturer les frais » ; modifiable facture par facture.</div>
          </div>
          <div class="col-sm-4">
            <label class="form-label-pro">BIC de règlement</label>
            <input type="text" name="payment_bic" class="form-control-pro" maxlength="11" style="font-family:monospace;text-transform:uppercase"
                   value="{{ old('payment_bic', $contact->payment_bic) }}" placeholder="SOLBDEFF">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Assistant WhatsApp</div>
      </div>
      <div class="card-pro-body">
        <p style="font-size:.8rem;color:var(--c-muted);margin-bottom:.75rem">
          Bulle flottante affichée sur le site public, en bas de l'écran, qui ouvre une conversation WhatsApp. Visible uniquement si un numéro est renseigné et l'assistant activé.
        </p>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Numéro WhatsApp</label>
            <input type="text" name="whatsapp_number" class="form-control-pro"
                   value="{{ old('whatsapp_number', $contact->whatsapp_number) }}" placeholder="+33612345678">
            <div class="form-help" style="font-size:.72rem;color:var(--c-muted);margin-top:.3rem">Format international, avec l'indicatif pays (ex : +33612345678).</div>
          </div>
          <div class="col-12">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
              <input type="hidden" name="whatsapp_enabled" value="0">
              <input type="checkbox" name="whatsapp_enabled" value="1"
                     {{ old('whatsapp_enabled', $contact->whatsapp_enabled) ? 'checked' : '' }}>
              <span style="font-size:.85rem">Activer la bulle WhatsApp sur le site public</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <button type="submit" class="btn-navy">
      <i class="fas fa-save"></i> Enregistrer
    </button>
  </div>

</div>
</form>

@endsection
