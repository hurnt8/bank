@extends('layouts.dashboard')
@section('title', 'Fiche client — ' . $user->name)
@section('page_title', 'Fiche client')

@push('styles')
<style>
/* ── Client Show — préfixe cu- ── */
.cu-profile {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 16px;
  padding: 1.5rem 1.75rem;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  margin-bottom: 1.5rem;
  display: flex; align-items: flex-start; gap: 1.25rem; flex-wrap: wrap;
}
.cu-avatar {
  width: 64px; height: 64px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; font-weight: 800; color: #fff; flex-shrink: 0;
}
.cu-info { flex: 1; min-width: 0; }
.cu-name  { font-size: 1.25rem; font-weight: 800; color: var(--c-navy); margin-bottom: .2rem; }
.cu-email { font-size: .82rem; color: var(--c-muted); margin-bottom: .5rem; }
.cu-badges { display: flex; gap: .375rem; flex-wrap: wrap; }
.cu-profile-actions { display: flex; gap: .5rem; align-items: flex-start; flex-shrink: 0; }

.cu-meta-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: .75rem;
  margin-bottom: 1.5rem;
}
.cu-meta {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 10px;
  padding: .75rem 1rem;
}
.cu-meta-lbl {
  font-size: .62rem; font-weight: 700; color: var(--c-muted);
  text-transform: uppercase; letter-spacing: .06em; margin-bottom: .2rem;
}
.cu-meta-val { font-size: .875rem; font-weight: 600; color: var(--c-navy); }

.cu-table-card {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  overflow: hidden;
}
.cu-table-hdr {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--c-border);
}
.cu-table-title {
  font-size: .875rem; font-weight: 700; color: var(--c-navy);
  display: flex; align-items: center; gap: .5rem;
}
.cu-table-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--c-accent); }
.cu-link-all {
  font-size: .7rem; font-weight: 700; color: var(--c-muted);
  text-decoration: none; display: flex; align-items: center; gap: .3rem;
  transition: color .15s;
}
.cu-link-all:hover { color: var(--c-navy); }

/* Responsive */
@media(max-width:640px) {
  .cu-table-card { overflow: visible; }
  .cu-profile { padding: 1rem; gap: .875rem; }
}
@media(max-width:575px) {
  .cu-table-hdr { flex-wrap: wrap; gap: .5rem; }
  .cu-avatar { width: 48px; height: 48px; font-size: 1.1rem; }
  .cu-name { font-size: 1.05rem; }
  .cu-profile-actions { width: 100%; }
  .cu-profile-actions .btn-navy { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')

@php
  $palette = ['#2563EB','#059669','#D97706','#7C3AED','#DC2626','#0D9488','#0657A4'];
  $avatarBg = $palette[crc32($user->email) % count($palette)];
  $stMap = [
    'draft'           => ['lbl' => 'Brouillon',      'cls' => 'bs-gray'],
    'pending'         => ['lbl' => 'En attente',     'cls' => 'bs-amber'],
    'validated'       => ['lbl' => 'Validé',         'cls' => 'bs-blue'],
    'contract_sent'   => ['lbl' => 'Contrat envoyé', 'cls' => 'bs-violet'],
    'contract_signed' => ['lbl' => 'Contrat signé',  'cls' => 'bs-emerald'],
    'finalized'       => ['lbl' => 'Finalisé',       'cls' => 'bs-green'],
    'rejected'        => ['lbl' => 'Refusé',         'cls' => 'bs-red'],
  ];
@endphp

@if ($errors->any())
<div class="flash flash-err">
  <i class="fas fa-exclamation-triangle"></i>
  <div>
    <strong>Impossible d'enregistrer :</strong>
    <ul style="margin:.25rem 0 0 1.1rem;padding:0">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
</div>
@endif

{{-- ── En-tête de page ── --}}
<div class="page-hdr-row" style="margin-bottom:1.25rem">
  <div class="page-hdr">
    <h1>Fiche client</h1>
    <p>Profil complet et historique des dossiers de <strong>{{ $user->name }}</strong></p>
  </div>
  <div class="page-hdr-actions">
    <a href="{{ route('admin.users') }}" class="btn-navy" style="background:var(--c-bg);color:var(--c-muted);border:1px solid var(--c-border)">
      <i class="fas fa-arrow-left"></i> Retour
    </a>
  </div>
</div>

{{-- ── Profil ── --}}
<div class="cu-profile">
  <div class="cu-avatar" style="background:{{ $avatarBg }}">
    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
  </div>

  <div class="cu-info">
    <div class="cu-name">{{ $user->name }}</div>
    <div class="cu-email">{{ $user->email }}</div>
    <div class="cu-badges">
      @if($user->invitation_token)
        <span class="badge-status bs-amber">
          <i class="fas fa-clock" style="font-size:.55rem"></i> En attente d'activation
        </span>
      @else
        <span class="badge-status bs-green">
          <i class="fas fa-check" style="font-size:.55rem"></i> Compte activé
        </span>
      @endif
      @foreach($user->getRoleNames() as $r)
        <span class="badge-status bs-blue">{{ ucfirst($r) }}</span>
      @endforeach
    </div>
  </div>

  <div class="cu-profile-actions">
    @if($user->invitation_token)
    <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST">
      @csrf
      <button type="submit" class="btn-navy" style="background:rgba(5,150,105,.1);color:#059669;border:1px solid rgba(5,150,105,.25)">
        <i class="fas fa-paper-plane"></i> Renvoyer l'invitation
      </button>
    </form>
    @endif
    @if($user->hasRole('client'))
      @if($user->kycVerification && $user->kycVerification->status === \App\Models\KycVerification::STATUS_APPROUVE)
        <a href="{{ route('admin.users.banking.edit', $user) }}" class="btn-navy" style="background:rgba(6,87,164,.1);color:var(--c-accent, #0657A4);border:1px solid rgba(6,87,164,.25)">
          <i class="fas fa-university"></i> {{ $user->bankAccount ? 'Coordonnées bancaires' : 'Attribuer IBAN / Carte' }}
        </a>
      @else
        <span class="btn-navy" style="background:var(--c-bg);color:var(--c-muted);border:1px solid var(--c-border);cursor:not-allowed" title="KYC non approuvé — attribution impossible">
          <i class="fas fa-university"></i> IBAN / Carte (KYC requis)
        </span>
      @endif
    @endif
    <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#editModal">
      <i class="fas fa-pen"></i> Modifier
    </button>
  </div>
</div>

{{-- ── Informations personnelles ── --}}
<div class="cu-meta-grid">
  <div class="cu-meta">
    <div class="cu-meta-lbl">Téléphone</div>
    <div class="cu-meta-val">{{ $user->phone ?? '—' }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Adresse</div>
    <div class="cu-meta-val" style="font-size:.8rem;font-weight:500">{{ $user->address ?? '—' }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Date de naissance</div>
    <div class="cu-meta-val">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : '—' }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Pièce d'identité</div>
    <div class="cu-meta-val" style="font-size:.8rem">
      {{ $user->id_type ? ucfirst(str_replace('_', ' ', $user->id_type)) : '—' }}
      @if($user->id_number)
        <span style="color:var(--c-muted);font-weight:400"> · {{ $user->id_number }}</span>
      @endif
    </div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Date de délivrance</div>
    <div class="cu-meta-val" style="font-size:.8rem">{{ $user->date_delivre ? $user->date_delivre->format('d/m/Y') : '—' }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Numéro fiscal</div>
    <div class="cu-meta-val" style="font-size:.8rem">{{ $user->tax_number ?? '—' }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Activité</div>
    <div class="cu-meta-val" style="font-size:.8rem">{{ $user->activity ?? '—' }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Devise</div>
    <div class="cu-meta-val">{{ $user->currency ?? '—' }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Langue</div>
    <div class="cu-meta-val">{{ strtoupper($user->locale ?? 'FR') }}</div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Solde</div>
    <div class="cu-meta-val" style="color:var(--c-green)">
      {{ number_format($user->balance ?? 0, 2, ',', ' ') }} {{ $user->currency ?? '' }}
    </div>
  </div>
  <div class="cu-meta">
    <div class="cu-meta-lbl">Membre depuis</div>
    <div class="cu-meta-val">{{ $user->created_at->format('d/m/Y') }}</div>
  </div>
</div>

{{-- ── KPIs prêts ── --}}
<div class="metrics-grid mb-4" style="grid-template-columns:repeat(4,1fr)">
  <div class="metric-card">
    <div class="metric-card__icon mi-navy"><i class="fas fa-folder-open"></i></div>
    <div class="metric-card__val">{{ $loanStats['total'] }}</div>
    <div class="metric-card__lbl">Total dossiers</div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-amber"><i class="fas fa-clock"></i></div>
    <div class="metric-card__val">{{ $loanStats['pending'] }}</div>
    <div class="metric-card__lbl">En cours</div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-blue"><i class="fas fa-file-signature"></i></div>
    <div class="metric-card__val">{{ $loanStats['active'] }}</div>
    <div class="metric-card__lbl">Actifs</div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-check-circle"></i></div>
    <div class="metric-card__val">{{ $loanStats['finalized'] }}</div>
    <div class="metric-card__lbl">Finalisés</div>
  </div>
</div>

{{-- ── Tableau des dossiers ── --}}
<div class="cu-table-card">
  <div class="cu-table-hdr">
    <div class="cu-table-title">
      <div class="cu-table-dot"></div>
      Dossiers de prêt ({{ $loanStats['total'] }})
    </div>
    <a href="{{ route('admin.loans.index') }}?search={{ urlencode($user->email) }}" class="cu-link-all">
      Voir dans les prêts <i class="fas fa-arrow-right" style="font-size:.55rem"></i>
    </a>
  </div>

  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Montant</th>
          <th>Objet</th>
          <th>Statut</th>
          <th>Admin</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        @php $stInfo = $stMap[$loan->status] ?? ['lbl' => ucfirst($loan->status ?? '—'), 'cls' => 'bs-gray']; @endphp
        <tr>
          <td data-label="Référence" class="cell-mono" style="font-size:.75rem">
            {{ $loan->reference ?? '#'.$loan->id }}
          </td>
          <td data-label="Montant" style="font-weight:800;color:var(--c-navy);white-space:nowrap">
            {{ number_format($loan->amount ?? 0, 0, ',', ' ') }}&nbsp;{{ $loan->currency ?? '€' }}
          </td>
          <td data-label="Objet" style="font-size:.77rem;color:var(--c-muted);max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ Str::limit($loan->objet ?? '—', 22) }}
          </td>
          <td data-label="Statut">
            <span class="badge-status {{ $stInfo['cls'] }}">{{ $stInfo['lbl'] }}</span>
          </td>
          <td data-label="Admin" style="font-size:.77rem;color:var(--c-muted)">
            {{ $loan->admin?->name ?? '—' }}
          </td>
          <td data-label="Date" style="font-size:.72rem;color:var(--c-muted);white-space:nowrap">
            {{ $loan->created_at->format('d/m/Y') }}
          </td>
          <td data-label="" style="display:flex;gap:.35rem">
            <a href="{{ route('admin.loans.show', $loan) }}" class="btn-icon btn-icon-primary" title="Voir le dossier">
              <i class="fas fa-eye"></i>
            </a>
            <form action="{{ route('admin.loans.destroy', $loan) }}" method="POST"
                  data-confirm="Ce dossier de pret sera definitivement supprime. Cette action est irreversible."
            data-confirm-title="Supprimer ce dossier ?" data-confirm-ok="Supprimer" data-confirm-danger="1">
              @csrf @method('DELETE')
              <button type="submit" class="btn-icon btn-icon-danger" title="Supprimer le dossier">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="padding:2.5rem;text-align:center">
            <i class="fas fa-folder-open" style="font-size:2rem;color:var(--c-muted);opacity:.25;display:block;margin-bottom:.75rem"></i>
            <p style="font-size:.8rem;color:var(--c-muted)">Aucun dossier pour ce client</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ── Modal édition ── --}}
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:var(--radius);border:none;box-shadow:var(--shadow)">

      <div class="modal-header" style="border-bottom:1px solid var(--c-border);padding:1.125rem 1.5rem">
        <div style="display:flex;align-items:center;gap:.875rem">
          <div style="width:40px;height:40px;border-radius:50%;
                      background:{{ $avatarBg }};
                      display:flex;align-items:center;justify-content:center;
                      font-size:.8rem;font-weight:800;color:#fff;flex-shrink:0">
            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
          </div>
          <div>
            <div style="font-size:.9375rem;font-weight:700;color:var(--c-navy)">Modifier {{ $user->name }}</div>
            <div style="font-size:.75rem;color:var(--c-muted)">{{ $user->email }}</div>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-body" style="padding:1.5rem">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-pro">Nom complet *</label>
              <input type="text" name="name" class="form-control-pro"
                     value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-pro">Adresse email *</label>
              <input type="email" name="email" class="form-control-pro"
                     value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Genre</label>
              <select name="gender" class="form-control-pro">
                <option value="N" {{ old('gender', $user->gender ?? 'N') === 'N' ? 'selected' : '' }}>— Non précisé</option>
                <option value="M" {{ old('gender', $user->gender ?? 'N') === 'M' ? 'selected' : '' }}>♂ Monsieur / Mr.</option>
                <option value="F" {{ old('gender', $user->gender ?? 'N') === 'F' ? 'selected' : '' }}>♀ Madame / Ms.</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Téléphone</label>
              <input type="text" name="phone" class="form-control-pro"
                     value="{{ old('phone', $user->phone) }}" placeholder="+33 6 00 00 00 00">
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Type de compte</label>
              <select name="type" class="form-control-pro">
                <option value="client" {{ old('type', $user->type) === 'client' ? 'selected' : '' }}>Client</option>
                @if($isSuperAdmin)
                <option value="staff"  {{ old('type', $user->type) === 'staff'  ? 'selected' : '' }}>Personnel</option>
                @endif
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Rôle</label>
              <select name="role" class="form-control-pro">
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                  {{ ucfirst($role->name) }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-pro">Adresse postale</label>
              <input type="text" name="address" class="form-control-pro"
                     value="{{ old('address', $user->address) }}"
                     placeholder="12 rue de la Paix, 75001 Paris">
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Date de naissance</label>
              <input type="date" name="birth_date" class="form-control-pro"
                     value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-5">
              <label class="form-label-pro">Type de pièce d'identité</label>
              <select name="id_type" class="form-control-pro">
                <option value="">— Non renseigné</option>
                <option value="passeport"       {{ old('id_type', $user->id_type) === 'passeport'       ? 'selected' : '' }}>Passeport</option>
                <option value="cni"             {{ old('id_type', $user->id_type) === 'cni'             ? 'selected' : '' }}>Carte nationale d'identité</option>
                <option value="permis_conduire" {{ old('id_type', $user->id_type) === 'permis_conduire' ? 'selected' : '' }}>Permis de conduire</option>
                <option value="titre_sejour"    {{ old('id_type', $user->id_type) === 'titre_sejour'    ? 'selected' : '' }}>Titre de séjour</option>
                <option value="autre"           {{ old('id_type', $user->id_type) === 'autre'           ? 'selected' : '' }}>Autre document</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label-pro">Numéro de pièce</label>
              <input type="text" name="id_number" class="form-control-pro"
                     value="{{ old('id_number', $user->id_number) }}"
                     placeholder="Ex : AB123456">
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Date de délivrance</label>
              <input type="date" name="date_delivre" class="form-control-pro"
                     value="{{ old('date_delivre', $user->date_delivre?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label-pro">Numéro fiscal</label>
              <input type="text" name="tax_number" class="form-control-pro"
                     value="{{ old('tax_number', $user->tax_number) }}"
                     placeholder="Ex : FR123456789">
            </div>
            <div class="col-md-6">
              <label class="form-label-pro">Activité exercée</label>
              <input type="text" name="activity" class="form-control-pro"
                     value="{{ old('activity', $user->activity) }}"
                     placeholder="Ex : Commerçant">
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Devise</label>
              <select name="currency" class="form-control-pro">
                @foreach(\App\Models\Currency::codes() as $cur)
                <option value="{{ $cur }}" {{ old('currency', $user->currency) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Langue</label>
              <select name="locale" class="form-control-pro">
                @foreach(['fr'=>'Français','en'=>'English','es'=>'Español','pl'=>'Polski','bg'=>'Български','hu'=>'Magyar','it'=>'Italiano','de'=>'Deutsch','lt'=>'Lietuvių','ro'=>'Română','lv'=>'Latviešu','nl'=>'Nederlands','pt'=>'Português','hr'=>'Hrvatski'] as $lc => $llabel)
                <option value="{{ $lc }}" {{ old('locale', $user->locale) === $lc ? 'selected' : '' }}>{{ $llabel }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:.875rem 1.5rem;gap:.5rem">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn-navy"><i class="fas fa-save"></i> Enregistrer</button>
        </div>
      </form>

    </div>
  </div>
</div>

@if ($errors->any() || session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
  var modalEl = document.getElementById('editModal');
  if (modalEl && window.bootstrap) {
    new bootstrap.Modal(modalEl).show();
  }
});
</script>
@endif

@endsection
