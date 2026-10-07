@extends('layouts.dashboard')
@section('title', 'Modifier un réseau social — ' . site_name())
@section('page_title', 'Modifier un réseau social')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Modifier — {{ $link->label }}</h4>
    <p>Le lien apparaîtra dans le pied de page et sur la page contact du site public</p>
  </div>
  <a href="{{ route('admin.social-links.index') }}" class="btn-ghost">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<form data-confirm="Enregistrer les modifications de ce réseau social ?" data-confirm-title="Réseau social" data-confirm-ok="Enregistrer" action="{{ route('admin.social-links.update', $link) }}" method="POST">
@csrf
@method('PUT')
<div class="row g-4">

  <div class="col-xl-6">
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Réseau social</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label-pro">Plateforme *</label>
            <input type="text" name="platform" class="form-control-pro" value="{{ old('platform', $link->platform) }}" placeholder="facebook" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-pro">Libellé *</label>
            <input type="text" name="label" class="form-control-pro" value="{{ old('label', $link->label) }}" placeholder="Facebook" required>
          </div>
          <div class="col-12">
            <label class="form-label-pro">Classe d'icône *</label>
            <input type="text" name="icon_class" class="form-control-pro" value="{{ old('icon_class', $link->icon_class) }}" placeholder="fab fa-facebook-f" required>
            <div class="form-help" style="font-size:.72rem;color:var(--c-muted);margin-top:.3rem">Classe Font Awesome, ex : fab fa-facebook-f</div>
          </div>
          <div class="col-12">
            <label class="form-label-pro">URL *</label>
            <input type="text" name="url" class="form-control-pro" value="{{ old('url', $link->url) }}" placeholder="https://facebook.com/votrepage" required>
          </div>
          <div class="col-md-6">
            <label class="form-label-pro">Ordre d'affichage</label>
            <input type="number" name="sort_order" class="form-control-pro" value="{{ old('sort_order', $link->sort_order) }}">
          </div>
          <div class="col-md-6" style="display:flex;align-items:flex-end">
            <label style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:var(--c-navy);font-weight:600;cursor:pointer;padding-bottom:.55rem">
              <input type="hidden" name="is_visible" value="0">
              <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', $link->is_visible ? '1' : '0') == '1' ? 'checked' : '' }}>
              Visible sur le site
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
