@extends('layouts.dashboard')
@section('title','Demandes de prêt')
@section('page_title','Demandes de prêt')

@push('styles')
<style>
/* ─────────────────────────────────────────
   LOANS INDEX — préfixe li-
   ───────────────────────────────────────── */

/* ── KPI strip ── */
.li-kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:.875rem;margin-bottom:1.5rem}
@media(max-width:900px){.li-kpi-row{grid-template-columns:repeat(2,1fr)}}
@media(max-width:500px){.li-kpi-row{grid-template-columns:1fr}}

.li-kpi{background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:.875rem 1.125rem;display:flex;align-items:center;gap:.875rem;position:relative;overflow:hidden}
.li-kpi::before{content:'';position:absolute;top:0;left:0;width:3px;height:100%;border-radius:12px 0 0 12px}
.li-kpi--navy::before{background:var(--c-navy)}
.li-kpi--amber::before{background:#d97706}
.li-kpi--blue::before{background:#0891b2}
.li-kpi--green::before{background:#059669}

.li-kpi-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0}
.li-kpi--navy .li-kpi-icon{background:#f0f3ff;color:var(--c-navy)}
.li-kpi--amber .li-kpi-icon{background:#fffbeb;color:#d97706}
.li-kpi--blue  .li-kpi-icon{background:#ecfeff;color:#0891b2}
.li-kpi--green .li-kpi-icon{background:#f0fdf4;color:#059669}

.li-kpi-val{font-size:1.5rem;font-weight:900;color:var(--c-navy);line-height:1}
.li-kpi-lbl{font-size:.7rem;font-weight:600;color:var(--c-muted);margin-top:.15rem;text-transform:uppercase;letter-spacing:.04em}

/* ── Header ── */
.li-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem}
.li-header-title{font-size:1.2rem;font-weight:800;color:var(--c-navy);margin:0 0 .2rem}
.li-header-sub{font-size:.78rem;color:var(--c-muted);margin:0}

/* ── Status chips ── */
.li-filters-strip{display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:1.125rem}
.li-chip{display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:999px;font-size:.72rem;font-weight:600;border:1.5px solid var(--c-border);background:#fff;color:var(--c-muted);cursor:pointer;text-decoration:none;transition:.15s;white-space:nowrap}
.li-chip:hover{border-color:var(--c-navy);color:var(--c-navy);text-decoration:none}
.li-chip.active{background:var(--c-navy);border-color:var(--c-navy);color:#fff}
.li-chip-count{font-size:.6rem;padding:.1rem .3rem;border-radius:8px;background:rgba(0,0,0,.08);font-weight:800;min-width:16px;text-align:center}
.li-chip.active .li-chip-count{background:rgba(255,255,255,.2)}

.li-chip[data-s="draft"]:not(.active):hover{border-color:#6b7280;color:#6b7280}
.li-chip[data-s="draft"].active{background:#6b7280;border-color:#6b7280}
.li-chip[data-s="pending"]:not(.active):hover{border-color:#d97706;color:#d97706}
.li-chip[data-s="pending"].active{background:#d97706;border-color:#d97706}
.li-chip[data-s="validated"]:not(.active):hover{border-color:#2563eb;color:#2563eb}
.li-chip[data-s="validated"].active{background:#2563eb;border-color:#2563eb}
.li-chip[data-s="contract_sent"]:not(.active):hover{border-color:#0891b2;color:#0891b2}
.li-chip[data-s="contract_sent"].active{background:#0891b2;border-color:#0891b2}
.li-chip[data-s="contract_signed"]:not(.active):hover{border-color:#7c3aed;color:#7c3aed}
.li-chip[data-s="contract_signed"].active{background:#7c3aed;border-color:#7c3aed}
.li-chip[data-s="finalized"]:not(.active):hover{border-color:#059669;color:#059669}
.li-chip[data-s="finalized"].active{background:#059669;border-color:#059669}
.li-chip[data-s="rejected"]:not(.active):hover{border-color:#dc2626;color:#dc2626}
.li-chip[data-s="rejected"].active{background:#dc2626;border-color:#dc2626}

/* ── Search bar ── */
.li-search-bar{background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:.625rem 1rem;display:flex;gap:.5rem;align-items:center;margin-bottom:1.125rem;flex-wrap:wrap}
.li-search-input{flex:1;min-width:180px;padding:.45rem .75rem;border:1.5px solid var(--c-border);border-radius:8px;font-size:.82rem;color:var(--c-navy);background:#f8f9fa;outline:none;transition:.15s;font-family:inherit}
.li-search-input:focus{border-color:var(--c-accent);background:#fff;box-shadow:0 0 0 3px rgba(198,161,91,.1)}
.li-search-input::placeholder{color:#c4cadc}

/* ── Table card ── */
.li-card{background:#fff;border:1px solid var(--c-border);border-radius:14px;overflow:hidden}

.li-table-meta{display:flex;align-items:center;justify-content:space-between;padding:.625rem 1.125rem;border-bottom:1px solid var(--c-border);background:#fafbfc;flex-wrap:wrap;gap:.5rem}
.li-table-meta-count{font-size:.75rem;color:var(--c-muted)}
.li-table-meta-count strong{color:var(--c-navy);font-weight:700}

table.li-tbl{width:100%;border-collapse:collapse}
table.li-tbl thead th{padding:.65rem 1rem;font-size:.65rem;font-weight:700;color:var(--c-muted);text-transform:uppercase;letter-spacing:.07em;background:#fafbfc;border-bottom:1px solid var(--c-border);white-space:nowrap;text-align:left}
table.li-tbl tbody td{padding:.8rem 1rem;font-size:.81rem;color:var(--c-text);border-bottom:1px solid #f3f4f6;vertical-align:middle}
table.li-tbl tbody tr:last-child td{border-bottom:0}
table.li-tbl tbody tr:hover td{background:#f8faff}
table.li-tbl tbody td:first-child{border-left:3px solid transparent;transition:border-color .15s}
table.li-tbl tbody tr:hover td:first-child{border-left-color:var(--c-accent)}

/* ── Cellules ── */
.li-ref-link{font-family:monospace;font-weight:800;font-size:.8rem;color:var(--c-navy);letter-spacing:.02em;text-decoration:none;transition:.15s}
.li-ref-link:hover{color:var(--c-accent)}
.li-archive{font-size:.62rem;color:var(--c-muted);font-family:monospace;margin-top:.15rem}
.li-pdf-pill{display:inline-flex;align-items:center;gap:.2rem;font-size:.6rem;font-weight:700;padding:.1rem .35rem;border-radius:4px;background:#FFF1F2;color:#dc2626;border:1px solid #FECDD3;margin-left:.35rem;vertical-align:middle;text-transform:uppercase;letter-spacing:.03em}
.li-fin-pill{display:inline-flex;align-items:center;gap:.2rem;font-size:.62rem;font-weight:700;padding:.1rem .4rem;border-radius:4px;background:#F5F3FF;color:#6d28d9;border:1px solid #DDD6FE;margin-top:.3rem}

.li-avatar{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.7rem;flex-shrink:0}
.li-avatar--client{background:linear-gradient(135deg,var(--c-navy),#1a3a6c);color:var(--c-accent)}
.li-avatar--admin{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff}

.li-name{font-weight:600;color:var(--c-navy);font-size:.82rem}
.li-sub{font-size:.68rem;color:var(--c-muted);margin-top:.05rem}

.li-amount{font-weight:800;color:var(--c-navy);font-size:.875rem}
.li-duration{display:inline-flex;align-items:center;gap:.2rem;font-size:.62rem;font-weight:700;padding:.1rem .35rem;border-radius:4px;background:#EFF6FF;color:#1d4ed8;margin-top:.2rem}
.li-rate{font-size:.65rem;color:var(--c-muted);margin-left:.3rem}

.li-monthly{font-weight:700;color:var(--c-navy);font-size:.82rem}
.li-monthly-cur{font-size:.68rem;color:var(--c-muted)}

.li-date-main{font-size:.75rem;color:var(--c-muted)}
.li-date-rel{font-size:.64rem;color:var(--c-muted)}

/* ── Status pills ── */
.li-status{display:inline-flex;align-items:center;gap:.3rem;font-size:.7rem;font-weight:700;padding:.25rem .6rem;border-radius:999px;white-space:nowrap}
.li-status--draft{background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb}
.li-status--pending{background:#fffbeb;color:#b45309;border:1px solid #fde68a}
.li-status--validated{background:#EFF6FF;color:#1d4ed8;border:1px solid #bfdbfe}
.li-status--contract_sent{background:#ecfeff;color:#0e7490;border:1px solid #a5f3fc}
.li-status--contract_signed{background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe}
.li-status--finalized{background:#f0fdf4;color:#065f46;border:1px solid #a7f3d0}
.li-status--rejected{background:#FFF1F2;color:#be123c;border:1px solid #fecdd3}
.li-sent-info{font-size:.63rem;color:var(--c-muted);margin-top:.2rem}

/* ── Actions ── */
.li-act{display:flex;gap:.25rem;justify-content:flex-end;align-items:center}

/* ── Progress track ── */
.li-progress{display:flex;gap:2px;margin-top:.3rem}
.li-progress-dot{width:6px;height:6px;border-radius:50%;background:var(--c-border)}
.li-progress-dot.done{background:var(--c-accent)}
.li-progress-dot.current{background:var(--c-navy)}

/* ── Empty state ── */
.li-empty{padding:3.5rem 1rem;text-align:center}
.li-empty-icon{font-size:2.25rem;color:var(--c-muted);opacity:.2;display:block;margin-bottom:.75rem}
.li-empty-title{font-size:.9rem;font-weight:700;color:var(--c-navy);margin-bottom:.3rem}
.li-empty-sub{font-size:.78rem;color:var(--c-muted);margin-bottom:1.125rem}

/* ── Pagination ── */
.li-pagi{padding:.75rem 1.125rem;border-top:1px solid var(--c-border)}

/* ─────────────────────────────────────────
   RESPONSIVE MOBILE — li-tbl
   ─────────────────────────────────────────*/
@media(max-width:900px){
  .li-header{flex-direction:column;align-items:flex-start}
  .li-header .btn-navy{align-self:flex-end}
  .li-search-bar{flex-wrap:wrap}
}
@media(max-width:640px){
  .li-header .btn-navy{width:100%;justify-content:center;align-self:stretch}
  /* Chips : scroll horizontal sans retour à la ligne */
  .li-filters-strip{flex-wrap:nowrap;overflow-x:auto;padding-bottom:.375rem;-webkit-overflow-scrolling:touch;scrollbar-width:none}
  .li-filters-strip::-webkit-scrollbar{display:none}
  .li-chip{flex-shrink:0}
  /* Méta : empilé */
  .li-table-meta{flex-direction:column;align-items:flex-start;gap:.2rem}
  /* Conteneur scroll → désactivé, on affiche des cartes */
  .li-tbl-wrap{overflow-x:visible}
  /* ── Tableau → cartes ── */
  table.li-tbl thead{display:none}
  table.li-tbl tbody tr{
    display:block;
    background:#fff;
    border:1px solid var(--c-border);
    border-radius:10px;
    margin-bottom:.75rem;
    overflow:hidden;
  }
  table.li-tbl tbody tr:hover td{background:transparent}
  table.li-tbl tbody td{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:.4rem 1rem;
    border-bottom:1px solid #f3f4f6;
    font-size:.8rem;
    min-height:36px;
    border-left:none !important;
  }
  table.li-tbl tbody td:last-child{border-bottom:none}
  table.li-tbl tbody td[data-label]:not([data-label=""])::before{
    content:attr(data-label);
    font-size:.65rem;font-weight:700;color:var(--c-muted);
    text-transform:uppercase;letter-spacing:.05em;
    flex-shrink:0;margin-right:.75rem;white-space:nowrap;
  }
  /* Référence : en-tête de carte */
  table.li-tbl tbody td:first-child{
    padding:.75rem 1rem;
    background:#fafbfc;
    border-bottom:1px solid var(--c-border);
    min-height:44px;
  }
  table.li-tbl tbody td:first-child::before{display:none}
  /* Actions : barre inférieure centrée */
  table.li-tbl tbody td:last-child{
    justify-content:center;
    gap:.625rem;
    flex-wrap:wrap;
    padding:.625rem 1rem;
    background:#fafbfc;
    border-top:1px solid var(--c-border);
  }
  table.li-tbl tbody td:last-child::before{display:none}
  table.li-tbl tbody td:last-child .btn-icon{width:44px;height:44px;font-size:.95rem}
  /* Barre de recherche : colonne sur petits écrans */
  .li-search-bar{flex-direction:column;align-items:stretch}
  .li-search-input{min-width:0;width:100%}
}
</style>
@endpush

@section('content')

@php
$statusChips = [
    ''               => ['Tous',           $stats['total'],          'fa-layer-group'],
    'draft'          => ['Brouillons',     $stats['draft'],          'fa-pen'],
    'pending'        => ['En attente',     $stats['pending'],        'fa-hourglass-half'],
    'validated'      => ['Validées',       $stats['validated'],      'fa-check-circle'],
    'contract_sent'  => ['Envoyé',         $stats['contract_sent'],  'fa-paper-plane'],
    'contract_signed'=> ['Signé',          $stats['contract_signed'],'fa-file-signature'],
    'finalized'      => ['Finalisées',     $stats['finalized'],      'fa-flag-checkered'],
    'rejected'       => ['Rejetées',       $stats['rejected'],       'fa-ban'],
];
$currentStatus = request('status','');

// Étapes pour la mini-barre de progression (0-based)
$stepMap = [
    'draft'           => 0,
    'pending'         => 1,
    'validated'       => 2,
    'contract_sent'   => 3,
    'contract_signed' => 4,
    'finalized'       => 5,
    'rejected'        => -1,
];
@endphp

{{-- ── HEADER ── --}}
<div class="li-header">
  <div>
    <div class="li-header-title">
      <i class="fas fa-folder-open" style="color:var(--c-accent);margin-right:.4rem;font-size:1rem"></i>
      Demandes de prêt
    </div>
    <p class="li-header-sub">
      {{ $stats['total'] }} dossier(s) au total
      @if($stats['pending']) &nbsp;·&nbsp; <span style="color:#d97706;font-weight:600">{{ $stats['pending'] }} en attente</span>@endif
      @if($stats['contract_sent']) &nbsp;·&nbsp; <span style="color:#0891b2;font-weight:600">{{ $stats['contract_sent'] }} contrat(s) envoyé(s)</span>@endif
    </p>
  </div>
  <a href="{{ route('admin.loans.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Nouvelle demande
  </a>
</div>

{{-- ── KPI STRIP ── --}}
<div class="li-kpi-row">
  <div class="li-kpi li-kpi--navy">
    <div class="li-kpi-icon"><i class="fas fa-layer-group"></i></div>
    <div>
      <div class="li-kpi-val">{{ $stats['total'] }}</div>
      <div class="li-kpi-lbl">Total</div>
    </div>
  </div>
  <div class="li-kpi li-kpi--amber">
    <div class="li-kpi-icon"><i class="fas fa-hourglass-half"></i></div>
    <div>
      <div class="li-kpi-val">{{ $stats['pending'] }}</div>
      <div class="li-kpi-lbl">En attente</div>
    </div>
  </div>
  <div class="li-kpi li-kpi--blue">
    <div class="li-kpi-icon"><i class="fas fa-paper-plane"></i></div>
    <div>
      <div class="li-kpi-val">{{ $stats['contract_sent'] + $stats['contract_signed'] }}</div>
      <div class="li-kpi-lbl">Contrats en cours</div>
    </div>
  </div>
  <div class="li-kpi li-kpi--green">
    <div class="li-kpi-icon"><i class="fas fa-flag-checkered"></i></div>
    <div>
      <div class="li-kpi-val">{{ $stats['finalized'] }}</div>
      <div class="li-kpi-lbl">Finalisées</div>
    </div>
  </div>
</div>

{{-- ── CHIPS DE STATUT ── --}}
<div class="li-filters-strip">
  @foreach($statusChips as $val => [$lbl, $count, $ico])
  @if($count > 0 || $val === '')
  <a href="{{ route('admin.loans.index', array_merge(request()->except('status','page'), $val ? ['status'=>$val] : [])) }}"
     class="li-chip {{ $currentStatus === $val ? 'active' : '' }}"
     data-s="{{ $val }}">
    <i class="fas {{ $ico }}" style="font-size:.62rem"></i>
    {{ $lbl }}
    <span class="li-chip-count">{{ $count }}</span>
  </a>
  @endif
  @endforeach
</div>

{{-- ── BARRE DE RECHERCHE ── --}}
<form method="GET" action="{{ route('admin.loans.index') }}" class="li-search-bar">
  @if($currentStatus)
  <input type="hidden" name="status" value="{{ $currentStatus }}">
  @endif
  <i class="fas fa-search" style="color:var(--c-muted);font-size:.8rem;flex-shrink:0"></i>
  <input type="text" name="search" class="li-search-input"
         placeholder="Référence, nom, email…"
         value="{{ request('search') }}"
         autocomplete="off">
  @if($isSuperAdmin && $admins->isNotEmpty())
  <select name="admin_id" class="li-search-input" style="flex:0;min-width:155px;cursor:pointer">
    <option value="">— Tous les admins —</option>
    @foreach($admins as $a)
    <option value="{{ $a->id }}" {{ request('admin_id') == $a->id ? 'selected' : '' }}>
      {{ $a->name }}
    </option>
    @endforeach
  </select>
  @endif
  <select name="type_financement" class="li-search-input" style="flex:0;min-width:170px;cursor:pointer">
    <option value="">— Tous les financements —</option>
    @foreach(\App\Models\LoanRequest::FINANCING_TYPES as $code => $label)
    <option value="{{ $code }}" {{ request('type_financement') == $code ? 'selected' : '' }}>
      {{ $label }}
    </option>
    @endforeach
  </select>
  <button type="submit" class="btn-navy btn-sm-pro">
    <i class="fas fa-search"></i> Chercher
  </button>
  @if(request()->anyFilled(['search','status','admin_id','type_financement']))
  <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-times"></i> Effacer
  </a>
  @endif
</form>

{{-- ── TABLE ── --}}
<div class="li-card">

  <div class="li-table-meta">
    <div class="li-table-meta-count">
      @if(request()->anyFilled(['search','status','admin_id','type_financement']))
        <strong>{{ $loans->total() }}</strong> résultat(s)
        @if(request('search')) pour <em>«&nbsp;{{ request('search') }}&nbsp;»</em>@endif
        @if(request('status')) — <em>{{ $statusChips[request('status')][0] ?? '' }}</em>@endif
        @if(request('admin_id') && $isSuperAdmin)
          — Admin : <em>{{ $admins->firstWhere('id', request('admin_id'))?->name }}</em>
        @endif
        @if(request('type_financement'))
          — <em>{{ \App\Models\LoanRequest::FINANCING_TYPES[request('type_financement')] ?? '' }}</em>
        @endif
      @else
        <strong>{{ $loans->total() }}</strong> dossier(s) au total
      @endif
    </div>
    <div style="font-size:.68rem;color:var(--c-muted)">
      Page {{ $loans->currentPage() }} / {{ max($loans->lastPage(),1) }}
    </div>
  </div>

  <div class="li-tbl-wrap" style="overflow-x:auto">
    <table class="li-tbl">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          @if($isSuperAdmin)<th>Admin</th>@endif
          <th>Montant</th>
          <th>Mensualité</th>
          <th>Statut</th>
          <th>Date</th>
          <th style="text-align:right;padding-right:1.25rem">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        @php
          $step    = $stepMap[$loan->status] ?? 0;
          $maxStep = 5;
        @endphp
        <tr>

          {{-- Référence --}}
          <td data-label="Référence">
            <a href="{{ route('admin.loans.show',$loan) }}" class="li-ref-link">
              {{ $loan->reference }}
            </a>
            @if($loan->contract_pdf_path)
              <span class="li-pdf-pill"><i class="fas fa-file-pdf"></i> PDF</span>
            @endif
            @if($loan->archive_ref)
            <div class="li-archive">{{ $loan->archive_ref }}</div>
            @endif
            @if($loan->type_financement)
            <div><span class="li-fin-pill"><i class="fas fa-tag" style="font-size:.55rem"></i> {{ $loan->financingTypeLabel() }}</span></div>
            @endif
          </td>

          {{-- Client --}}
          <td data-label="Client">
            <div style="display:flex;align-items:center;gap:.55rem">
              <div class="li-avatar li-avatar--client">
                {{ strtoupper(substr($loan->client?->name ?? $loan->name,0,1)) }}
              </div>
              <div>
                <div class="li-name">{{ $loan->client?->name ?? $loan->name }}</div>
                <div class="li-sub">{{ $loan->client?->email ?? $loan->email }}</div>
              </div>
            </div>
          </td>

          {{-- Admin (super-admin seulement) --}}
          @if($isSuperAdmin)
          <td data-label="Admin">
            @if($loan->admin)
            <div style="display:flex;align-items:center;gap:.45rem">
              <div class="li-avatar li-avatar--admin" style="width:26px;height:26px;border-radius:6px;font-size:.65rem">
                {{ strtoupper(substr($loan->admin->name,0,1)) }}
              </div>
              <div class="li-sub" style="font-size:.75rem;color:var(--c-navy);font-weight:600">
                {{ $loan->admin->name }}
              </div>
            </div>
            @else
              <span class="li-sub">—</span>
            @endif
          </td>
          @endif

          {{-- Montant --}}
          <td data-label="Montant">
            <div class="li-amount">
              {{ number_format((float)$loan->amount,0,',',' ') }}
              <span style="font-size:.68rem;font-weight:500;color:var(--c-muted)">{{ $loan->currency }}</span>
            </div>
            <div>
              <span class="li-duration"><i class="fas fa-calendar-alt" style="font-size:.55rem"></i> {{ $loan->darly }} mois</span>
              <span class="li-rate">{{ $loan->interest_rate }}%</span>
            </div>
          </td>

          {{-- Mensualité --}}
          <td data-label="Mensualité">
            <div class="li-monthly">{{ number_format((float)$loan->monthly_payment,2,',',' ') }}</div>
            <div class="li-monthly-cur">{{ $loan->currency }}/mois</div>
          </td>

          {{-- Statut --}}
          <td data-label="Statut">
            <span class="li-status li-status--{{ $loan->status }}">
              @php
                $sIcons = [
                  'draft'           => 'fa-pen',
                  'pending'         => 'fa-hourglass-half',
                  'validated'       => 'fa-check-circle',
                  'contract_sent'   => 'fa-paper-plane',
                  'contract_signed' => 'fa-file-signature',
                  'finalized'       => 'fa-flag-checkered',
                  'rejected'        => 'fa-ban',
                ];
              @endphp
              <i class="fas {{ $sIcons[$loan->status] ?? 'fa-circle' }}" style="font-size:.62rem"></i>
              {{ $loan->statusLabel() }}
            </span>
            @if($loan->sent_at && in_array($loan->status, ['contract_sent','contract_signed','finalized']))
            <div class="li-sent-info">Envoyé le {{ $loan->sent_at->format('d/m/Y') }}</div>
            @endif
            @if($loan->status === 'rejected')
            <div class="li-sent-info" style="color:#dc2626">Rejeté</div>
            @endif
          </td>

          {{-- Date --}}
          <td data-label="Date">
            <div class="li-date-main">{{ $loan->created_at->format('d/m/Y') }}</div>
            <div class="li-date-rel">{{ $loan->created_at->diffForHumans() }}</div>
          </td>

          {{-- Actions contextuelles par statut --}}
          <td data-label="">
            <div class="li-act">

              {{-- Voir le dossier — toujours disponible --}}
              <a href="{{ route('admin.loans.show',$loan) }}"
                 class="btn-icon btn-icon-primary" title="Voir le dossier">
                <i class="fas fa-eye"></i>
              </a>

              @if($loan->isEditable())
                {{-- Modifiable → bouton Modifier --}}
                <a href="{{ route('admin.loans.edit',$loan) }}"
                   class="btn-icon" title="Modifier">
                  <i class="fas fa-pen"></i>
                </a>
              @endif

              @if(in_array($loan->status, ['draft','pending','validated']))
                {{-- Accès au contrat pour préparer / valider --}}
                <a href="{{ route('admin.loans.contract',$loan) }}"
                   class="btn-icon" title="Contrat" style="color:#d97706">
                  <i class="fas fa-file-contract"></i>
                </a>

              @elseif($loan->status === 'contract_sent')
                {{-- Contrat envoyé → renvoyer depuis la page contrat --}}
                <a href="{{ route('admin.loans.contract',$loan) }}"
                   class="btn-icon" title="Renvoyer le contrat" style="color:#0891b2">
                  <i class="fas fa-paper-plane"></i>
                </a>

              @elseif($loan->status === 'contract_signed')
                {{-- Contrat signé → finaliser --}}
                <a href="{{ route('admin.loans.contract',$loan) }}"
                   class="btn-icon" title="Finaliser le dossier" style="color:#7c3aed">
                  <i class="fas fa-file-signature"></i>
                </a>

              @elseif($loan->status === 'finalized')
                <span class="btn-icon" style="opacity:.3;cursor:default" title="Dossier finalisé">
                  <i class="fas fa-flag-checkered"></i>
                </span>

              @elseif($loan->status === 'rejected')
                <span class="btn-icon" style="opacity:.3;cursor:default;color:#dc2626" title="Dossier rejeté">
                  <i class="fas fa-ban"></i>
                </span>
              @endif

            </div>
          </td>

        </tr>
        @empty
        <tr>
          <td colspan="{{ $isSuperAdmin ? 8 : 7 }}">
            <div class="li-empty">
              <i class="fas fa-folder-open li-empty-icon"></i>
              <div class="li-empty-title">Aucune demande trouvée</div>
              <div class="li-empty-sub">
                @if(request()->anyFilled(['search','status','admin_id','type_financement']))
                  Aucun résultat pour ces critères.
                  <a href="{{ route('admin.loans.index') }}" style="color:var(--c-navy);font-weight:600">Effacer les filtres</a>
                @else
                  Aucun dossier de prêt pour le moment.
                @endif
              </div>
              <a href="{{ route('admin.loans.create') }}" class="btn-navy btn-sm-pro">
                <i class="fas fa-plus"></i> Nouvelle demande
              </a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($loans->hasPages())
  <div class="li-pagi">
    {{ $loans->appends(request()->query())->links('partials.pagination') }}
  </div>
  @endif

</div>

@endsection
