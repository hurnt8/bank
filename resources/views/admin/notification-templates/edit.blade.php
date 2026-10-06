@extends('layouts.dashboard')
@section('title','Modifier le modèle de notification')
@section('page_title','Modifier le modèle de notification')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>{{ $template->name }}</h4>
    <div style="display:flex;align-items:center;gap:.5rem;margin-top:.35rem;flex-wrap:wrap">
      <span class="badge-status bs-amber" style="font-size:.65rem">{{ strtoupper($template->locale) }}</span>
      <span class="badge-status" style="font-size:.65rem;background:#EEF2FF;color:var(--c-navy);border:1px solid #C7D2FE">{{ $template->typeLabel() }}</span>
    </div>
  </div>
  <a href="{{ route('admin.notification-templates.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif
<div class="row g-4">

  {{-- ── Colonne principale ── --}}
  <div class="col-xl-8">
    <form action="{{ route('admin.notification-templates.update', $template) }}" method="POST" id="tplForm">
    @csrf @method('PUT')

    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Informations générales</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Nom du modèle *</label>
            <input type="text" name="name" class="form-control-pro"
                   value="{{ old('name', $template->name) }}" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Étape *</label>
            <select name="type" class="form-control-pro" required>
              @foreach($types as $code=>$label)
              <option value="{{ $code }}" {{ old('type', $template->type)==$code?'selected':'' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Langue *</label>
            <select name="locale" class="form-control-pro" required>
              @foreach(['fr'=>'Français','en'=>'English','pl'=>'Polski','es'=>'Español','bg'=>'Български','hu'=>'Magyar','it'=>'Italiano','de'=>'Deutsch','lt'=>'Lietuvių','ro'=>'Română','lv'=>'Latviešu','nl'=>'Nederlands','pt'=>'Português','hr'=>'Hrvatski'] as $code=>$label)
              <option value="{{ $code }}" {{ old('locale', $template->locale)==$code?'selected':'' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12" id="subjectField">
            <label class="form-label-pro">Sujet de l'email *</label>
            <input type="text" name="subject" id="subjectInput" class="form-control-pro"
                   value="{{ old('subject', $template->subject) }}" required>
          </div>
        </div>
      </div>
    </div>

    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span><span id="contentCardTitle">Contenu de l'email</span></div>
      </div>
      <div class="card-pro-body">
        <div style="font-size:.75rem;color:var(--c-muted);margin-bottom:.6rem">
          Mise en forme visuelle — pas besoin de connaître le HTML. Utilisez les balises <code>{tag}</code>
          listées à droite (clic pour insérer) — elles seront remplacées par les données du dossier au moment de l'envoi.
        </div>

        <div class="wys-toolbar">
          <button type="button" class="wys-btn" data-cmd="bold" title="Gras"><i class="fas fa-bold"></i></button>
          <button type="button" class="wys-btn" data-cmd="italic" title="Italique"><i class="fas fa-italic"></i></button>
          <button type="button" class="wys-btn" data-cmd="underline" title="Souligné"><i class="fas fa-underline"></i></button>
          <span class="wys-sep"></span>
          <button type="button" class="wys-btn" data-cmd="insertUnorderedList" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
          <button type="button" class="wys-btn" data-cmd="insertOrderedList" title="Liste numérotée"><i class="fas fa-list-ol"></i></button>
          <span class="wys-sep"></span>
          <button type="button" class="wys-btn" id="wysLinkBtn" title="Insérer un lien"><i class="fas fa-link"></i></button>
          <button type="button" class="wys-btn" data-cmd="removeFormat" title="Effacer la mise en forme"><i class="fas fa-eraser"></i></button>
        </div>
        <div id="contentEditor" class="wys-editor" contenteditable="true">{!! old('content', $template->content) !!}</div>
        <textarea name="content" id="contentHidden" style="display:none"></textarea>
      </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-bottom:1.5rem">
      <a href="{{ route('admin.notification-templates.index') }}" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-navy">
        <i class="fas fa-save me-1"></i>Enregistrer
      </button>
    </div>
    </form>

    @if(in_array($template->type, [\App\Models\NotificationTemplate::TYPE_VALIDATION, \App\Models\NotificationTemplate::TYPE_CONDITIONS, \App\Models\NotificationTemplate::TYPE_INSURANCE]))
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION DOCX — document attaché au dossier                     --}}
    {{-- "Notification de validation" : ce document débloque « Valider ».--}}
    {{-- "Conditions générales" : disponible pour référence/conversion  --}}
    {{-- manuelle — l'envoi automatique au contrat utilise le champ     --}}
    {{-- « Contenu » (HTML) ci-dessus, pas ce DOCX.                     --}}
    {{-- "Assurance emprunteur" : le DOCX généré par dossier est        --}}
    {{-- converti manuellement en PDF puis uploadé sur la fiche dossier.--}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @php
      $isValidationType = $template->type === \App\Models\NotificationTemplate::TYPE_VALIDATION;
      $isInsuranceType  = $template->type === \App\Models\NotificationTemplate::TYPE_INSURANCE;
      $docxHeaderHelp = match(true) {
          $isValidationType => 'Document généré par dossier (comme les contrats), à convertir en PDF puis uploader — requis pour débloquer « Valider ».',
          $isInsuranceType  => 'Document généré par dossier, à convertir en PDF puis uploader sur la fiche du dossier (comme l\'attestation d\'assurance).',
          default           => 'Document de référence, téléchargeable pour consultation ou conversion manuelle. L\'envoi automatique joint au contrat utilise le champ « Contenu » ci-dessus (converti en PDF automatiquement) — pas ce DOCX.',
      };
      $docxEmptyHelp = match(true) {
          $isValidationType => 'Uploadez un fichier <code>.docx</code> contenant des placeholders <code>{variable}</code> — indispensable pour que « Valider » soit disponible sur les dossiers de cette langue.',
          $isInsuranceType  => 'Uploadez un fichier <code>.docx</code> contenant des placeholders <code>{variable}</code> — nécessaire pour générer l\'attestation d\'assurance personnalisée par dossier.',
          default           => 'Uploadez un fichier <code>.docx</code> contenant des placeholders <code>{variable}</code> si vous préférez rédiger les conditions dans Word plutôt que dans l\'éditeur ci-dessus (téléchargement manuel uniquement, non joint automatiquement).',
      };
    @endphp
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
            {!! $docxHeaderHelp !!}
          </div>
        </div>
        @if($template->hasDocxTemplate())
        <div style="display:flex;gap:.5rem;margin-left:auto;align-items:center;flex-wrap:wrap">
          <a href="{{ route('admin.notification-templates.docx.download', $template) }}"
             class="btn-ghost btn-sm-pro">
            <i class="fas fa-download me-1"></i>Télécharger
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
          @endif
        </div>
        @else
        <div style="padding:.875rem 1rem;background:#FEF9C3;border:1px solid #FDE047;
                    border-radius:8px;margin-bottom:1.25rem;font-size:.8125rem;color:#713F12">
          <i class="fas fa-exclamation-triangle me-1"></i>
          <strong>Aucun template DOCX uploadé.</strong>
          {!! $docxEmptyHelp !!}
        </div>
        @endif

        <form action="{{ route('admin.notification-templates.docx.upload', $template) }}"
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
        </form>

      </div>
    </div>
    @else
    <div class="card-pro" style="padding:1rem 1.25rem;font-size:.8rem;color:var(--c-muted);
                display:flex;align-items:center;gap:.6rem">
      <i class="fas fa-info-circle" style="color:var(--c-navy)"></i>
      <span>
        Aucun document à générer pour cette étape : le PDF du contrat et le tableau d'amortissement
        sont déjà joints automatiquement à l'envoi. Seuls le sujet et le contenu ci-dessus sont utilisés.
      </span>
    </div>
    @endif
  </div>

  {{-- ── Référence des variables (sidebar) ── --}}
  <div class="col-xl-4">
    <div class="card-pro" style="position:sticky;top:1.5rem">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Variables disponibles</div>
      </div>
      <div class="card-pro-body" style="max-height:72vh;overflow-y:auto">
        <div style="font-size:.75rem;color:var(--c-muted);margin-bottom:.75rem">
          Cliquez pour insérer la balise dans le sujet ou le contenu (à l'endroit où vous avez cliqué en dernier).
        </div>
        @foreach($variables as $tag => $desc)
        <div style="display:flex;align-items:flex-start;gap:.5rem;margin-bottom:.3rem">
          <button type="button" onclick="insertVar('{{ $tag }}')"
                  style="font-size:.67rem;padding:.15rem .4rem;border-radius:4px;
                         border:1px solid #cbd5e1;background:#f8fafc;color:var(--c-navy);
                         cursor:pointer;white-space:nowrap;flex-shrink:0;
                         font-family:monospace">{{ $tag }}</button>
          <span style="font-size:.7rem;color:var(--c-muted);line-height:1.4;padding-top:.15rem">{{ $desc }}</span>
        </div>
        @endforeach
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

@push('styles')
<style>
.wys-toolbar {
  display: flex; align-items: center; gap: .25rem; flex-wrap: wrap;
  padding: .4rem .5rem; margin-bottom: .5rem;
  background: #f8fafc; border: 1px solid var(--c-border); border-radius: 8px 8px 0 0;
}
.wys-btn {
  width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid transparent; border-radius: 6px; background: transparent; color: var(--c-navy);
  cursor: pointer; font-size: .8rem;
}
.wys-btn:hover { background: #e5e7eb; }
.wys-btn.active { background: var(--c-navy); color: #fff; }
.wys-sep { width: 1px; height: 20px; background: var(--c-border); margin: 0 .25rem; }
.wys-editor {
  min-height: 260px; max-height: 480px; overflow-y: auto;
  border: 1px solid var(--c-border); border-top: none; border-radius: 0 0 8px 8px;
  padding: .75rem .9rem; font-size: .875rem; line-height: 1.6; color: #111827;
  background: #fff;
}
.wys-editor:focus { outline: 2px solid var(--c-navy); outline-offset: -1px; }
.wys-editor ul, .wys-editor ol { padding-left: 1.4rem; }
</style>
@endpush

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

// ── Sujet masqué + titre du contenu adapté pour "Conditions générales" ──────
(function () {
  var typeSelect     = document.querySelector('select[name="type"]');
  var subjectField   = document.getElementById('subjectField');
  var subjectInput   = document.getElementById('subjectInput');
  var contentTitle   = document.getElementById('contentCardTitle');
  var NO_SUBJECT_TYPES = ['{{ \App\Models\NotificationTemplate::TYPE_CONDITIONS }}'];
  if (!typeSelect || !subjectField) return;

  function sync() {
    var noSubject = NO_SUBJECT_TYPES.indexOf(typeSelect.value) !== -1;
    subjectField.style.display = noSubject ? 'none' : '';
    subjectInput.required = !noSubject;
    if (contentTitle) contentTitle.textContent = noSubject ? 'Contenu' : 'Contenu de l\'email';
  }

  typeSelect.addEventListener('change', sync);
  sync();
})();

// ── Éditeur WYSIWYG du contenu de notification ──────────────────────────────
(function () {
  const editor       = document.getElementById('contentEditor');
  const subjectInput = document.getElementById('subjectInput');
  const hiddenField  = document.getElementById('contentHidden');
  const form         = document.getElementById('tplForm');
  if (!editor) return;

  let lastTarget = editor;
  editor.addEventListener('focus', () => lastTarget = editor);
  if (subjectInput) subjectInput.addEventListener('focus', () => lastTarget = subjectInput);

  document.querySelectorAll('.wys-btn[data-cmd]').forEach(btn => {
    btn.addEventListener('click', () => {
      editor.focus();
      document.execCommand(btn.dataset.cmd, false, null);
    });
  });

  const linkBtn = document.getElementById('wysLinkBtn');
  if (linkBtn) {
    linkBtn.addEventListener('click', () => {
      const url = prompt('Adresse du lien (https://...) :');
      if (url) {
        editor.focus();
        document.execCommand('createLink', false, url);
      }
    });
  }

  window.insertVar = function (tag) {
    if (lastTarget === subjectInput && subjectInput) {
      const start = subjectInput.selectionStart ?? subjectInput.value.length;
      const end   = subjectInput.selectionEnd ?? subjectInput.value.length;
      subjectInput.value = subjectInput.value.slice(0, start) + tag + subjectInput.value.slice(end);
      subjectInput.focus();
      subjectInput.selectionStart = subjectInput.selectionEnd = start + tag.length;
    } else {
      editor.focus();
      document.execCommand('insertText', false, tag);
    }
  };

  if (form) {
    form.addEventListener('submit', () => {
      hiddenField.value = editor.innerHTML;
    });
  }
})();
</script>
@endpush

@endsection
