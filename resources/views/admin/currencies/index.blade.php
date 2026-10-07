@extends('layouts.dashboard')
@section('title', 'Devises — ' . site_name())
@section('page_title', 'Devises')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Devises</h4>
    <p>Devises disponibles dans le projet (factures, transferts). Une devise désactivée disparaît des listes déroulantes mais reste affichée sur les enregistrements existants.</p>
  </div>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<form data-confirm="Enregistrer les devises (devise par défaut et activation) ?" data-confirm-title="Devises" data-confirm-ok="Enregistrer" action="{{ route('admin.currencies.update') }}" method="POST">
@csrf
@method('PUT')

<div class="card-pro">
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th>Code</th>
          <th>Nom</th>
          <th style="width:100px">Symbole</th>
          <th style="width:140px">Taux de change</th>
          <th style="width:100px">Ordre</th>
          <th style="width:80px">Actif</th>
          <th style="width:80px">Défaut</th>
          <th style="text-align:right;width:56px"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($currencies as $currency)
        <tr>
          <td data-label="Code" style="color:var(--c-muted);font-size:.78rem;text-transform:uppercase">
            {{ $currency->code }}
          </td>

          <td data-label="Nom">
            <input type="text" name="currencies[{{ $currency->id }}][name]"
                   value="{{ old("currencies.{$currency->id}.name", $currency->name) }}"
                   class="form-control-pro" required>
          </td>

          <td data-label="Symbole">
            <input type="text" name="currencies[{{ $currency->id }}][symbol]"
                   value="{{ old("currencies.{$currency->id}.symbol", $currency->symbol) }}"
                   class="form-control-pro" required>
          </td>

          <td data-label="Taux de change">
            <input type="number" name="currencies[{{ $currency->id }}][exchange_rate]"
                   value="{{ old("currencies.{$currency->id}.exchange_rate", $currency->exchange_rate) }}"
                   class="form-control-pro" step="0.000001" min="0.000001" required>
          </td>

          <td data-label="Ordre">
            <input type="number" name="currencies[{{ $currency->id }}][sort_order]"
                   value="{{ old("currencies.{$currency->id}.sort_order", $currency->sort_order) }}"
                   class="form-control-pro" style="max-width:80px" min="0">
          </td>

          <td data-label="Actif">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
              <input type="hidden" name="currencies[{{ $currency->id }}][is_active]" value="0">
              <input type="checkbox" name="currencies[{{ $currency->id }}][is_active]" value="1"
                     {{ old("currencies.{$currency->id}.is_active", $currency->is_active) ? 'checked' : '' }}>
            </label>
          </td>

          <td data-label="Défaut">
            <input type="radio" name="default_id" value="{{ $currency->id }}"
                   {{ old('default_id', $currency->is_default ? $currency->id : null) == $currency->id ? 'checked' : '' }}>
          </td>

          <td data-label="" style="text-align:right">
            @unless($currency->is_default)
            <button type="submit" form="delete-currency-{{ $currency->id }}"
                    data-confirm="La devise {{ $currency->code }} sera definitivement supprimee."
                    data-confirm-title="Supprimer cette devise ?" data-confirm-ok="Supprimer" data-confirm-danger="1"
                    class="btn-icon btn-icon-danger" title="Supprimer">
              <i class="fas fa-trash"></i>
            </button>
            @endunless
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  <button type="submit" class="btn-navy">
    <i class="fas fa-save"></i> Enregistrer
  </button>
</div>

</form>

{{-- Formulaires de suppression : hors du <form> groupé ci-dessus (un <form> ne peut pas
     être imbriqué dans un autre), reliés à leur bouton via l'attribut form="" du bouton. --}}
@foreach($currencies as $currency)
@unless($currency->is_default)
<form id="delete-currency-{{ $currency->id }}" action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" style="display:none">
  @csrf @method('DELETE')
</form>
@endunless
@endforeach

{{-- Pagination --}}
@if($currencies->hasPages())
<div class="card-pro" style="padding:1rem 1.25rem;margin-top:1rem">
  {{ $currencies->links('partials.pagination') }}
</div>
@endif

<div class="card-pro mt-4" style="padding:1.5rem">
  <h5 style="margin-bottom:1rem">Ajouter une devise</h5>
  <form data-confirm="Ajouter cette devise ?" data-confirm-title="Nouvelle devise" data-confirm-ok="Ajouter" action="{{ route('admin.currencies.store') }}" method="POST">
    @csrf
    <div class="d-flex flex-wrap gap-3">
      <div>
        <label class="form-label-pro">Code (ISO 4217)</label>
        <input type="text" name="code" value="{{ old('code') }}" class="form-control-pro"
               style="max-width:100px;text-transform:uppercase" maxlength="3" required>
      </div>
      <div>
        <label class="form-label-pro">Nom</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control-pro" required>
      </div>
      <div>
        <label class="form-label-pro">Symbole</label>
        <input type="text" name="symbol" value="{{ old('symbol') }}" class="form-control-pro" style="max-width:100px" required>
      </div>
      <div>
        <label class="form-label-pro">Taux de change</label>
        <input type="number" name="exchange_rate" value="{{ old('exchange_rate', 1) }}" class="form-control-pro"
               style="max-width:140px" step="0.000001" min="0.000001" required>
      </div>
      <div style="align-self:flex-end">
        <button type="submit" class="btn-navy">
          <i class="fas fa-plus"></i> Ajouter
        </button>
      </div>
    </div>
  </form>
</div>


@endsection
