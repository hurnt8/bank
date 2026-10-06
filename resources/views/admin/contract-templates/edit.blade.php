@extends('layouts.dashboard')
@section('title','Modifier le modèle')
@section('page_title','Modifier le modèle')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>{{ $template->name }}</h4>
    <div style="display:flex;align-items:center;gap:.5rem;margin-top:.35rem;flex-wrap:wrap">
      @if($template->is_default)
      <span class="badge-status bs-amber" style="font-size:.65rem">Par défaut</span>
      @endif
      @if($template->hasDocxTemplate())
      <span style="font-size:.68rem;padding:.15rem .5rem;border-radius:4px;font-weight:700;
                   background:#F0FDF4;color:#166534;border:1px solid #86EFAC">
        <i class="fas fa-file-word me-1"></i>DOCX v{{ $template->docx_version }} actif
      </span>
      @else
      <span style="font-size:.68rem;padding:.15rem .5rem;border-radius:4px;
                   background:#FEF9C3;color:#713F12;border:1px solid #FDE047">
        <i class="fas fa-file-word me-1"></i>Pas de DOCX
      </span>
      @endif
      @if($template->locale)
      <span style="font-size:.68rem;color:var(--c-muted);font-weight:600">{{ strtoupper($template->locale) }}</span>
      @endif
    </div>
  </div>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
    @if($template->hasDocxTemplate())
    <a href="{{ route('admin.contract-templates.docx.preview', $template) }}"
       class="btn-ghost btn-sm-pro" target="_blank">
      <i class="fas fa-file-word me-1"></i>Aperçu DOCX
    </a>
    <a href="{{ route('admin.contract-templates.docx.download', $template) }}"
       class="btn-ghost btn-sm-pro">
      <i class="fas fa-download me-1"></i>Télécharger
    </a>
    @endif
    <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Retour
    </a>
  </div>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif
<div class="row g-4">

  {{-- ── Colonne principale ── --}}
  <div class="col-xl-8">

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- FORMULAIRE PRINCIPAL (nom, langue, défaut, admins)             --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <form action="{{ route('admin.contract-templates.update', $template) }}"
          method="POST" id="tplForm">
    @csrf @method('PUT')

    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Informations générales</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-sm-8">
            <label class="form-label-pro">Nom du modèle *</label>
            <input type="text" name="name" class="form-control-pro"
                   value="{{ old('name', $template->name) }}" required>
          </div>
          <div class="col-sm-4">
            <label class="form-label-pro">Langue par défaut</label>
            <select name="locale" class="form-control-pro">
              <option value="">Automatique (langue du client)</option>
              @foreach(['fr'=>'Français','en'=>'English','pl'=>'Polski','es'=>'Español','bg'=>'Български','hu'=>'Magyar','it'=>'Italiano','de'=>'Deutsch','lt'=>'Lietuvių','ro'=>'Română','lv'=>'Latviešu','nl'=>'Nederlands','pt'=>'Português','hr'=>'Hrvatski'] as $code=>$label)
              <option value="{{ $code }}" {{ old('locale', $template->locale)==$code?'selected':'' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.85rem">
              <input type="checkbox" name="is_default" value="1"
                     {{ old('is_default', $template->is_default)?'checked':'' }}
                     style="width:16px;height:16px;accent-color:var(--c-navy)">
              <span>Modèle par défaut</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Admins assignés (super-admin) ── --}}
    @if(auth()->user()->hasRole('super-admin') && $admins->count())
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Admins assignés</div>
      </div>
      <div class="card-pro-body">
        <div style="font-size:.75rem;color:var(--c-muted);margin-bottom:.75rem">
          Laissez tous décochés = accessible à tous les admins.
        </div>
        <div class="row g-2">
          @foreach($admins as $admin)
          <div class="col-sm-6 col-md-4">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.83rem">
              <input type="checkbox" name="assigned_admins[]" value="{{ $admin->id }}"
                     {{ in_array($admin->id, $assignedIds) ? 'checked' : '' }}
                     style="width:15px;height:15px;accent-color:var(--c-navy)">
              {{ $admin->name }}
            </label>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-bottom:1.5rem">
      <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-navy">
        <i class="fas fa-save me-1"></i>Enregistrer
      </button>
    </div>
    </form>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION DOCX (formulaire séparé)                               --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="card-pro" id="docx-section">
      <div class="card-pro-hdr">
        <div>
          <div class="card-pro-title">
            <span class="icon-dot" style="background:#1D4ED8"></span>
            Template Word (.docx)
            @if($template->hasDocxTemplate())
            <span style="margin-left:.5rem;font-size:.68rem;padding:.15rem .5rem;
                         border-radius:10px;background:#DBEAFE;color:#1D4ED8;font-weight:700">
              v{{ $template->docx_version }} actif
            </span>
            @endif
          </div>
          <div style="font-size:.72rem;color:var(--c-muted);margin-top:.15rem">
            Génération DOCX via ZipArchive — polices, tableaux, filigrane et signatures préservés à 100 %
          </div>
        </div>
        @if($template->hasDocxTemplate())
        <div style="display:flex;gap:.5rem;margin-left:auto;align-items:center;flex-wrap:wrap">
          <a href="{{ route('admin.contract-templates.docx.download', $template) }}"
             class="btn-ghost btn-sm-pro">
            <i class="fas fa-download me-1"></i>Télécharger
          </a>
          <a href="{{ route('admin.contract-templates.docx.preview', $template) }}"
             class="btn-ghost btn-sm-pro" target="_blank">
            <i class="fas fa-file-word me-1"></i>Aperçu données démo
          </a>
        </div>
        @endif
      </div>

      <div class="card-pro-body">

        @if($template->hasDocxTemplate())
        <div style="padding:.75rem 1rem;background:#F0FDF4;border:1px solid #86EFAC;
                    border-radius:8px;margin-bottom:1.25rem">
          <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
            <i class="fas fa-check-circle" style="color:#16A34A"></i>
            <span style="font-size:.8125rem;font-weight:700;color:#166534">
              Template DOCX actif — {{ count($template->docx_detected_vars ?? []) }} variable(s) détectée(s)
            </span>
          </div>
          @if(count($template->docx_detected_vars ?? []) > 0)
          <div style="display:flex;flex-wrap:wrap;gap:.25rem">
            @foreach($template->docx_detected_vars as $var)
            <button type="button" onclick="copyVar('{{"{"}}{{ $var }}{{"}"}}' )"
                    style="font-size:.67rem;padding:.12rem .4rem;border-radius:4px;
                           border:1px solid #86EFAC;background:#fff;color:#166534;
                           cursor:pointer;font-family:monospace">{{"{"}}{{ $var }}{{"}"}}</button>
            @endforeach
          </div>
          <div style="font-size:.7rem;color:#166534;margin-top:.5rem">
            <i class="fas fa-info-circle me-1"></i>Cliquez sur une variable pour la copier.
          </div>
          @endif
        </div>
        @else
        <div style="padding:.875rem 1rem;background:#FEF9C3;border:1px solid #FDE047;
                    border-radius:8px;margin-bottom:1.25rem;font-size:.8125rem;color:#713F12">
          <i class="fas fa-exclamation-triangle me-1"></i>
          <strong>Aucun template DOCX uploadé.</strong>
          Uploadez un fichier <code>.docx</code> contenant des placeholders
          <code>{variable}</code> pour activer la génération de contrats Word.
        </div>
        @endif

        <form action="{{ route('admin.contract-templates.docx.upload', $template) }}"
              method="POST" enctype="multipart/form-data">
          @csrf

          <div style="display:flex;align-items:flex-end;gap:.75rem;flex-wrap:wrap">
            <div style="flex:1;min-width:220px">
              <label class="form-label-pro">
                <i class="fas fa-upload me-1" style="color:#1D4ED8"></i>
                @if($template->hasDocxTemplate()) Remplacer le template DOCX @else Uploader un template DOCX @endif
              </label>
              <input type="file" name="docx_file" accept=".docx"
                     class="form-control-pro" style="font-size:.82rem" required>
              @error('docx_file')
              <div style="font-size:.72rem;color:#dc2626;margin-top:.3rem">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
              </div>
              @enderror
            </div>
            <button type="submit" class="btn-navy" style="white-space:nowrap">
              <i class="fas fa-upload me-1"></i>
              @if($template->hasDocxTemplate()) Mettre à jour (v{{ $template->docx_version + 1 }}) @else Uploader @endif
            </button>
          </div>

          <div style="margin-top:.625rem;padding:.5rem .75rem;background:#F8FAFC;
                      border:1px solid var(--c-border);border-radius:6px;font-size:.72rem;
                      color:var(--c-muted);line-height:1.6">
            <i class="fas fa-lightbulb me-1" style="color:var(--c-accent)"></i>
            Fichier <code>.docx</code> max 20 Mo · Placeholders au format <code>{variable}</code> (accolades simples) ·
            En-têtes et pieds de page également analysés ·
            @if($template->hasDocxTemplate())
            Chaque upload <strong>incrémente la version</strong> et archive l'ancien fichier.
            @endif
          </div>
        </form>

      </div>
    </div>

  </div>

  {{-- ── Référence des variables DOCX (sidebar) ── --}}
  <div class="col-xl-4">
    <div class="card-pro" style="position:sticky;top:1.5rem">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Variables disponibles</div>
      </div>
      <div class="card-pro-body" style="max-height:72vh;overflow-y:auto">
        <div style="font-size:.75rem;color:var(--c-muted);margin-bottom:.75rem">
          Cliquez pour copier — utilisez ces balises dans votre fichier .docx.
        </div>

        @php
          $groups = [
            'Données dossier' => array_filter($variables, fn($k) => in_array($k, [
              '{reference}','{archive}','{nom_client}','{adresse_client}','{date_naissance}',
              '{type_identite}','{numero_identite}','{date_delivre}','{numero_fiscal}','{activite_exercee}','{agent_suivi}','{directeur}','{notaire}',
              '{objet}','{montant}','{montant_lettres}','{montant_totalavecinteret}','{devise}','{duree}','{mensualite}','{montant_mensualite}','{taux}',
              '{frais_admin}','{compte_bancaire}','{date}','{societe}',
            ]), ARRAY_FILTER_USE_KEY),
            'Accord de genre' => array_filter($variables, fn($k) => in_array($k, [
              '{ne_e}','{nee}','{denomme_e}','{zamieszkal_a}','{e}',
            ]), ARRAY_FILTER_USE_KEY),
          ];
        @endphp

        @foreach($groups as $groupName => $groupVars)
        <div style="margin-bottom:1rem">
          <div style="font-size:.7rem;font-weight:700;color:var(--c-navy);text-transform:uppercase;
                      letter-spacing:.6px;margin-bottom:.4rem;padding-bottom:.3rem;
                      border-bottom:1px solid var(--c-border)">{{ $groupName }}</div>
          @foreach($groupVars as $tag => $desc)
          <div style="display:flex;align-items:flex-start;gap:.5rem;margin-bottom:.3rem">
            <button type="button" onclick="copyVar('{{ $tag }}')"
                    style="font-size:.67rem;padding:.15rem .4rem;border-radius:4px;
                           border:1px solid #cbd5e1;background:#f8fafc;color:var(--c-navy);
                           cursor:pointer;white-space:nowrap;flex-shrink:0;
                           font-family:monospace">{{ $tag }}</button>
            <span style="font-size:.7rem;color:var(--c-muted);line-height:1.4;padding-top:.15rem">{{ $desc }}</span>
          </div>
          @endforeach
        </div>
        @endforeach

        @if($template->hasDocxTemplate())
        <div style="margin-top:.5rem;padding:.65rem .75rem;background:#EFF6FF;
                    border-radius:6px;border:1px solid #BFDBFE">
          <div style="font-size:.72rem;font-weight:700;color:#1D4ED8;margin-bottom:.25rem">
            <i class="fas fa-file-word me-1"></i>Variables DOCX détectées
          </div>
          <div style="font-size:.7rem;color:#1e40af;line-height:1.5;margin-bottom:.4rem">
            Présentes dans le template Word uploadé :
          </div>
          <div style="display:flex;flex-wrap:wrap;gap:.2rem">
            @forelse($template->docx_detected_vars ?? [] as $dv)
            <button type="button" onclick="copyVar('{{"{"}}{{ $dv }}{{"}"}}' )"
                    style="font-size:.65rem;padding:.1rem .35rem;border-radius:3px;
                           border:1px solid #BFDBFE;background:#fff;color:#1D4ED8;
                           cursor:pointer;font-family:monospace">{{"{"}}{{ $dv }}{{"}"}}</button>
            @empty
            <span style="font-size:.7rem;color:var(--c-muted);font-style:italic">Aucune</span>
            @endforelse
          </div>
        </div>
        @endif

      </div>
    </div>
  </div>

</div>

<div id="copyToast"
     style="position:fixed;bottom:1.5rem;right:1.5rem;background:#0E3B2E;color:#fff;
            padding:.5rem 1rem;border-radius:8px;font-size:.8rem;
            opacity:0;transition:opacity .3s;pointer-events:none;z-index:9999">
  Copié !
</div>

@push('scripts')
<script>
function copyVar(tag) {
  navigator.clipboard.writeText(tag).then(() => {
    const t = document.getElementById('copyToast');
    t.textContent = tag + ' copié';
    t.style.opacity = '1';
    setTimeout(() => t.style.opacity = '0', 1800);
  });
}
</script>
@endpush

@endsection
