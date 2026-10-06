<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Assurance {{ $loan->reference }} · {{ site_name() }}</title>
<link rel="icon" type="image/png" sizes="32x32" href="/site-icon-32.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
<style>
:root {
  --navy:    #0E3B2E;
  --navy2:   #14503D;
  --accent:    #DCBE87;
  --accent-d:   #2B94F7;
  --green:   #059669;
  --greend:  #047857;
  --red:     #DC2626;
  --text:    #E2E8F0;
  --sub:     #94A3B8;
  --muted:   rgba(255,255,255,.36);
  --line:    rgba(255,255,255,.07);
  --sw:      272px;
  --r:       7px;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Outfit', sans-serif; overflow: hidden; background: #525659; }

/* ════════════════ LAYOUT ════════════════ */
.pv { display: flex; height: 100vh; }

/* ════════════════ SIDEBAR ════════════════ */
.pv-side {
  width: var(--sw); flex-shrink: 0;
  background: var(--navy);
  border-right: 1px solid var(--line);
  display: flex; flex-direction: column;
  overflow-y: auto; overflow-x: hidden;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,.08) transparent;
}

/* — Brand — */
.s-brand {
  display: flex; align-items: center; gap: .7rem;
  padding: .9rem 1.125rem;
  border-bottom: 1px solid var(--line);
  text-decoration: none; flex-shrink: 0;
}
.s-logo {
  width: 30px; height: 30px; border-radius: 6px;
  background: var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-size: .62rem; font-weight: 900; color: var(--navy);
  letter-spacing: .01em; flex-shrink: 0;
}
.s-brand-wrap { display: flex; flex-direction: column; }
.s-brand-name { font-size: .74rem; font-weight: 800; color: var(--text); text-transform: uppercase; letter-spacing: .08em; line-height: 1.3; }
.s-brand-hint  { font-size: .57rem; color: var(--muted); margin-top: .1rem; }

/* — Document type (accent vert) — */
.s-doc {
  padding: .9rem 1.125rem;
  border-bottom: 1px solid var(--line);
  position: relative; flex-shrink: 0;
}
.s-doc::before {
  content: '';
  position: absolute; left: 0; top: 0; bottom: 0;
  width: 3px; border-radius: 0 2px 2px 0;
  background: var(--green);
}
.s-doc-badge {
  display: inline-flex; align-items: center; gap: .4rem;
  background: rgba(5,150,105,.12);
  color: #6EE7B7;
  border: 1px solid rgba(5,150,105,.25);
  padding: .26rem .65rem; border-radius: 999px;
  font-size: .64rem; font-weight: 700;
  margin-bottom: .6rem;
}
.s-doc-title { font-size: .9rem; font-weight: 700; color: var(--text); margin-bottom: .18rem; }
.s-doc-sub   { font-size: .64rem; color: var(--muted); }

/* — Identity — */
.s-identity {
  padding: .8rem 1.125rem;
  border-bottom: 1px solid var(--line); flex-shrink: 0;
}
.s-ref {
  font-family: 'Courier New', monospace;
  font-size: .98rem; font-weight: 800; color: var(--accent);
  margin-bottom: .35rem; letter-spacing: .02em;
}
.s-client-name  { font-size: .8rem; font-weight: 600; color: var(--text); margin-bottom: .14rem; }
.s-client-email { font-size: .67rem; color: var(--muted); }

/* — Meta grid — */
.s-meta {
  padding: .8rem 1.125rem;
  border-bottom: 1px solid var(--line);
  display: grid; grid-template-columns: 1fr 1fr; gap: .65rem;
  flex-shrink: 0;
}
.s-meta-lbl {
  font-size: .55rem; font-weight: 700; color: var(--muted);
  text-transform: uppercase; letter-spacing: .07em; margin-bottom: .18rem;
}
.s-meta-val { font-size: .78rem; font-weight: 700; color: var(--text); }
.s-meta-val.accent  { color: var(--accent); }
.s-meta-val.green { color: #6EE7B7; }
.s-meta-val.full  { grid-column: 1 / -1; }

/* — Section header — */
.s-sec {
  padding: .6rem 1.125rem .2rem;
  font-size: .55rem; font-weight: 700; color: var(--muted);
  text-transform: uppercase; letter-spacing: .1em; flex-shrink: 0;
}

/* — Actions — */
.s-actions {
  padding: .2rem .75rem .75rem;
  display: flex; flex-direction: column; gap: .32rem;
  flex-shrink: 0;
}
.s-btn {
  display: flex; align-items: center; gap: .5rem;
  width: 100%;
  padding: .58rem .8rem;
  border-radius: var(--r);
  font-size: .77rem; font-weight: 600;
  cursor: pointer; text-decoration: none;
  border: 1px solid; font-family: 'Outfit', sans-serif;
  transition: background .14s, border-color .14s, color .14s;
  white-space: nowrap; line-height: 1;
  background: none;
}
.s-btn i { width: 14px; text-align: center; flex-shrink: 0; font-size: .8rem; }

.s-btn-ghost {
  background: rgba(255,255,255,.05);
  color: var(--text);
  border-color: rgba(255,255,255,.1);
}
.s-btn-ghost:hover { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.2); color: #fff; }

.s-btn-primary {
  background: var(--green); color: #fff;
  border-color: var(--greend); font-weight: 700;
}
.s-btn-primary:hover { background: var(--greend); }

.s-btn-red {
  background: rgba(220,38,38,.13); color: #FCA5A5;
  border-color: rgba(220,38,38,.25);
}
.s-btn-red:hover { background: rgba(220,38,38,.23); color: #FECACA; }

/* — Nav links — */
.s-nav {
  padding: .15rem .5rem .5rem;
  display: flex; flex-direction: column; gap: .12rem; flex-shrink: 0;
}
.s-nav-item {
  display: flex; align-items: center; gap: .5rem;
  padding: .45rem .65rem;
  border-radius: 6px;
  text-decoration: none;
  font-size: .76rem; font-weight: 500; color: var(--sub);
  transition: background .12s, color .12s;
}
.s-nav-item:hover { background: rgba(255,255,255,.06); color: var(--text); }
.s-nav-item i { width: 14px; text-align: center; font-size: .78rem; flex-shrink: 0; }

.s-line { height: 1px; background: var(--line); margin: .4rem 1.125rem; flex-shrink: 0; }

/* — Back (pinned bottom) — */
.s-back-wrap {
  margin-top: auto;
  padding: .75rem 1.125rem;
  border-top: 1px solid var(--line); flex-shrink: 0;
}
.s-back {
  display: flex; align-items: center; gap: .5rem;
  text-decoration: none;
  font-size: .74rem; font-weight: 500; color: var(--muted);
  transition: color .14s;
}
.s-back:hover { color: var(--text); }
.s-back i { width: 14px; text-align: center; }

/* ════════════════ PDF AREA ════════════════ */
.pv-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

.pv-bar {
  height: 34px;
  background: #3A3D40;
  border-bottom: 1px solid #2B2D30;
  display: flex; align-items: center;
  padding: 0 1rem; gap: .5rem;
  flex-shrink: 0;
}
.pv-crumb {
  display: flex; align-items: center; gap: .35rem;
  font-size: .67rem; color: rgba(255,255,255,.45);
}
.pv-crumb a { color: rgba(255,255,255,.45); text-decoration: none; }
.pv-crumb a:hover { color: rgba(255,255,255,.82); }
.pv-crumb-sep { font-size: .6rem; color: rgba(255,255,255,.25); }
.pv-crumb-cur { color: rgba(255,255,255,.82); font-weight: 600; }
.pv-bar-hint { margin-left: auto; font-size: .6rem; color: rgba(255,255,255,.28); }
kbd {
  font-family: 'Courier New', monospace; font-size: .56rem;
  background: rgba(255,255,255,.1);
  border: 1px solid rgba(255,255,255,.14);
  padding: .05rem .28rem; border-radius: 3px;
  color: rgba(255,255,255,.4);
}

.pv-frame-wrap { flex: 1; position: relative; }
.pv-frame { position: absolute; inset: 0; width: 100%; height: 100%; border: none; display: block; }

/* Empty state */
.pv-empty {
  flex: 1;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 1rem;
  background: #F3F4F6;
}
.pv-empty-ico {
  width: 68px; height: 68px; border-radius: 14px;
  background: rgba(5,150,105,.08);
  display: flex; align-items: center; justify-content: center;
}
.pv-empty-ico i { font-size: 1.75rem; color: #059669; opacity: .4; }
.pv-empty h3 { font-size: .95rem; font-weight: 700; color: #0F172A; }
.pv-empty p  { font-size: .78rem; color: #6B7280; }
.pv-empty-cta {
  display: inline-flex; align-items: center; gap: .4rem;
  padding: .55rem 1.1rem;
  background: var(--navy); color: #fff;
  border: none; border-radius: var(--r);
  font-size: .78rem; font-weight: 600;
  text-decoration: none; cursor: pointer;
  font-family: 'Outfit', sans-serif; margin-top: .25rem;
}
.pv-empty-cta:hover { background: var(--navy2); }

/* ════════════════ MOBILE ════════════════ */
.pv-mob-bar {
  display: none; height: 50px;
  background: var(--navy);
  border-bottom: 1px solid var(--line);
  align-items: center;
  padding: 0 1rem; gap: .75rem; flex-shrink: 0;
}
.pv-mob-toggle {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color: var(--text); padding: .35rem .55rem;
  border-radius: 6px; font-size: .8rem; cursor: pointer;
}
.pv-mob-ref  { font-family: 'Courier New', monospace; font-size: .82rem; font-weight: 700; color: var(--accent); }
.pv-mob-name { font-size: .74rem; color: var(--sub); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.pv-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(0,0,0,.52); z-index: 50;
}
.pv-overlay.open { display: block; }

@media (max-width: 768px) {
  .pv-mob-bar { display: flex; }
  .pv-bar     { display: none; }
  .pv-side {
    position: fixed; top: 0; left: calc(-1 * var(--sw)); bottom: 0;
    z-index: 100; transition: left .24s cubic-bezier(.4,0,.2,1);
  }
  .pv-side.open { left: 0; }
}

/* ── Modal de confirmation ── */
.cf-modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;padding:1rem}
.cf-modal-overlay.open{display:flex}
.cf-modal{background:var(--navy2);border:1px solid var(--line);border-radius:14px;max-width:400px;width:100%;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,.4)}
.cf-modal-icon{width:40px;height:40px;border-radius:10px;background:rgba(220,190,135,.15);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.05rem;margin-bottom:.75rem}
.cf-modal-title{font-size:.95rem;font-weight:800;color:var(--text);margin-bottom:.5rem}
.cf-modal-msg{font-size:.8rem;color:var(--sub);line-height:1.6;margin-bottom:1.25rem;white-space:pre-line}
.cf-modal-actions{display:flex;gap:.5rem;justify-content:flex-end}
</style>
</head>
<body>

@php $panelPrefix = request()->routeIs('super-admin.*') ? 'super-admin' : 'admin'; @endphp

<div class="pv-overlay" id="ovl" onclick="sideClose()"></div>

<div class="pv">

  {{-- ════ SIDEBAR ════ --}}
  <aside class="pv-side" id="side">

    {{-- Brand --}}
    <a href="{{ route($panelPrefix.'.loans.show', $loan) }}" class="s-brand">
      <div class="s-logo">CI</div>
      <div class="s-brand-wrap">
        <span class="s-brand-name">{{ site_name() }}</span>
        <span class="s-brand-hint">Portail de gestion</span>
      </div>
    </a>

    {{-- Type de document --}}
    <div class="s-doc">
      <div class="s-doc-badge"><i class="fas fa-shield-alt"></i>&nbsp;Attestation d'assurance</div>
      <div class="s-doc-title">Aperçu du document</div>
      <div class="s-doc-sub">PDF · Lecture seule</div>
    </div>

    {{-- Référence + client --}}
    <div class="s-identity">
      <div class="s-ref">{{ $loan->reference }}</div>
      <div class="s-client-name">{{ $loan->name }}</div>
      @if($loan->email)
      <div class="s-client-email">{{ $loan->email }}</div>
      @endif
    </div>

    {{-- Métadonnées --}}
    <div class="s-meta">
      <div>
        <div class="s-meta-lbl">Capital assuré</div>
        <div class="s-meta-val accent">{{ number_format($loan->amount, 0, ',', ' ') }} {{ $loan->currency }}</div>
      </div>
      <div>
        <div class="s-meta-lbl">Durée</div>
        <div class="s-meta-val">{{ $loan->darly }} mois</div>
      </div>
      @if($loan->frais_assurance)
      <div>
        <div class="s-meta-lbl">Frais assurance</div>
        <div class="s-meta-val green">{{ number_format($loan->frais_assurance, 2, ',', ' ') }} {{ $loan->currency }}</div>
      </div>
      @endif
      @if($loan->date_fin_assurance)
      <div>
        <div class="s-meta-lbl">Date de fin</div>
        <div class="s-meta-val">{{ $loan->date_fin_assurance->format('d/m/Y') }}</div>
      </div>
      @endif
      @if($insuranceTemplate)
      <div style="grid-column:1/-1">
        <div class="s-meta-lbl">Modèle d'assurance</div>
        <div class="s-meta-val" style="font-size:.72rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $insuranceTemplate->name }}</div>
      </div>
      @endif
    </div>

    {{-- Actions --}}
    <div class="s-sec">Actions</div>
    <div class="s-actions">
      <form action="{{ route($panelPrefix.'.loans.insurance.send', $loan) }}" method="POST"
            data-confirm="Envoyer l'attestation par email à {{ $loan->email ?? '' }} ?">
        @csrf
        <button type="submit" class="s-btn s-btn-primary">
          <i class="fas fa-paper-plane"></i> Envoyer par email
        </button>
      </form>
      <a href="{{ route($panelPrefix.'.loans.insurance.pdf', $loan) }}"
         download="Assurance_{{ $loan->reference }}.pdf"
         class="s-btn s-btn-ghost">
        <i class="fas fa-download"></i> Télécharger le PDF
      </a>
      <button onclick="window.print()" class="s-btn s-btn-ghost" type="button">
        <i class="fas fa-print"></i> Imprimer
      </button>
    </div>

    <div class="s-line"></div>

    {{-- Navigation --}}
    <div class="s-sec">Navigation</div>
    <div class="s-nav">
      @if($loan->contract_pdf_path)
      <a href="{{ route($panelPrefix.'.loans.contract.viewer', $loan) }}" class="s-nav-item">
        <i class="fas fa-file-contract" style="color:#F87171"></i>
        Contrat de prêt
      </a>
      @endif
      <a href="{{ route($panelPrefix.'.loans.contract', $loan) }}" class="s-nav-item">
        <i class="fas fa-cog" style="color:var(--sub)"></i>
        Gérer le dossier
      </a>
      <a href="{{ route($panelPrefix.'.loans.show', $loan) }}" class="s-nav-item">
        <i class="fas fa-folder-open" style="color:var(--sub)"></i>
        Fiche dossier
      </a>
    </div>

    {{-- Retour (bas de sidebar) --}}
    <div class="s-back-wrap">
      <a href="{{ route($panelPrefix.'.loans.show', $loan) }}" class="s-back">
        <i class="fas fa-arrow-left"></i> Retour au dossier
      </a>
    </div>

  </aside>

  {{-- ════ ZONE PDF ════ --}}
  <main class="pv-main">

    {{-- Barre mobile --}}
    <div class="pv-mob-bar">
      <button class="pv-mob-toggle" onclick="sideOpen()" type="button" aria-label="Menu">
        <i class="fas fa-bars"></i>
      </button>
      <span class="pv-mob-ref">{{ $loan->reference }}</span>
      <span class="pv-mob-name">{{ $loan->name }}</span>
    </div>

    {{-- Fil d'Ariane (desktop) --}}
    <div class="pv-bar">
      <nav class="pv-crumb" aria-label="Fil d'Ariane">
        <a href="{{ route($panelPrefix.'.loans.index') }}">Dossiers</a>
        <span class="pv-crumb-sep">/</span>
        <a href="{{ route($panelPrefix.'.loans.show', $loan) }}">{{ $loan->reference }}</a>
        <span class="pv-crumb-sep">/</span>
        <span class="pv-crumb-cur">Attestation d'assurance</span>
      </nav>
      <div class="pv-bar-hint"><kbd>ESC</kbd> pour fermer</div>
    </div>

    {{-- PDF ou placeholder --}}
    @if($loan->insurance_pdf_path)
    <div class="pv-frame-wrap">
      <iframe
        class="pv-frame"
        src="{{ route($panelPrefix.'.loans.insurance.pdf', $loan) }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH"
        title="Attestation d'assurance — {{ $loan->reference }}"
        loading="eager"
      ></iframe>
    </div>
    @else
    <div class="pv-empty">
      <div class="pv-empty-ico"><i class="fas fa-shield-alt"></i></div>
      <h3>Aucune attestation disponible</h3>
      <p>Générez ou uploadez l'attestation depuis le dossier.</p>
      <a href="{{ route($panelPrefix.'.loans.show', $loan) }}" class="pv-empty-cta">
        <i class="fas fa-arrow-left"></i> Retour au dossier
      </a>
    </div>
    @endif

  </main>
</div>

{{-- ── Modal de confirmation ── --}}
<div class="cf-modal-overlay" id="cfModalOverlay">
  <div class="cf-modal">
    <div class="cf-modal-icon"><i class="fas fa-question-circle"></i></div>
    <div class="cf-modal-title" id="cfModalTitle">Confirmation</div>
    <div class="cf-modal-msg" id="cfModalMsg"></div>
    <div class="cf-modal-actions">
      <button type="button" class="s-btn s-btn-ghost" id="cfModalCancel" style="width:auto">Annuler</button>
      <button type="button" class="s-btn s-btn-primary" id="cfModalConfirm" style="width:auto">Confirmer</button>
    </div>
  </div>
</div>

<script>
function sideOpen()  { document.getElementById('side').classList.add('open'); document.getElementById('ovl').classList.add('open'); }
function sideClose() { document.getElementById('side').classList.remove('open'); document.getElementById('ovl').classList.remove('open'); }

(function () {
  var overlay = document.getElementById('cfModalOverlay');
  var titleEl = document.getElementById('cfModalTitle');
  var msgEl   = document.getElementById('cfModalMsg');
  var btnOk   = document.getElementById('cfModalConfirm');
  var btnCancel = document.getElementById('cfModalCancel');
  var pendingResolve = null;

  function close(result) {
    overlay.classList.remove('open');
    if (pendingResolve) { var r = pendingResolve; pendingResolve = null; r(result); }
  }

  window.confirmModal = function (message, opts) {
    opts = opts || {};
    titleEl.textContent = opts.title || 'Confirmation';
    msgEl.textContent = message;
    btnOk.textContent = opts.confirmLabel || 'Confirmer';
    overlay.classList.add('open');
    return new Promise(function (resolve) { pendingResolve = resolve; });
  };

  btnOk.addEventListener('click', function () { close(true); });
  btnCancel.addEventListener('click', function () { close(false); });
  overlay.addEventListener('click', function (e) { if (e.target === overlay) close(false); });

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form && form.dataset && form.dataset.confirm) {
      e.preventDefault();
      confirmModal(form.dataset.confirm, { title: form.dataset.confirmTitle }).then(function (ok) {
        if (ok) form.submit();
      });
    }
  }, true);

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (overlay.classList.contains('open')) { close(false); return; }
    window.location.href = '{{ route($panelPrefix.'.loans.show', $loan) }}';
  });
})();
</script>
</body>
</html>
