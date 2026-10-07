@extends('layouts.dashboard')
@section('title', 'Langues — ' . site_name())
@section('page_title', 'Langues')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Langues</h4>
    <p>Cochez les langues à proposer dans le sélecteur du site public et de l'espace client. Une langue décochée n'est plus accessible.</p>
  </div>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<form data-confirm="Enregistrer les langues disponibles sur le site ?" data-confirm-title="Langues" data-confirm-ok="Enregistrer" action="{{ route('admin.languages.update') }}" method="POST">
@csrf
@method('PUT')

<div class="card-pro">
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th style="width:52px"></th>
          <th>Langue</th>
          <th>Code</th>
          <th style="width:110px">Ordre</th>
          <th style="width:110px">Visible</th>
        </tr>
      </thead>
      <tbody>
        @foreach($languages as $lang)
        <tr>
          <td data-label="">
            <img src="{{ asset('images/'.$lang->code.'.'.$lang->flag_ext) }}" alt="{{ $lang->code }}"
                 style="width:28px;height:auto;border-radius:3px;display:block">
          </td>

          <td data-label="Langue">
            <div class="cell-name">{{ $lang->native_name }}</div>
          </td>

          <td data-label="Code" style="color:var(--c-muted);font-size:.78rem;text-transform:uppercase">
            {{ $lang->code }}
          </td>

          <td data-label="Ordre">
            <input type="number" name="languages[{{ $lang->id }}][sort_order]"
                   value="{{ old("languages.{$lang->id}.sort_order", $lang->sort_order) }}"
                   class="form-control-pro" style="max-width:80px" min="0">
          </td>

          <td data-label="Visible">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
              <input type="hidden" name="languages[{{ $lang->id }}][is_visible]" value="0">
              <input type="checkbox" name="languages[{{ $lang->id }}][is_visible]" value="1"
                     {{ old("languages.{$lang->id}.is_visible", $lang->is_visible) ? 'checked' : '' }}>
            </label>
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


{{-- Pagination --}}
@if($languages->hasPages())
<div class="card-pro" style="padding:1rem 1.25rem;margin-top:1rem">
  {{ $languages->links('partials.pagination') }}
</div>
@endif

@endsection
