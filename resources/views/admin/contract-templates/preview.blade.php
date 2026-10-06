@extends('layouts.dashboard')
@section('title', 'Aperçu — ' . $template->name)
@section('page_title', 'Aperçu du modèle')

@push('styles')
<style>
.preview-page { display:grid; grid-template-columns:300px 1fr; gap:1.25rem; align-items:start }
@media(max-width:1024px){ .preview-page{ grid-template-columns:1fr } }

.preview-sidebar { display:flex; flex-direction:column; gap:1rem }

.tpl-banner { background:linear-gradient(135deg,#0E3B2E,#14503D); border-radius:14px; padding:1.375rem 1.25rem; color:#fff }
.tpl-banner-name { font-size:.9375rem; font-weight:800; margin-bottom:.375rem; line-height:1.3 }
.tpl-banner-type { display:inline-flex; align-items:center; gap:.35rem; background:rgba(198,161,91,.15); border:1px solid rgba(198,161,91,.3); border-radius:999px; padding:.2rem .65rem; font-size:.68rem; font-weight:700; color:#C6A15B; margin-bottom:1rem }
.tpl-banner-type.html-type { background:rgba(198,161,91,.15); border-color:rgba(198,161,91,.3); color:#DCBE87 }

.meta-row { display:flex; align-items:center; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid rgba(255,255,255,.07) }
.meta-row:last-child { border-bottom:0 }
.meta-lbl { font-size:.7rem; color:rgba(255,255,255,.45); font-weight:500 }
.meta-val { font-size:.78rem; font-weight:600; color:#fff }

.pdf-wrap { background:var(--c-card,#fff); border:1px solid var(--c-border); border-radius:14px; overflow:hidden }
.pdf-toolbar { display:flex; align-items:center; justify-content:space-between; padding:.75rem 1.125rem; border-bottom:1px solid var(--c-border); gap:.75rem; flex-wrap:wrap }
.pdf-toolbar-title { font-size:.8125rem; font-weight:700; color:var(--c-navy); display:flex; align-items:center; gap:.5rem }
.pdf-toolbar-actions { display:flex; gap:.5rem }
.pdf-frame { width:100%; height:80vh; border:none; display:block; background:#525659 }

.vars-card { background:var(--c-card,#fff); border:1px solid var(--c-border); border-radius:14px; overflow:hidden }
.vars-hdr { padding:.75rem 1rem; border-bottom:1px solid var(--c-border); font-size:.78rem; font-weight:700; color:var(--c-navy); display:flex; align-items:center; gap:.5rem }
.vars-body { padding:.75rem 1rem; display:flex; flex-direction:column; gap:.35rem; max-height:340px; overflow-y:auto }
.var-chip { display:flex; align-items:center; gap:.5rem; padding:.35rem .6rem; border-radius:7px; background:var(--c-bg,#f8f9fa); border:1px solid var(--c-border) }
.var-chip code { font-size:.68rem; font-weight:700; color:var(--c-accent-d,#b45309) }
.var-chip span { font-size:.67rem; color:var(--c-muted); flex:1; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
.var-chip .unknown { color:#ef4444; font-size:.67rem; font-style:italic }

.html-preview { padding:2rem; font-family:'DejaVu Sans',Arial,sans-serif; font-size:.85rem; line-height:1.7; color:#1a1a2e; max-height:80vh; overflow-y:auto }
</style>
@endpush

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr" style="margin-bottom:1.25rem">
  <div>
    <div style="font-size:1.0625rem;font-weight:800;color:var(--c-navy)">
      {{ $template->name }}
    </div>
    <div style="font-size:.78rem;color:var(--c-muted);margin-top:.2rem">
      Modèle HTML — DomPDF (Times New Roman)
      @if(count($detectedBalises))
        · {{ count($detectedBalises) }} variable(s) détectée(s)
      @endif
    </div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.contract-templates.edit', $template) }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-pen"></i> Modifier
    </a>
    <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Retour
    </a>
  </div>
</div>

<div class="preview-page">

  {{-- ── Sidebar ── --}}
  <div class="preview-sidebar">

    {{-- Bannière modèle --}}
    <div class="tpl-banner">
      <div class="tpl-banner-name">{{ $template->name }}</div>
      <div class="meta-row">
        <span class="meta-lbl">Variables</span>
        <span class="meta-val">{{ count($detectedBalises) }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-lbl">Créé le</span>
        <span class="meta-val">{{ $template->created_at?->format('d/m/Y') ?? '—' }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-lbl">Mis à jour</span>
        <span class="meta-val">{{ $template->updated_at?->format('d/m/Y') ?? '—' }}</span>
      </div>
    </div>

    {{-- Téléchargements --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Fichiers</div>
      </div>
      <div class="card-pro-body" style="display:flex;flex-direction:column;gap:.5rem">
        <a href="{{ route('admin.contract-templates.preview-pdf', $template) }}"
           target="_blank"
           class="btn-ghost btn-sm-pro"
           style="justify-content:center;text-align:center">
          <i class="fas fa-file-pdf" style="color:#ef4444"></i> Ouvrir PDF dans un onglet
        </a>
        <a href="{{ route('admin.contract-templates.edit', $template) }}"
           class="btn-navy btn-sm-pro"
           style="justify-content:center;text-align:center">
          <i class="fas fa-pen"></i> Modifier le template
        </a>
      </div>
    </div>

    {{-- Variables détectées --}}
    @if(!empty($detectedBalises))
    <div class="vars-card">
      <div class="vars-hdr">
        <i class="fas fa-brackets-curly" style="color:var(--c-accent)"></i>
        Variables détectées
        <span style="margin-left:auto;background:var(--c-bg);border:1px solid var(--c-border);border-radius:999px;padding:.1rem .5rem;font-size:.68rem;font-weight:600;color:var(--c-muted)">
          {{ count($detectedBalises) }}
        </span>
      </div>
      <div class="vars-body">
        @foreach($detectedBalises as $tag => $desc)
        <div class="var-chip">
          <code>{{ $tag }}</code>
          @if($desc)
            <span>{{ $desc }}</span>
          @else
            <span class="unknown">Variable personnalisée</span>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif

  </div>

  {{-- ── Zone principale : PDF --}}
  <div>
    <div class="pdf-wrap">
      <div class="pdf-toolbar">
        <div class="pdf-toolbar-title">
          <i class="fas fa-file-pdf" style="color:#ef4444"></i>
          Aperçu PDF du contrat
        </div>
        <div class="pdf-toolbar-actions">
          <a href="{{ route('admin.contract-templates.preview-pdf', $template) }}"
             target="_blank" class="btn-ghost btn-sm-pro">
            <i class="fas fa-external-link-alt"></i> Onglet
          </a>
          <a href="{{ route('admin.contract-templates.edit', $template) }}"
             class="btn-ghost btn-sm-pro">
            <i class="fas fa-pen"></i> Modifier
          </a>
        </div>
      </div>
      <iframe
        src="{{ route('admin.contract-templates.preview-pdf', $template) }}"
        class="pdf-frame"
        title="Aperçu {{ $template->name }}"
        loading="lazy"
      ></iframe>
    </div>
  </div>

</div>

@endsection
