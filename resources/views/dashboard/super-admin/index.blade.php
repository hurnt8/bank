@extends('layouts.dashboard')
@section('title', 'Super Administration — ' . site_name())
@section('page_title', 'Vue d\'ensemble système')

@push('styles')
<style>
/* ══════════════════════════════════════════
   SUPER ADMIN DASHBOARD — préfixe sadb-
   ══════════════════════════════════════════ */

/* ── Hero ── */
.sadb-hero {
  background: linear-gradient(135deg, #050f20 0%, #0b1f42 55%, #091830 100%);
  border-radius: 16px;
  padding: 2rem 2.25rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
  position: relative;
  overflow: hidden;
}
.sadb-hero::before {
  content: '';
  position: absolute; top: -60px; right: -60px;
  width: 260px; height: 260px; border-radius: 50%;
  background: radial-gradient(circle, rgba(198,161,91,.12) 0%, transparent 70%);
  pointer-events: none;
}
.sadb-hero::after {
  content: '';
  position: absolute; bottom: -80px; left: 30%;
  width: 200px; height: 200px; border-radius: 50%;
  background: radial-gradient(circle, rgba(37,99,235,.08) 0%, transparent 70%);
  pointer-events: none;
}

.sadb-hero-left { position: relative; z-index: 1; }
.sadb-hero-tag {
  display: inline-flex; align-items: center; gap: .4rem;
  font-size: .6rem; font-weight: 800; letter-spacing: .12em;
  text-transform: uppercase; color: var(--c-accent);
  background: rgba(198,161,91,.12); border: 1px solid rgba(198,161,91,.2);
  border-radius: 999px; padding: .2rem .6rem;
  margin-bottom: .5rem;
}
.sadb-hero-title { font-size: 1.5rem; font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: .35rem; }
.sadb-hero-sub   { font-size: .78rem; color: rgba(255,255,255,.35); }

.sadb-hero-stats {
  display: flex; align-items: stretch; gap: 0;
  position: relative; z-index: 1;
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 14px; overflow: hidden;
}
.sadb-hero-stat {
  padding: 1rem 1.625rem; text-align: center;
}
.sadb-hero-stat + .sadb-hero-stat {
  border-left: 1px solid rgba(255,255,255,.1);
}
.sadb-hero-stat-val {
  display: block; font-size: 1.625rem; font-weight: 900; color: var(--c-accent); line-height: 1;
}
.sadb-hero-stat-lbl {
  display: block; font-size: .62rem; color: rgba(255,255,255,.35); margin-top: .3rem; white-space: nowrap;
}

/* ── User type badges ── */
.sadb-hero-users {
  display: flex; gap: .625rem; margin-top: .75rem; flex-wrap: wrap;
}
.sadb-user-chip {
  display: flex; align-items: center; gap: .35rem;
  font-size: .65rem; font-weight: 700; padding: .2rem .55rem;
  border-radius: 999px; white-space: nowrap;
}
.sadb-user-chip-dot { width: 6px; height: 6px; border-radius: 50%; }

/* ── KPI grid ── */
.sadb-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media(max-width:1100px) { .sadb-kpi-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:575px)  { .sadb-kpi-grid { grid-template-columns: 1fr 1fr; } }

.sadb-kpi {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  padding: 1.25rem 1.375rem;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  position: relative; overflow: hidden;
  transition: box-shadow .15s, transform .15s;
}
.sadb-kpi:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); transform: translateY(-2px); }
.sadb-kpi-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: .75rem; }
.sadb-kpi-ico {
  width: 46px; height: 46px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; font-size: 1rem;
}
.sadb-kpi-val { font-size: 2.125rem; font-weight: 900; color: var(--c-navy); line-height: 1; margin-bottom: .2rem; }
.sadb-kpi-lbl { font-size: .72rem; color: var(--c-muted); font-weight: 500; }
.sadb-kpi-sub { margin-top: .5rem; display: flex; gap: .35rem; flex-wrap: wrap; }
.sadb-kpi-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 14px 14px; }

/* icon colors */
.sadb-ico-navy { background: rgba(14,59,46,.08); color: var(--c-navy); }
.sadb-ico-accent { background: rgba(198,161,91,.12); color: #a07d20; }
.sadb-ico-amber{ background: rgba(217,119,6,.1);  color: #D97706; }
.sadb-ico-green{ background: rgba(5,150,105,.1);  color: #059669; }
.sadb-ico-blue { background: rgba(37,99,235,.1);  color: #2563EB; }
.sadb-ico-violet{ background: rgba(124,58,237,.1); color: #7C3AED; }

/* ── Middle row ── */
.sadb-mid {
  display: grid;
  grid-template-columns: 5fr 7fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media(max-width:991px) { .sadb-mid { grid-template-columns: 1fr; } }

/* ── Card ── */
.sadb-card {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  display: flex; flex-direction: column;
}
.sadb-card-hdr {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.25rem; border-bottom: 1px solid var(--c-border); flex-shrink: 0;
}
.sadb-card-title {
  font-size: .825rem; font-weight: 800; color: var(--c-navy);
  display: flex; align-items: center; gap: .5rem;
}
.sadb-card-title-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.sadb-card-body { padding: 1.125rem 1.25rem; flex: 1; }
.sadb-view-all {
  font-size: .7rem; font-weight: 700; color: var(--c-muted);
  text-decoration: none; display: flex; align-items: center; gap: .3rem; transition: .15s;
}
.sadb-view-all:hover { color: var(--c-navy); }

/* ── Donut ── */
.sadb-ring-wrap { display: flex; align-items: center; gap: 1.375rem; margin-bottom: 1.25rem; }
.sadb-ring-legend { flex: 1; display: flex; flex-direction: column; gap: .5rem; }
.sadb-ring-item { display: flex; align-items: center; gap: .5rem; font-size: .75rem; }
.sadb-ring-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.sadb-ring-lbl { flex: 1; color: var(--c-text); }
.sadb-ring-val { font-weight: 700; color: var(--c-navy); }

.sadb-sbars { display: flex; flex-direction: column; gap: .625rem; }
.sadb-sbar-row { display: flex; align-items: center; gap: .75rem; }
.sadb-sbar-lbl { font-size: .7rem; font-weight: 600; color: var(--c-text); width: 86px; flex-shrink: 0; }
.sadb-sbar-track { flex: 1; height: 6px; background: var(--c-bg); border-radius: 999px; overflow: hidden; }
.sadb-sbar-fill  { height: 100%; border-radius: 999px; }
.sadb-sbar-cnt { font-size: .7rem; font-weight: 700; color: var(--c-navy); width: 24px; text-align: right; flex-shrink: 0; }

/* ── User rows ── */
.sadb-user-row {
  display: flex; align-items: center; gap: .75rem;
  padding: .75rem 0; border-bottom: 1px solid var(--c-border);
}
.sadb-user-row:last-child { border-bottom: 0; }
.sadb-user-av {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: .8rem;
}
.sadb-user-info { flex: 1; min-width: 0; }
.sadb-user-name  { font-size: .8rem; font-weight: 600; color: var(--c-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sadb-user-email { font-size: .68rem; color: var(--c-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sadb-user-since { font-size: .65rem; color: var(--c-muted); flex-shrink: 0; white-space: nowrap; }

/* ── Table card ── */
.sadb-table-card {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  overflow: hidden;
}
.sadb-table-hdr {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.25rem; border-bottom: 1px solid var(--c-border);
}

/* ── Empty ── */
.sadb-empty { padding: 2.5rem; text-align: center; }
.sadb-empty i { font-size: 2rem; color: var(--c-muted); opacity: .25; display: block; margin-bottom: .75rem; }
.sadb-empty p { font-size: .8rem; color: var(--c-muted); }

@media(max-width:575px) { .sadb-hero-stats { display: none; } }

/* ─────────────────────────────────────────
   RESPONSIVE MOBILE — sadb-
   ─────────────────────────────────────────*/
@media(max-width:768px) {
  .sadb-hero { padding: 1.25rem 1.25rem; gap: 1rem; }
  .sadb-hero-title { font-size: 1.125rem; }
}
/* Mode carte pro-table démarre à 640px — table-card doit laisser passer les coins arrondis */
@media(max-width:640px) {
  .sadb-table-card { overflow: visible; }
}
@media(max-width:575px) {
  /* Donut : empilé */
  .sadb-ring-wrap { flex-direction: column; align-items: center; gap: .75rem; }
  .sadb-ring-legend { width: 100%; }
  /* En-têtes cartes */
  .sadb-card-hdr { flex-wrap: wrap; gap: .5rem; }
  .sadb-table-hdr { flex-wrap: wrap; gap: .5rem; }
  /* Ligne utilisateur : wrap sur 320px */
  .sadb-user-row { flex-wrap: wrap; gap: .375rem; }
  .sadb-user-info { flex: 1 1 calc(100% - 52px); min-width: 0; }
  .sadb-user-since { margin-left: auto; }
  /* KPI sub-badges */
  .sadb-kpi-sub { gap: .25rem; }
}
@media(max-width:400px) {
  .sadb-kpi-grid { gap: .625rem; }
  .sadb-kpi { padding: .875rem 1rem; }
  .sadb-kpi-val { font-size: 1.625rem; }
  .sadb-kpi-top { flex-wrap: wrap; gap: .375rem; }
}

/* ── Bouton installation PWA ── */
.sadb-pwa-btn {
  display: none;
  align-items: center; gap: .4rem;
  margin-top: .625rem;
  padding: .375rem .875rem;
  border: 1px solid rgba(198,161,91,.35);
  border-radius: 8px;
  background: rgba(198,161,91,.1);
  color: var(--c-accent);
  font-size: .7rem; font-weight: 600;
  cursor: pointer; font-family: inherit;
  transition: background .2s, border-color .2s;
}
.sadb-pwa-btn:hover { background: rgba(198,161,91,.22); border-color: rgba(198,161,91,.6); }
.sadb-pwa-btn i { font-size: .65rem; }
</style>
@endpush

@section('content')
@php
  $total = max($stats['total_loans'], 1);

  /* Map actual statuses for display */
  $statuses = [
    ['lbl' => 'En attente',  'key' => 'pending_loans',   'color' => '#D97706'],
    ['lbl' => 'Approuvés',   'key' => 'approved_loans',  'color' => '#059669'],
    ['lbl' => 'En révision', 'key' => 'review_loans',    'color' => '#2563EB'],
    ['lbl' => 'Refusés',     'key' => 'rejected_loans',  'color' => '#DC2626'],
  ];

  $cx = 60; $cy = 60; $r = 46; $circ = 2 * 3.14159 * $r;
  $offset = 0; $svgSegs = [];
  foreach ($statuses as $s) {
    $v   = $stats[$s['key']] ?? 0;
    $len = $v > 0 ? ($v / $total) * $circ : 0;
    $svgSegs[] = array_merge($s, [
      'val'   => $v,
      'pct'   => $total > 0 ? round($v / $total * 100) : 0,
      'dash'  => $len,
      'dashoffset' => $circ - $offset,
    ]);
    $offset += $len;
  }

  $avatarPalette = ['#2563EB','#059669','#D97706','#7C3AED','#DC2626','#0D9488','#C6A15B'];

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

{{-- ── HERO ── --}}
<div class="sadb-hero">
  <div class="sadb-hero-left">
    <div class="sadb-hero-tag">
      <i class="fas fa-shield-alt" style="font-size:.6rem"></i>
      Super Administration
    </div>
    <div class="sadb-hero-title">Panneau de contrôle</div>
    <div class="sadb-hero-sub">{{ now()->isoFormat('dddd D MMMM YYYY') }}</div>
    <button id="sadb-pwa-btn" class="sadb-pwa-btn" aria-label="Installer l'application">
      <i class="fas fa-download"></i> Installer l'application
    </button>
    <div class="sadb-hero-users">
      <span class="sadb-user-chip" style="background:rgba(37,99,235,.15);color:#93c5fd">
        <span class="sadb-user-chip-dot" style="background:#2563EB"></span>
        {{ $stats['total_clients'] }} clients
      </span>
      <span class="sadb-user-chip" style="background:rgba(124,58,237,.15);color:#c4b5fd">
        <span class="sadb-user-chip-dot" style="background:#7C3AED"></span>
        {{ $stats['total_staff'] }} staff
      </span>
      <span class="sadb-user-chip" style="background:rgba(198,161,91,.15);color:var(--c-accent)">
        <span class="sadb-user-chip-dot" style="background:var(--c-accent)"></span>
        {{ $stats['total_users'] }} total
      </span>
    </div>
  </div>

  <div class="sadb-hero-stats">
    <div class="sadb-hero-stat">
      <span class="sadb-hero-stat-val">{{ $stats['total_loans'] }}</span>
      <span class="sadb-hero-stat-lbl">Dossiers</span>
    </div>
    <div class="sadb-hero-stat">
      <span class="sadb-hero-stat-val">{{ $stats['month_loans'] }}</span>
      <span class="sadb-hero-stat-lbl">Ce mois</span>
    </div>
    <div class="sadb-hero-stat">
      <span class="sadb-hero-stat-val">
        {{ $stats['total_amount'] > 0 ? number_format($stats['total_amount'] / 1000, 0, ',', ' ') . 'k' : '0' }}€
      </span>
      <span class="sadb-hero-stat-lbl">Volume</span>
    </div>
  </div>
</div>

{{-- ── KPI CARDS ── --}}
<div class="sadb-kpi-grid">

  <div class="sadb-kpi">
    <div class="sadb-kpi-top">
      <div class="sadb-kpi-ico sadb-ico-navy"><i class="fas fa-users"></i></div>
    </div>
    <div class="sadb-kpi-val">{{ $stats['total_users'] }}</div>
    <div class="sadb-kpi-lbl">Utilisateurs totaux</div>
    <div class="sadb-kpi-sub">
      <span class="badge-status bs-blue">{{ $stats['total_clients'] }} clients</span>
      <span class="badge-status bs-violet">{{ $stats['total_staff'] }} staff</span>
    </div>
    <div class="sadb-kpi-bar" style="background:var(--c-navy)"></div>
  </div>

  <div class="sadb-kpi">
    <div class="sadb-kpi-top">
      <div class="sadb-kpi-ico sadb-ico-accent"><i class="fas fa-file-invoice-dollar"></i></div>
      <span style="font-size:.65rem;font-weight:700;background:rgba(198,161,91,.1);color:#a07d20;padding:.18rem .45rem;border-radius:999px">
        {{ $stats['month_loans'] }} ce mois
      </span>
    </div>
    <div class="sadb-kpi-val">{{ $stats['total_loans'] }}</div>
    <div class="sadb-kpi-lbl">Dossiers de prêt</div>
    <div class="sadb-kpi-bar" style="background:var(--c-accent)"></div>
  </div>

  <div class="sadb-kpi">
    <div class="sadb-kpi-top">
      <div class="sadb-kpi-ico sadb-ico-amber"><i class="fas fa-hourglass-half"></i></div>
      @if(($stats['pending_loans'] ?? 0) > 0)
      <span style="font-size:.65rem;font-weight:700;background:#FEF3C7;color:#D97706;padding:.18rem .45rem;border-radius:999px">
        <i class="fas fa-exclamation-triangle" style="font-size:.5rem"></i> Urgent
      </span>
      @endif
    </div>
    <div class="sadb-kpi-val">{{ $stats['pending_loans'] ?? 0 }}</div>
    <div class="sadb-kpi-lbl">En attente de traitement</div>
    <div class="sadb-kpi-bar" style="background:#D97706"></div>
  </div>

  <div class="sadb-kpi">
    <div class="sadb-kpi-top">
      <div class="sadb-kpi-ico sadb-ico-green"><i class="fas fa-check-double"></i></div>
      <span style="font-size:.65rem;font-weight:700;background:rgba(5,150,105,.1);color:#059669;padding:.18rem .45rem;border-radius:999px">
        {{ $total > 0 ? round(($stats['approved_loans'] ?? 0) / $total * 100) : 0 }}% du total
      </span>
    </div>
    <div class="sadb-kpi-val">{{ $stats['approved_loans'] ?? 0 }}</div>
    <div class="sadb-kpi-lbl">Approuvés / Finalisés</div>
    <div class="sadb-kpi-bar" style="background:#059669"></div>
  </div>

</div>

{{-- ── MIDDLE ROW ── --}}
<div class="sadb-mid">

  {{-- Répartition statuts --}}
  <div class="sadb-card">
    <div class="sadb-card-hdr">
      <div class="sadb-card-title">
        <div class="sadb-card-title-dot" style="background:var(--c-navy)"></div>
        Répartition des demandes
      </div>
      <span style="font-size:.7rem;font-weight:700;color:var(--c-muted)">{{ $stats['total_loans'] }} total</span>
    </div>
    <div class="sadb-card-body">
      @if($stats['total_loans'] === 0)
      <div class="sadb-empty">
        <i class="fas fa-inbox"></i>
        <p>Aucune demande enregistrée</p>
      </div>
      @else
      <div class="sadb-ring-wrap">
        <svg width="110" height="110" viewBox="0 0 120 120" style="flex-shrink:0">
          <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="var(--c-bg)" stroke-width="14"/>
          @foreach($svgSegs as $seg)
          @if($seg['val'] > 0)
          <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
            stroke="{{ $seg['color'] }}" stroke-width="14"
            stroke-dasharray="{{ $seg['dash'] }} {{ $circ - $seg['dash'] }}"
            stroke-dashoffset="{{ $seg['dashoffset'] }}"
            stroke-linecap="butt"
            transform="rotate(-90 {{ $cx }} {{ $cy }})"/>
          @endif
          @endforeach
          <text x="{{ $cx }}" y="{{ $cy - 3 }}" text-anchor="middle" font-size="15" font-weight="800" fill="#0E3B2E">{{ $stats['total_loans'] }}</text>
          <text x="{{ $cx }}" y="{{ $cy + 12 }}" text-anchor="middle" font-size="7.5" fill="#9CA3AF">dossiers</text>
        </svg>
        <div class="sadb-ring-legend">
          @foreach($svgSegs as $seg)
          <div class="sadb-ring-item">
            <div class="sadb-ring-dot" style="background:{{ $seg['color'] }}"></div>
            <span class="sadb-ring-lbl">{{ $seg['lbl'] }}</span>
            <span class="sadb-ring-val">{{ $seg['val'] }}</span>
          </div>
          @endforeach
        </div>
      </div>
      <div class="sadb-sbars">
        @foreach($svgSegs as $seg)
        <div class="sadb-sbar-row">
          <span class="sadb-sbar-lbl">{{ $seg['lbl'] }}</span>
          <div class="sadb-sbar-track">
            <div class="sadb-sbar-fill" style="width:{{ $seg['pct'] }}%;background:{{ $seg['color'] }}"></div>
          </div>
          <span class="sadb-sbar-cnt">{{ $seg['val'] }}</span>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </div>

  {{-- Derniers utilisateurs --}}
  <div class="sadb-card">
    <div class="sadb-card-hdr">
      <div class="sadb-card-title">
        <div class="sadb-card-title-dot" style="background:#7C3AED"></div>
        Derniers inscrits
      </div>
      <a href="{{ route('admin.users') }}" class="sadb-view-all">
        Voir tout <i class="fas fa-arrow-right" style="font-size:.55rem"></i>
      </a>
    </div>
    <div class="sadb-card-body" style="padding:.75rem 1.25rem">
      @forelse($recentUsers as $u)
      @php
        $bg = $avatarPalette[crc32($u->email) % count($avatarPalette)];
        $rn = $u->getRoleNames()->first();
        $rc = ['super-admin' => ['lbl' => 'Super Admin', 'cls' => 'bs-dark'],
               'admin'       => ['lbl' => 'Admin',       'cls' => 'bs-amber'],
               'client'      => ['lbl' => 'Client',      'cls' => 'bs-blue']][$rn] ?? ['lbl' => '—', 'cls' => 'bs-gray'];
      @endphp
      <div class="sadb-user-row">
        <div class="sadb-user-av" style="background:{{ $bg }}18;color:{{ $bg }}">
          {{ strtoupper(mb_substr($u->name, 0, 1)) }}
        </div>
        <div class="sadb-user-info">
          <div class="sadb-user-name">{{ $u->name }}</div>
          <div class="sadb-user-email">{{ $u->email }}</div>
        </div>
        <span class="badge-status {{ $rc['cls'] }}" style="margin-right:.375rem">{{ $rc['lbl'] }}</span>
        <span class="sadb-user-since">{{ $u->created_at->diffForHumans(null, true) }}</span>
      </div>
      @empty
      <div class="sadb-empty">
        <i class="fas fa-users"></i>
        <p>Aucun utilisateur</p>
      </div>
      @endforelse
    </div>
  </div>

</div>

{{-- ── DERNIÈRES DEMANDES ── --}}
<div class="sadb-table-card">
  <div class="sadb-table-hdr">
    <div class="sadb-card-title">
      <div class="sadb-card-title-dot" style="background:var(--c-accent)"></div>
      Dernières demandes de prêt
    </div>
    <a href="{{ route('admin.loans.index') }}" class="sadb-view-all">
      Voir toutes <i class="fas fa-arrow-right" style="font-size:.55rem"></i>
    </a>
  </div>
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          <th>Admin</th>
          <th>Montant</th>
          <th>Statut</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentLoans as $loan)
        @php
          $stInfo     = $stMap[$loan->status] ?? ['lbl' => ucfirst($loan->status ?? '—'), 'cls' => 'bs-gray'];
          $clientName = $loan->client?->name  ?? $loan->name  ?? '—';
          $adminName  = $loan->admin?->name   ?? '—';
        @endphp
        <tr>
          <td data-label="Référence" class="cell-mono" style="font-size:.75rem">
            {{ $loan->reference ?? '#'.$loan->id }}
          </td>
          <td data-label="Client">
            <div class="cell-name">{{ $clientName }}</div>
            <div class="cell-sub">{{ $loan->client?->email ?? $loan->email ?? '' }}</div>
          </td>
          <td data-label="Admin" style="font-size:.77rem;color:var(--c-muted)">{{ $adminName }}</td>
          <td data-label="Montant" style="font-weight:800;color:var(--c-navy);white-space:nowrap">
            {{ number_format($loan->amount ?? 0, 0, ',', ' ') }}&nbsp;{{ $loan->currency ?? '€' }}
          </td>
          <td data-label="Statut">
            <span class="badge-status {{ $stInfo['cls'] }}">{{ $stInfo['lbl'] }}</span>
          </td>
          <td data-label="Date" style="font-size:.72rem;color:var(--c-muted);white-space:nowrap">
            {{ $loan->created_at->format('d/m/Y') }}
          </td>
          <td data-label="">
            <a href="{{ route('admin.loans.show', $loan) }}" class="btn-icon btn-icon-primary" title="Voir">
              <i class="fas fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <div class="sadb-empty">
              <i class="fas fa-inbox"></i>
              <p>Aucune demande enregistrée</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
  const btn = document.getElementById('sadb-pwa-btn');
  if (!btn) return;
  let deferred = null;

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferred = e;
    btn.style.display = 'inline-flex';
  });

  btn.addEventListener('click', function () {
    if (!deferred) return;
    deferred.prompt();
    deferred.userChoice.then(function (result) {
      deferred = null;
      if (result.outcome === 'accepted') btn.style.display = 'none';
    });
  });

  window.addEventListener('appinstalled', function () {
    btn.style.display = 'none';
  });
})();
</script>
@endpush
