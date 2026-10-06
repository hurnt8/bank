@extends('layouts.dashboard')
@section('title', 'Contrat — ' . $loan->reference)
@section('page_title', 'Contrat ' . $loan->reference)

@push('styles')
<style>
/* ─────────────────────────────────────────────
   LOAN CONTRACT — préfixe lc- (pas de conflit)
   ───────────────────────────────────────────── */

/* ── Header ── */
.lc-header{background:#fff;border:1px solid var(--c-border);border-radius:14px;padding:1.125rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap}
.lc-header-left{display:flex;align-items:center;gap:.875rem;flex:1;min-width:0}
.lc-header-icon{width:44px;height:44px;border-radius:11px;background:linear-gradient(135deg,#FEF3C7,#FDE68A);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--c-accent-d,#9A7736);flex-shrink:0}
.lc-header-ref{font-family:monospace;font-size:1rem;font-weight:900;color:var(--c-navy)}
.lc-header-sub{font-size:.76rem;color:var(--c-muted);margin-top:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lc-header-actions{display:flex;gap:.5rem;flex-wrap:wrap;flex-shrink:0}
.lc-lang-badge{display:inline-flex;align-items:center;gap:.3rem;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:999px;padding:.2rem .6rem;font-size:.68rem;font-weight:700;color:var(--c-blue,#2563eb)}

/* ── Layout ── */
.lc-layout{display:grid;grid-template-columns:300px 1fr;gap:1.25rem;align-items:start}
@media(max-width:1100px){.lc-layout{grid-template-columns:1fr}}

/* ── Panel gauche (sticky) ── */
.lc-panel{display:flex;flex-direction:column;gap:1rem;position:sticky;top:calc(var(--topbar-h,64px) + 1rem)}
@media(max-width:1100px){.lc-panel{position:static}}

/* ── Panel card ── */
.lc-pcard{background:#fff;border:1px solid var(--c-border);border-radius:12px;overflow:hidden}
.lc-pcard-hdr{padding:.7rem 1rem;border-bottom:1px solid var(--c-border);background:#fafbfc;display:flex;align-items:center;gap:.5rem}
.lc-pcard-ico{width:22px;height:22px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.63rem;flex-shrink:0}
.lc-pcard-title{font-size:.72rem;font-weight:800;color:var(--c-navy);text-transform:uppercase;letter-spacing:.06em}
.lc-pcard-body{padding:.875rem 1rem;display:flex;flex-direction:column;gap:.5rem}

/* ── Summary rows ── */
.lc-sum-row{display:flex;align-items:center;justify-content:space-between;padding:.45rem .5rem;border-radius:7px;transition:.15s}
.lc-sum-row:hover{background:#f8f9fa}
.lc-sum-lbl{font-size:.72rem;color:var(--c-muted);font-weight:500}
.lc-sum-val{font-size:.8rem;font-weight:700;color:var(--c-navy);text-align:right}

/* ── Status action block ── */
.lc-action-block{border-radius:10px;padding:.875rem;margin-bottom:.25rem}
.lc-action-title{font-size:.8rem;font-weight:700;color:var(--c-navy);margin-bottom:.3rem}
.lc-action-sub{font-size:.72rem;color:var(--c-muted);margin-bottom:.75rem;line-height:1.5}
.lc-action-sent{display:flex;align-items:center;gap:.5rem;background:#ECFDF5;border:1px solid #A7F3D0;border-radius:8px;padding:.625rem .75rem}
.lc-action-signed{display:flex;align-items:center;gap:.5rem;background:#F5F3FF;border:1px solid #DDD6FE;border-radius:8px;padding:.625rem .75rem}
.lc-action-final{display:flex;align-items:center;gap:.5rem;background:#ECFDF5;border:1px solid #A7F3D0;border-radius:8px;padding:.625rem .75rem}

/* ── Upload zone ── */
.lc-upload-zone{border:1.5px dashed var(--c-border);border-radius:8px;padding:.875rem;text-align:center;cursor:pointer;background:#fafbfc;transition:.15s}
.lc-upload-zone:hover{border-color:var(--c-accent);background:#fffdf5}
.lc-pdf-file{display:flex;align-items:center;gap:.625rem;padding:.5rem .75rem;background:#FFF1F2;border:1px solid #FECDD3;border-radius:8px}

/* ── PDF viewer ── */
.lc-pdf-card{background:#fff;border:1px solid var(--c-border);border-radius:14px;overflow:hidden}
.lc-pdf-toolbar{display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;border-bottom:1px solid var(--c-border);gap:.75rem;flex-wrap:wrap;background:#fafbfc}
.lc-pdf-title{font-size:.82rem;font-weight:700;color:var(--c-navy);display:flex;align-items:center;gap:.5rem}
.lc-pdf-actions{display:flex;gap:.5rem;flex-wrap:wrap}
.lc-pdf-frame{width:100%;height:75vh;border:none;display:block;background:#525659}
.lc-pdf-placeholder{height:75vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f8f9fa;color:var(--c-muted)}

/* ── Variables card ── */
.lc-editor-card{background:#fff;border:1px solid var(--c-border);border-radius:14px;overflow:hidden;margin-top:1.125rem}

/* ── Variable chip ── */
.lc-var-chip{display:flex;align-items:center;gap:.625rem;padding:.55rem .75rem;background:#f8f9fa;border:1.5px solid var(--c-border);border-radius:8px;transition:.15s;cursor:default}
.lc-var-chip:hover{border-color:var(--c-accent);background:#fffdf5}
.lc-var-chip-ico{width:28px;height:28px;border-radius:7px;background:#EEF2FF;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.65rem;color:var(--c-navy)}
.lc-var-code{font-size:.7rem;color:var(--c-accent-d,#b45309);font-weight:700;font-family:'Courier New',monospace;display:block;line-height:1.2}
.lc-var-desc{font-size:.64rem;color:var(--c-muted);line-height:1.3;margin-top:.1rem}
.lc-copy-btn{width:26px;height:26px;border:none;background:none;color:var(--c-muted);cursor:pointer;border-radius:6px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:.15s}
.lc-copy-btn:hover{background:var(--c-border);color:var(--c-navy)}

/* ── Vars grid ── */
.lc-vars-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:.5rem}

/* ── Sélecteur de document (tabs) ── */
.lc-doctabs{display:flex;gap:.35rem;flex-wrap:wrap}
.lc-doctab-btn{display:flex;align-items:center;gap:.45rem;padding:.6rem 1.1rem;font-size:.8rem;font-weight:700;color:var(--c-muted);background:#f8f9fa;border:1px solid var(--c-border);border-bottom:none;border-radius:10px 10px 0 0;cursor:pointer;font-family:inherit;transition:.15s}
.lc-doctab-btn:hover{background:#fff;color:var(--c-navy)}
.lc-doctab-badge{font-size:.6rem;padding:.05rem .4rem;border-radius:8px;background:#e5e7eb;color:#6b7280;font-weight:800}
.lc-doctab-btn.active .lc-doctab-badge{background:rgba(255,255,255,.25);color:#fff}
.lc-doctab-btn--notification.active{color:#fff;background:#0ea5e9;border-color:#0ea5e9}
.lc-doctab-btn--contrat.active{color:#fff;background:#dc2626;border-color:#dc2626}
.lc-doctab-btn--assurance.active{color:#fff;background:#16a34a;border-color:#16a34a}
.lc-doctab-pane{display:none}
.lc-doctab-pane.active{display:block}
</style>
@endpush

@section('content')

@php
$panelPrefix   = request()->routeIs('super-admin.*') ? 'super-admin' : 'admin';
$defaultDocTab = $loan->canBeValidated() ? 'notification' : 'contrat';
@endphp

{{-- ── HEADER ── --}}
<div class="lc-header">
  <div class="lc-header-left">
    <div class="lc-header-icon"><i class="fas fa-file-contract"></i></div>
    <div style="min-width:0">
      <div class="lc-header-ref">
        Contrat — <span style="color:var(--c-accent)">{{ $loan->reference }}</span>
        <span class="lc-lang-badge" style="margin-left:.5rem;vertical-align:middle">
          <i class="fas fa-globe"></i> {{ strtoupper($loan->contract_language ?? 'FR') }}
        </span>
      </div>
      <div class="lc-header-sub">
        {{ $loan->name }}
        @if($loan->email) · {{ $loan->email }}@endif
        · {{ number_format($loan->amount,0,',',' ') }} {{ $loan->currency }}
        · {{ $loan->darly }} mois
      </div>
    </div>
  </div>

  <div class="lc-header-actions">
    <a href="{{ route($panelPrefix.'.loans.show', $loan) }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Dossier
    </a>
    <a href="{{ $loan->notification_pdf_path ? route($panelPrefix.'.loans.notification.pdf', $loan) : '#' }}"
       class="btn-ghost btn-sm-pro{{ !$loan->notification_pdf_path ? ' disabled' : '' }}"
       style="{{ !$loan->notification_pdf_path ? 'opacity:.4;pointer-events:none' : '' }}"
       target="_blank" title="Voir le document de notification">
      <i class="fas fa-bell" style="color:#0ea5e9"></i> Notification
    </a>
    <a href="{{ $loan->contract_pdf_path ? route($panelPrefix.'.loans.contract.viewer', $loan) : '#' }}"
       class="btn-ghost btn-sm-pro{{ !$loan->contract_pdf_path ? ' disabled' : '' }}"
       style="{{ !$loan->contract_pdf_path ? 'opacity:.4;pointer-events:none' : '' }}"
       title="Plein écran contrat">
      <i class="fas fa-file-contract" style="color:#dc2626"></i> Contrat
    </a>
    <a href="{{ $loan->insurance_pdf_path ? route($panelPrefix.'.loans.insurance.viewer', $loan) : '#' }}"
       class="btn-ghost btn-sm-pro{{ !$loan->insurance_pdf_path ? ' disabled' : '' }}"
       style="{{ !$loan->insurance_pdf_path ? 'opacity:.4;pointer-events:none' : '' }}"
       title="Plein écran assurance">
      <i class="fas fa-shield-alt" style="color:#16a34a"></i> Assurance
    </a>
    @if($loan->contract_pdf_path)
    <a href="{{ route($panelPrefix.'.loans.contract.pdf', $loan) }}"
       download="Contrat_{{ $loan->reference }}.pdf" class="btn-ghost btn-sm-pro">
      <i class="fas fa-download"></i> Télécharger
    </a>
    @endif
    @if($loan->contractTemplate?->hasDocxTemplate())
    <a href="{{ route($panelPrefix.'.loans.contract.docx', $loan) }}" class="btn-navy btn-sm-pro">
      <i class="fas fa-file-word"></i> Générer DOCX
    </a>
    @endif
  </div>
</div>

{{-- ── LAYOUT ── --}}
<div class="lc-layout">

  {{-- ════ PANEL GAUCHE (sticky) ════ --}}
  <div class="lc-panel">

    {{-- Récapitulatif chiffres clés --}}
    <div class="lc-pcard">
      <div class="lc-pcard-hdr">
        <div class="lc-pcard-ico" style="background:#FEF9EC;color:var(--c-accent-d)"><i class="fas fa-chart-pie"></i></div>
        <span class="lc-pcard-title">Récapitulatif</span>
        <span class="badge-status bs-{{ $loan->statusColor() }}" style="margin-left:auto;font-size:.62rem;padding:.2rem .6rem">
          {{ $loan->statusLabel() }}
        </span>
      </div>
      <div class="lc-pcard-body" style="padding:.5rem .875rem">
        @foreach([
          ['Référence',   $loan->reference, null],
          ['Client',      $loan->name, null],
          ['Montant',     number_format($loan->amount,2,',',' ').' '.$loan->currency, null],
          ['Durée',       $loan->darly.' mois', null],
          ['Mensualité',  number_format($loan->monthly_payment,2,',',' ').' '.$loan->currency, null],
          ['Taux',        $loan->interest_rate.' %', null],
          ['Total intérêts', number_format($loan->total_cost,2,',',' ').' '.$loan->currency, null],
        ] as [$lbl,$val])
        <div class="lc-sum-row">
          <span class="lc-sum-lbl">{{ $lbl }}</span>
          <span class="lc-sum-val">{{ $val }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Action contextuelle --}}
    <div class="lc-pcard">
      <div class="lc-pcard-hdr">
        <div class="lc-pcard-ico" style="background:#FEF9EC;color:var(--c-accent-d)"><i class="fas fa-bolt"></i></div>
        <span class="lc-pcard-title">Action</span>
      </div>
      <div class="lc-pcard-body">

        @if($loan->canBeValidated())
          @if($loan->notification_pdf_path)
          {{-- Prêt à valider --}}
          <div style="font-size:.78rem;color:var(--c-muted);margin-bottom:.625rem;line-height:1.5">
            Le document de notification sera envoyé à
            <strong style="color:var(--c-navy)">{{ $loan->email }}</strong> en
            <strong style="color:var(--c-navy)">{{ strtoupper($loan->contract_language ?? 'FR') }}</strong>.
          </div>
          <form action="{{ route($panelPrefix.'.loans.validate', $loan) }}" method="POST"
                onsubmit="confirmValidate(event); return false;">
            @csrf
            <button type="submit" class="btn-navy" style="width:100%;justify-content:center" id="validateBtn">
              <i class="fas fa-check"></i> Valider
            </button>
          </form>
          @else
          {{-- Document de notification manquant --}}
          <div style="background:#FFFBEB;border:1px solid #FDE68A;border-left:3px solid #F59E0B;border-radius:8px;padding:.625rem .75rem;font-size:.78rem;color:#78350F;display:flex;gap:.5rem;align-items:flex-start;margin-bottom:.25rem">
            <i class="fas fa-lock" style="color:#F59E0B;flex-shrink:0;margin-top:.1rem"></i>
            <span>Uploadez d'abord le <strong>document de notification</strong> ci-dessous pour débloquer la validation.</span>
          </div>
          @endif

        @elseif($loan->canSendContract())
          @if($loan->contract_pdf_path)
          {{-- Prêt à envoyer le contrat --}}
          <div style="font-size:.78rem;color:var(--c-muted);margin-bottom:.625rem;line-height:1.5">
            Le PDF du contrat sera envoyé avec le tableau d'amortissement à
            <strong style="color:var(--c-navy)">{{ $loan->email }}</strong> en
            <strong style="color:var(--c-navy)">{{ strtoupper($loan->contract_language ?? 'FR') }}</strong>.
          </div>
          <form action="{{ route($panelPrefix.'.loans.send-contract', $loan) }}" method="POST"
                onsubmit="confirmSendContract(event); return false;">
            @csrf
            <button type="submit" class="btn-navy" style="width:100%;justify-content:center" id="sendBtn">
              <i class="fas fa-paper-plane"></i> Envoyer le contrat
            </button>
          </form>
          <div style="font-size:.68rem;color:var(--c-muted);margin-top:.5rem;display:flex;align-items:flex-start;gap:.35rem">
            <i class="fas fa-info-circle" style="flex-shrink:0;margin-top:.15rem;color:var(--c-accent)"></i>
            <span>Une notification in-app est aussi envoyée au client.</span>
          </div>
          @else
          {{-- PDF contrat manquant --}}
          <div style="background:#FFFBEB;border:1px solid #FDE68A;border-left:3px solid #F59E0B;border-radius:8px;padding:.625rem .75rem;font-size:.78rem;color:#78350F;display:flex;gap:.5rem;align-items:flex-start;margin-bottom:.25rem">
            <i class="fas fa-lock" style="color:#F59E0B;flex-shrink:0;margin-top:.1rem"></i>
            <span>Uploadez d'abord le <strong>PDF du contrat</strong> ci-dessous pour débloquer l'envoi.</span>
          </div>
          @endif

        @elseif($loan->status === 'contract_sent')
        <div class="lc-action-sent">
          <i class="fas fa-check-circle" style="color:var(--c-green);font-size:.9rem;flex-shrink:0"></i>
          <div>
            <div style="font-size:.8rem;font-weight:700;color:#065F46">Contrat envoyé</div>
            <div style="font-size:.7rem;color:#047857">{{ $loan->sent_at?->format('d/m/Y \à H:i') }}</div>
          </div>
        </div>
        <form action="{{ route($panelPrefix.'.loans.contract.pdf.resend', $loan) }}" method="POST"
              data-confirm="Renvoyer le contrat à {{ $loan->email }} ?">
          @csrf
          <button type="submit" class="btn-ghost btn-sm-pro" style="width:100%;justify-content:center;margin-top:.5rem">
            <i class="fas fa-redo" style="color:var(--c-green)"></i> Renvoyer l'email
          </button>
        </form>
        <form action="{{ route($panelPrefix.'.loans.signed', $loan) }}" method="POST"
              data-confirm="Confirmer la réception du contrat signé ?"
              style="margin-top:.5rem">
          @csrf
          <button type="submit" class="btn-navy" style="width:100%;justify-content:center;background:var(--c-green);border-color:var(--c-green)">
            <i class="fas fa-file-signature"></i> Contrat signé reçu
          </button>
        </form>

        @elseif($loan->status === 'contract_signed')
        <div class="lc-action-signed">
          <i class="fas fa-file-signature" style="color:#7c3aed;font-size:.9rem;flex-shrink:0"></i>
          <div>
            <div style="font-size:.8rem;font-weight:700;color:#5B21B6">Contrat signé reçu</div>
            <div style="font-size:.7rem;color:#6D28D9">Le dossier avance vers la finalisation</div>
          </div>
        </div>

        @elseif($loan->status === 'finalized')
        <div class="lc-action-final">
          <i class="fas fa-check-double" style="color:var(--c-green);font-size:.9rem;flex-shrink:0"></i>
          <div>
            <div style="font-size:.8rem;font-weight:700;color:#065F46">Dossier finalisé</div>
            <div style="font-size:.7rem;color:#047857">Toutes les étapes sont complètes</div>
          </div>
        </div>

        @else
        <div style="padding:.625rem .75rem;background:#f8f9fa;border-radius:8px;font-size:.8rem;color:var(--c-muted);text-align:center">
          <i class="fas fa-clock" style="display:block;font-size:1.25rem;margin-bottom:.35rem;opacity:.35"></i>
          En attente — statut : <strong>{{ $loan->statusLabel() }}</strong>
        </div>
        @endif

      </div>
    </div>

    {{-- ── Séparateur NOTIFICATION ── --}}
    <div style="display:flex;align-items:center;gap:.5rem;margin:.25rem 0 -.25rem">
      <div style="width:3px;height:16px;border-radius:2px;background:#0ea5e9;flex-shrink:0"></div>
      <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#0ea5e9">Notification de validation</span>
      <div style="flex:1;height:1px;background:#e0f2fe"></div>
    </div>

    {{-- Document de notification --}}
    <div class="lc-pcard">
      <div class="lc-pcard-hdr">
        <div class="lc-pcard-ico" style="background:#F0F9FF;color:#0ea5e9"><i class="fas fa-file-pdf"></i></div>
        <span class="lc-pcard-title">Document de notification</span>
        @if($loan->notification_pdf_path)
        <a href="{{ route($panelPrefix.'.loans.notification.pdf',$loan) }}" target="_blank"
           class="btn-ghost btn-sm-pro" style="margin-left:auto;padding:.2rem .5rem;font-size:.7rem" title="Visualiser">
          <i class="fas fa-expand-alt"></i>
        </a>
        @endif
      </div>
      <div class="lc-pcard-body">
        @if($loan->notification_pdf_path)
        <div class="lc-pdf-file" style="background:#F0F9FF;border-color:#BAE6FD">
          <i class="fas fa-file-pdf" style="color:#0ea5e9;font-size:1.1rem;flex-shrink:0"></i>
          <div style="flex:1;min-width:0">
            <div style="font-size:.78rem;font-weight:700;color:#0369A1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $loan->reference }}_notification.pdf
            </div>
            <div style="font-size:.64rem;color:#0284C7;margin-top:.1rem">
              <i class="fas fa-check-circle"></i> Sera joint à la notification
            </div>
          </div>
          <a href="{{ route($panelPrefix.'.loans.notification.pdf',$loan) }}"
             class="btn-ghost btn-sm-pro" style="padding:.25rem .45rem;flex-shrink:0" target="_blank">
            <i class="fas fa-download"></i>
          </a>
        </div>
        @endif

        @if($notificationTemplate?->hasDocxTemplate())
        <a href="{{ route($panelPrefix.'.loans.notification.docx', $loan) }}"
           style="width:100%;justify-content:center;display:flex;align-items:center;gap:.45rem;padding:.55rem .875rem;font-size:.78rem;font-weight:700;border-radius:8px;cursor:pointer;background:#0ea5e9;color:#fff;text-decoration:none;margin-bottom:.625rem">
          <i class="fas fa-file-word"></i> Générer &amp; Télécharger DOCX
        </a>
        <div style="font-size:.63rem;color:var(--c-muted);margin-bottom:.75rem;text-align:center">
          Ouvrez → convertissez en PDF → uploadez ci-dessous
        </div>
        @elseif(!$notificationTemplate)
        <div style="font-size:.72rem;color:#78350F;background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:.5rem .625rem;margin-bottom:.625rem">
          <i class="fas fa-exclamation-triangle"></i> Aucun modèle de notification pour la langue
          <strong>{{ strtoupper($loan->contract_language ?? 'FR') }}</strong>.
          <a href="{{ route('admin.notification-templates.create') }}" style="color:#78350F;font-weight:700">Créer →</a>
        </div>
        @else
        <div style="font-size:.72rem;color:#78350F;background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:.5rem .625rem;margin-bottom:.625rem">
          <i class="fas fa-exclamation-triangle"></i> Le modèle « {{ $notificationTemplate->name }} » n'a pas de DOCX.
          <a href="{{ route('admin.notification-templates.edit',$notificationTemplate) }}" style="color:#78350F;font-weight:700">Configurer →</a>
        </div>
        @endif

        <form action="{{ route($panelPrefix.'.loans.notification.pdf.upload',$loan) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="lc-upload-zone" onclick="this.querySelector('input').click()">
            <i class="fas fa-cloud-upload-alt" style="color:var(--c-accent);font-size:1.3rem;display:block;margin-bottom:.35rem"></i>
            <div style="font-size:.76rem;font-weight:600;color:var(--c-navy)">
              {{ $loan->notification_pdf_path ? 'Remplacer le PDF' : 'Uploader le document PDF' }}
            </div>
            <div style="font-size:.65rem;color:var(--c-muted);margin-top:.15rem">PDF · max 20 Mo · Cliquez pour choisir</div>
            <input type="file" name="notification_pdf" accept=".pdf" required style="display:none"
                   onchange="this.closest('form').submit()">
          </div>
          @error('notification_pdf')
          <div style="font-size:.72rem;color:#dc2626;margin-top:.35rem">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
          </div>
          @enderror
        </form>
      </div>
    </div>

    {{-- ── Séparateur CONTRAT ── --}}
    <div style="display:flex;align-items:center;gap:.5rem;margin:.5rem 0 -.25rem">
      <div style="width:3px;height:16px;border-radius:2px;background:#dc2626;flex-shrink:0"></div>
      <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#dc2626">Contrat de prêt</span>
      <div style="flex:1;height:1px;background:#fee2e2"></div>
    </div>

    {{-- Upload PDF du contrat --}}
    <div class="lc-pcard">
      <div class="lc-pcard-hdr">
        <div class="lc-pcard-ico" style="background:#FFF1F2;color:#dc2626"><i class="fas fa-file-pdf"></i></div>
        <span class="lc-pcard-title">PDF du contrat</span>
        @if($loan->contract_pdf_path)
        <a href="{{ route($panelPrefix.'.loans.contract.viewer',$loan) }}"
           class="btn-ghost btn-sm-pro" style="margin-left:auto;padding:.2rem .5rem;font-size:.7rem" title="Visualiser plein écran">
          <i class="fas fa-expand-alt"></i>
        </a>
        @endif
      </div>
      <div class="lc-pcard-body">
        @if($loan->contract_pdf_path)
        <div class="lc-pdf-file">
          <i class="fas fa-file-pdf" style="color:#dc2626;font-size:1.1rem;flex-shrink:0"></i>
          <div style="flex:1;min-width:0">
            <div style="font-size:.78rem;font-weight:700;color:#9F1239;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $loan->reference }}.pdf
            </div>
            <div style="font-size:.64rem;color:#be123c;margin-top:.1rem">
              <i class="fas fa-check-circle"></i> Sera joint à l'envoi
            </div>
          </div>
          <a href="{{ route($panelPrefix.'.loans.contract.pdf',$loan) }}"
             class="btn-ghost btn-sm-pro" style="padding:.25rem .45rem;flex-shrink:0" target="_blank">
            <i class="fas fa-download"></i>
          </a>
        </div>
        @endif

        <form action="{{ route($panelPrefix.'.loans.contract.pdf.upload',$loan) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="lc-upload-zone" onclick="this.querySelector('input').click()">
            <i class="fas fa-cloud-upload-alt" style="color:var(--c-accent);font-size:1.3rem;display:block;margin-bottom:.35rem"></i>
            <div style="font-size:.76rem;font-weight:600;color:var(--c-navy)">
              {{ $loan->contract_pdf_path ? 'Remplacer le PDF' : 'Uploader le contrat signé' }}
            </div>
            <div style="font-size:.65rem;color:var(--c-muted);margin-top:.15rem">PDF · max 20 Mo · Cliquez pour choisir</div>
            <input type="file" name="contract_pdf" accept=".pdf" required style="display:none"
                   onchange="this.closest('form').submit()">
          </div>
          @error('contract_pdf')
          <div style="font-size:.72rem;color:#dc2626;margin-top:.35rem">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
          </div>
          @enderror
        </form>
      </div>
    </div>

    {{-- ── Séparateur ASSURANCE ── --}}
    <div style="display:flex;align-items:center;gap:.5rem;margin:.5rem 0 -.25rem">
      <div style="width:3px;height:16px;border-radius:2px;background:#16a34a;flex-shrink:0"></div>
      <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#16a34a">Assurance emprunteur</span>
      <div style="flex:1;height:1px;background:#dcfce7"></div>
    </div>

    {{-- Attestation d'assurance --}}
    <div class="lc-pcard">
      <div class="lc-pcard-hdr">
        <div class="lc-pcard-ico" style="background:#F0FDF4;color:#16a34a"><i class="fas fa-shield-alt"></i></div>
        <span class="lc-pcard-title">Attestation d'assurance</span>
        @if($loan->insurance_pdf_path)
        <a href="{{ route($panelPrefix.'.loans.insurance.viewer', $loan) }}"
           class="btn-ghost btn-sm-pro" style="margin-left:auto;padding:.2rem .5rem;font-size:.7rem" title="Visualiser plein écran">
          <i class="fas fa-expand-alt"></i>
        </a>
        @endif
      </div>
      <div class="lc-pcard-body">

        @if($loan->insurance_pdf_path)
        <div class="lc-pdf-file" style="background:#F0FDF4;border-color:#A7F3D0">
          <i class="fas fa-shield-alt" style="color:#16a34a;font-size:1.1rem;flex-shrink:0"></i>
          <div style="flex:1;min-width:0">
            <div style="font-size:.78rem;font-weight:700;color:#065F46;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $loan->reference }}_assurance.pdf
            </div>
            <div style="font-size:.64rem;color:#047857;margin-top:.1rem">
              <i class="fas fa-check-circle"></i> Attestation disponible
            </div>
          </div>
          <a href="{{ route($panelPrefix.'.loans.insurance.pdf', $loan) }}"
             class="btn-ghost btn-sm-pro" style="padding:.25rem .45rem;flex-shrink:0" target="_blank">
            <i class="fas fa-download"></i>
          </a>
        </div>
        @endif

        {{-- Modèle en cours --}}
        <div style="display:flex;align-items:center;gap:.5rem;padding:.4rem .6rem;background:#f8f9fa;border:1px solid var(--c-border);border-radius:7px">
          <i class="fas fa-file-medical-alt" style="color:#16a34a;font-size:.8rem;flex-shrink:0"></i>
          <div style="flex:1;min-width:0">
            <div style="font-size:.74rem;font-weight:700;color:var(--c-navy)">
              {{ $insuranceTemplate?->name ?? 'Aucun modèle' }}
            </div>
            <div style="font-size:.63rem;color:var(--c-muted)">
              {{ $insuranceTemplate ? strtoupper($insuranceTemplate->locale) : 'Non configuré pour cette langue' }}
              · <a href="{{ route('admin.notification-templates.create') }}" style="color:#16a34a;text-decoration:none">{{ $insuranceTemplate ? 'Modifier' : 'Créer' }} →</a>
            </div>
          </div>
        </div>

        {{-- Générer le DOCX personnalisé pour ce dossier --}}
        @if($insuranceTemplate?->hasDocxTemplate())
        <a href="{{ route($panelPrefix.'.loans.insurance.docx', $loan) }}"
           style="width:100%;justify-content:center;display:flex;align-items:center;gap:.45rem;padding:.55rem .875rem;font-size:.78rem;font-weight:700;border-radius:8px;cursor:pointer;background:#16a34a;color:#fff;text-decoration:none">
          <i class="fas fa-file-word"></i> Télécharger DOCX assurance
        </a>
        <div style="font-size:.63rem;color:var(--c-muted);margin-top:.25rem;text-align:center">
          Ouvrez → finalisez → exportez en PDF → uploadez ci-dessous
        </div>
        @else
        <div style="font-size:.68rem;color:var(--c-muted);text-align:center;padding:.4rem">
          Aucun template DOCX disponible pour la langue de ce dossier.
        </div>
        @endif

        {{-- Upload --}}
        <form action="{{ route($panelPrefix.'.loans.insurance.pdf.upload', $loan) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="lc-upload-zone" onclick="this.querySelector('input').click()"
               style="{{ $loan->insurance_pdf_path ? 'border-color:#A7F3D0;background:#F0FDF4' : '' }}">
            <i class="fas fa-file-upload" style="color:#16a34a;font-size:1.1rem;display:block;margin-bottom:.3rem"></i>
            <div style="font-size:.74rem;font-weight:600;color:var(--c-navy)">
              {{ $loan->insurance_pdf_path ? 'Remplacer l\'attestation' : 'Uploader un PDF existant' }}
            </div>
            <div style="font-size:.64rem;color:var(--c-muted);margin-top:.1rem">PDF · max 20 Mo</div>
            <input type="file" name="insurance_pdf" accept=".pdf" required style="display:none"
                   onchange="this.closest('form').submit()">
          </div>
          @error('insurance_pdf')
          <div style="font-size:.72rem;color:#dc2626;margin-top:.35rem">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
          </div>
          @enderror
        </form>

      </div>
    </div>

  </div>{{-- /lc-panel --}}

  {{-- ════ ZONE PRINCIPALE ════ --}}
  <div style="min-width:0;display:flex;flex-direction:column;gap:1.125rem">

    {{-- ── Sélecteur de document : un seul visible à la fois ── --}}
    <div class="lc-doctabs">
      <button type="button" class="lc-doctab-btn lc-doctab-btn--notification{{ $defaultDocTab==='notification' ? ' active':'' }}"
              onclick="lcDocTab(this,'notification')">
        <i class="fas fa-bell"></i> Notification
        @if($loan->notification_pdf_path)<span class="lc-doctab-badge">PDF</span>@endif
      </button>
      <button type="button" class="lc-doctab-btn lc-doctab-btn--contrat{{ $defaultDocTab==='contrat' ? ' active':'' }}"
              onclick="lcDocTab(this,'contrat')">
        <i class="fas fa-file-contract"></i> Contrat
        @if($loan->contract_pdf_path)<span class="lc-doctab-badge">PDF</span>@endif
      </button>
      <button type="button" class="lc-doctab-btn lc-doctab-btn--assurance"
              onclick="lcDocTab(this,'assurance')">
        <i class="fas fa-shield-alt"></i> Assurance
        @if($loan->insurance_pdf_path)<span class="lc-doctab-badge">PDF</span>@endif
      </button>
    </div>

    <div class="lc-pdf-card" style="border-radius:0 12px 12px 12px">

      {{-- Pane Notification --}}
      <div class="lc-doctab-pane{{ $defaultDocTab==='notification' ? ' active':'' }}" data-pane="notification">
        <div class="lc-pdf-toolbar">
          <div class="lc-pdf-title">
            <i class="fas fa-file-pdf" style="color:#0ea5e9"></i>
            Aperçu du document de notification
            <span class="lc-lang-badge" style="background:#F0F9FF;border-color:#BAE6FD;color:#0369A1">{{ strtoupper($loan->contract_language ?? 'FR') }}</span>
          </div>
          @if($loan->notification_pdf_path)
          <div class="lc-pdf-actions">
            <a href="{{ route($panelPrefix.'.loans.notification.pdf', $loan) }}" class="btn-ghost btn-sm-pro" target="_blank">
              <i class="fas fa-external-link-alt"></i> Nouvel onglet
            </a>
            <a href="{{ route($panelPrefix.'.loans.notification.pdf', $loan) }}"
               download="Notification_{{ $loan->reference }}.pdf" class="btn-ghost btn-sm-pro">
              <i class="fas fa-download"></i> Télécharger
            </a>
          </div>
          @endif
        </div>

        @if($loan->notification_pdf_path)
        <iframe
          data-src="{{ route($panelPrefix.'.loans.notification.pdf', $loan) }}"
          class="lc-pdf-frame"
          title="Notification {{ $loan->reference }}"
        ></iframe>
        @else
        <div class="lc-pdf-placeholder">
          <i class="fas fa-file-pdf" style="font-size:3rem;margin-bottom:.75rem;opacity:.2;color:#0ea5e9"></i>
          <div style="font-size:.9rem;font-weight:600;color:var(--c-navy)">Aucun document disponible</div>
          <div style="font-size:.78rem;color:var(--c-muted);margin-top:.3rem">Uploadez le document de notification dans le panneau gauche</div>
        </div>
        @endif
      </div>

      {{-- Pane Contrat --}}
      <div class="lc-doctab-pane{{ $defaultDocTab==='contrat' ? ' active':'' }}" data-pane="contrat">
        <div class="lc-pdf-toolbar">
          <div class="lc-pdf-title">
            <i class="fas fa-file-pdf" style="color:#dc2626"></i>
            Aperçu du contrat
            <span class="lc-lang-badge">{{ strtoupper($loan->contract_language ?? 'FR') }}</span>
          </div>
          @if($loan->contract_pdf_path)
          <div class="lc-pdf-actions">
            <a href="{{ route($panelPrefix.'.loans.contract.pdf', $loan) }}" class="btn-ghost btn-sm-pro" target="_blank">
              <i class="fas fa-external-link-alt"></i> Nouvel onglet
            </a>
            <a href="{{ route($panelPrefix.'.loans.contract.pdf', $loan) }}"
               download="Contrat_{{ $loan->reference }}.pdf" class="btn-ghost btn-sm-pro">
              <i class="fas fa-download"></i> Télécharger
            </a>
          </div>
          @endif
        </div>

        @if($loan->contract_pdf_path)
        <iframe
          data-src="{{ route($panelPrefix.'.loans.contract.pdf', $loan) }}"
          class="lc-pdf-frame"
          title="Aperçu contrat {{ $loan->reference }}"
        ></iframe>
        @else
        <div class="lc-pdf-placeholder">
          <i class="fas fa-file-pdf" style="font-size:3rem;margin-bottom:.75rem;opacity:.2"></i>
          <div style="font-size:.9rem;font-weight:600;color:var(--c-navy)">Aucun PDF disponible</div>
          <div style="font-size:.78rem;color:var(--c-muted);margin-top:.3rem">Uploadez le contrat signé dans le panneau gauche</div>
        </div>
        @endif
      </div>

      {{-- Pane Assurance --}}
      <div class="lc-doctab-pane" data-pane="assurance">
        <div class="lc-pdf-toolbar">
          <div class="lc-pdf-title">
            <i class="fas fa-shield-alt" style="color:#16a34a"></i>
            Attestation d'assurance emprunteur
          </div>
          @if($loan->insurance_pdf_path)
          <div class="lc-pdf-actions">
            <a href="{{ route($panelPrefix.'.loans.insurance.pdf', $loan) }}" class="btn-ghost btn-sm-pro" target="_blank">
              <i class="fas fa-external-link-alt"></i> Nouvel onglet
            </a>
            <a href="{{ route($panelPrefix.'.loans.insurance.pdf', $loan) }}"
               download="Assurance_{{ $loan->reference }}.pdf" class="btn-ghost btn-sm-pro">
              <i class="fas fa-download"></i> Télécharger
            </a>
          </div>
          @endif
        </div>

        @if($loan->insurance_pdf_path)
        <iframe
          data-src="{{ route($panelPrefix.'.loans.insurance.pdf', $loan) }}"
          class="lc-pdf-frame"
          title="Attestation assurance {{ $loan->reference }}"
        ></iframe>
        @else
        <div class="lc-pdf-placeholder">
          <i class="fas fa-shield-alt" style="font-size:3rem;margin-bottom:.75rem;opacity:.2;color:#16a34a"></i>
          <div style="font-size:.9rem;font-weight:600;color:var(--c-navy)">Aucune attestation disponible</div>
          <div style="font-size:.78rem;color:var(--c-muted);margin-top:.3rem">Uploadez l'attestation d'assurance dans le panneau gauche</div>
        </div>
        @endif
      </div>

    </div>{{-- /lc-pdf-card --}}

    {{-- Variables disponibles --}}
    <div class="lc-editor-card">
      <div class="lc-pcard-hdr" style="padding:.8rem 1.25rem">
        <div class="lc-pcard-ico" style="background:#EEF2FF;color:var(--c-navy)"><i class="fas fa-tags"></i></div>
        <span class="lc-pcard-title">Variables disponibles (contrat, assurance, notification)</span>
        <span style="margin-left:auto;font-size:.62rem;padding:.15rem .5rem;border-radius:10px;background:#e5e7eb;color:#6b7280;font-weight:700">{{ count($variables) }}</span>
      </div>
      <div style="padding:1.125rem">
        <div style="font-size:.78rem;color:var(--c-muted);margin-bottom:1rem">
          Cliquez sur <i class="fas fa-copy"></i> pour copier une variable, puis utilisez-la dans vos modèles DOCX ou HTML.
        </div>
        <div class="lc-vars-grid">
          @foreach($variables as $var => $desc)
          <div class="lc-var-chip">
            <div class="lc-var-chip-ico"><i class="fas fa-tag"></i></div>
            <div style="flex:1;min-width:0">
              <span class="lc-var-code">{{ $var }}</span>
              <span class="lc-var-desc">{{ $desc }}</span>
            </div>
            <button type="button" class="lc-copy-btn"
                    onclick="lcCopy('{{ $var }}', this)" title="Copier">
              <i class="fas fa-copy"></i>
            </button>
          </div>
          @endforeach
        </div>
      </div>

    </div>{{-- /lc-editor-card --}}
  </div>{{-- /zone principale --}}

</div>{{-- /lc-layout --}}

@endsection

@push('scripts')
<script>
function lcDocTab(btn, tab) {
  document.querySelectorAll('.lc-doctab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.lc-doctab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  const pane = document.querySelector('.lc-doctab-pane[data-pane="' + tab + '"]');
  if (pane) {
    pane.classList.add('active');
    lcLoadPaneIframe(pane);
  }
}

function lcLoadPaneIframe(pane) {
  const iframe = pane.querySelector('iframe[data-src]');
  if (iframe && !iframe.getAttribute('src')) {
    iframe.setAttribute('src', iframe.dataset.src);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const activePane = document.querySelector('.lc-doctab-pane.active');
  if (activePane) lcLoadPaneIframe(activePane);
});

function lcCopy(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    btn.innerHTML = '<i class="fas fa-check" style="color:#22c55e"></i>';
    setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i>', 1500);
  });
}

async function confirmValidate(e) {
  const form = e.target;
  const ok = await confirmModal(
    'Valider ce dossier et envoyer la notification à {{ $loan->email }} ?\n\n' +
    'Langue : {{ strtoupper($loan->contract_language ?? "FR") }}\n' +
    'Le document de notification sera joint au message.',
    { title: 'Valider le dossier', confirmLabel: 'Valider' }
  );
  if (ok) {
    const btn = document.getElementById('validateBtn');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours…';
    }
    form.submit();
  }
}

async function confirmSendContract(e) {
  const form = e.target;
  const ok = await confirmModal(
    'Envoyer le contrat à {{ $loan->email }} ?\n\n' +
    'Langue : {{ strtoupper($loan->contract_language ?? "FR") }}\n' +
    'Le PDF du contrat + tableau d\'amortissement seront joints au message.',
    { title: 'Envoyer le contrat', confirmLabel: 'Envoyer' }
  );
  if (ok) {
    const btn = document.getElementById('sendBtn');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours…';
    }
    form.submit();
  }
}

// Auto-submit feedback upload
document.querySelectorAll('.lc-upload-zone input[type="file"]').forEach(function(inp) {
  inp.addEventListener('change', function() {
    if (this.files[0]) {
      const zone = this.closest('.lc-upload-zone');
      zone.querySelector('i').style.color = '#22c55e';
      zone.querySelectorAll('div')[0].textContent = this.files[0].name;
      if (zone.querySelectorAll('div')[1]) zone.querySelectorAll('div')[1].textContent = 'Upload en cours…';
      this.closest('form').submit();
    }
  });
});
</script>
@endpush
