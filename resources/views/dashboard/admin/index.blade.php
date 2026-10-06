@extends('layouts.dashboard')
@section('title', 'Tableau de bord — ' . site_name())
@section('page_title', 'Vue d\'ensemble')

@push('styles')
<style>
/* ══════════════════════════════════════════
   ADMIN DASHBOARD — préfixe adb-
   ══════════════════════════════════════════ */

/* ── Hero ── */
.adb-hero {
  background: linear-gradient(135deg, #0E3B2E 0%, #14503D 60%, #14503D 100%);
  border-radius: 16px;
  padding: 1.875rem 2rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
  position: relative;
  overflow: hidden;
}
.adb-hero::before {
  content: '';
  position: absolute; top: -50px; right: -50px;
  width: 240px; height: 240px; border-radius: 50%;
  background: rgba(198,161,91,.06); pointer-events: none;
}
.adb-hero::after {
  content: '';
  position: absolute; bottom: -70px; right: 100px;
  width: 180px; height: 180px; border-radius: 50%;
  background: rgba(198,161,91,.04); pointer-events: none;
}
.adb-hero-left { position: relative; z-index: 1; }
.adb-hero-tag {
  font-size: .62rem; font-weight: 700; letter-spacing: .12em;
  text-transform: uppercase; color: var(--c-accent);
  display: flex; align-items: center; gap: .4rem; margin-bottom: .4rem;
}
.adb-hero-tag::before {
  content: '';
  width: 18px; height: 2px; border-radius: 1px; background: var(--c-accent);
}
.adb-hero-title { font-size: 1.375rem; font-weight: 900; color: #fff; line-height: 1.2; margin-bottom: .25rem; }
.adb-hero-sub   { font-size: .78rem; color: rgba(255,255,255,.4); }

.adb-hero-kpis {
  display: flex; align-items: center; gap: 0;
  position: relative; z-index: 1;
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 14px; overflow: hidden;
}
.adb-hero-kpi {
  padding: 1rem 1.5rem; text-align: center; position: relative;
}
.adb-hero-kpi + .adb-hero-kpi {
  border-left: 1px solid rgba(255,255,255,.1);
}
.adb-hero-kpi-val { display: block; font-size: 1.5rem; font-weight: 900; color: var(--c-accent); line-height: 1; }
.adb-hero-kpi-lbl { display: block; font-size: .62rem; color: rgba(255,255,255,.4); margin-top: .3rem; white-space: nowrap; }

/* ── Alert banner ── */
.adb-alert {
  display: flex; align-items: center; gap: .875rem;
  padding: .875rem 1.125rem; border-radius: 12px;
  background: #FEF3C7; border: 1.5px solid #FDE68A;
  margin-bottom: 1.25rem;
}
.adb-alert-ico {
  width: 36px; height: 36px; border-radius: 9px;
  background: #D97706; color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: .875rem; flex-shrink: 0;
}
.adb-alert-body { flex: 1; }
.adb-alert-title { font-size: .8rem; font-weight: 700; color: #92400E; }
.adb-alert-sub   { font-size: .72rem; color: #B45309; margin-top: .1rem; }
.adb-alert-btn {
  padding: .4rem .875rem; border-radius: 8px;
  background: #D97706; color: #fff;
  font-size: .72rem; font-weight: 700;
  text-decoration: none; white-space: nowrap; transition: .15s;
  flex-shrink: 0;
}
.adb-alert-btn:hover { background: #B45309; color: #fff; }

/* ── KPI cards ── */
.adb-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media(max-width:991px) { .adb-kpi-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:575px) { .adb-kpi-grid { grid-template-columns: 1fr 1fr; } }

.adb-kpi {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  padding: 1.25rem 1.375rem;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  position: relative; overflow: hidden;
  transition: box-shadow .15s, transform .15s;
}
.adb-kpi:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateY(-2px); }
.adb-kpi-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: .75rem; }
.adb-kpi-ico {
  width: 44px; height: 44px; border-radius: 11px;
  display: flex; align-items: center; justify-content: center; font-size: .9375rem;
}
.adb-kpi-change {
  font-size: .65rem; font-weight: 700; padding: .2rem .5rem;
  border-radius: 999px; white-space: nowrap;
}
.adb-kpi-val  { font-size: 2rem; font-weight: 900; color: var(--c-navy); line-height: 1; margin-bottom: .25rem; }
.adb-kpi-lbl  { font-size: .72rem; color: var(--c-muted); font-weight: 500; }
.adb-kpi-bar  { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 14px 14px; }

/* Colors */
.adb-ico-navy { background: rgba(14,59,46,.08); color: var(--c-navy); }
.adb-ico-accent { background: rgba(198,161,91,.12); color: #a07d20; }
.adb-ico-amber{ background: rgba(217,119,6,.1);  color: #D97706; }
.adb-ico-green{ background: rgba(5,150,105,.1);  color: #059669; }
.adb-ico-red  { background: rgba(220,38,38,.1);  color: #DC2626; }

/* ── Middle row ── */
.adb-mid {
  display: grid;
  grid-template-columns: 5fr 7fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media(max-width:991px) { .adb-mid { grid-template-columns: 1fr; } }

/* ── Card shell ── */
.adb-card {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  display: flex; flex-direction: column;
}
.adb-card-hdr {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--c-border);
  flex-shrink: 0;
}
.adb-card-title {
  font-size: .825rem; font-weight: 800; color: var(--c-navy);
  display: flex; align-items: center; gap: .5rem;
}
.adb-card-title-dot {
  width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.adb-card-body { padding: 1.125rem 1.25rem; flex: 1; }
.adb-view-all {
  font-size: .7rem; font-weight: 700; color: var(--c-muted);
  text-decoration: none; display: flex; align-items: center; gap: .3rem; transition: color .15s;
}
.adb-view-all:hover { color: var(--c-navy); }

/* ── Donut ── */
.adb-ring-wrap { display: flex; align-items: center; gap: 1.375rem; margin-bottom: 1.25rem; }
.adb-ring-legend { flex: 1; display: flex; flex-direction: column; gap: .5rem; }
.adb-ring-item { display: flex; align-items: center; gap: .5rem; font-size: .75rem; }
.adb-ring-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.adb-ring-lbl { flex: 1; color: var(--c-text); }
.adb-ring-val { font-weight: 700; color: var(--c-navy); }

/* Status bars */
.adb-sbars { display: flex; flex-direction: column; gap: .625rem; }
.adb-sbar-row { display: flex; align-items: center; gap: .75rem; }
.adb-sbar-lbl { font-size: .7rem; font-weight: 600; color: var(--c-text); width: 80px; flex-shrink: 0; }
.adb-sbar-track { flex: 1; height: 6px; background: var(--c-bg); border-radius: 999px; overflow: hidden; }
.adb-sbar-fill  { height: 100%; border-radius: 999px; }
.adb-sbar-cnt { font-size: .7rem; font-weight: 700; color: var(--c-navy); width: 24px; text-align: right; flex-shrink: 0; }

/* ── Clients list ── */
.adb-client-row {
  display: flex; align-items: center; gap: .75rem;
  padding: .75rem 0; border-bottom: 1px solid var(--c-border);
}
.adb-client-row:last-child { border-bottom: 0; }
.adb-client-av {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: .8rem;
}
.adb-client-info { flex: 1; min-width: 0; }
.adb-client-name { font-size: .8rem; font-weight: 600; color: var(--c-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.adb-client-email{ font-size: .68rem; color: var(--c-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.adb-client-since{ font-size: .65rem; color: var(--c-muted); flex-shrink: 0; white-space: nowrap; }

/* ── Loans table ── */
.adb-table-card {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
  overflow: hidden;
}
.adb-table-hdr {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--c-border);
}

/* Empty state */
.adb-empty {
  padding: 3rem; text-align: center;
}
.adb-empty i { font-size: 2rem; color: var(--c-muted); opacity: .25; display: block; margin-bottom: .75rem; }
.adb-empty p { font-size: .8rem; color: var(--c-muted); }

@media(max-width:575px) { .adb-hero-kpis { display: none; } }

/* ─────────────────────────────────────────
   RESPONSIVE MOBILE — adb-
   ─────────────────────────────────────────*/
@media(max-width:768px) {
  .adb-hero { padding: 1.25rem 1.25rem; gap: 1rem; }
  .adb-hero-title { font-size: 1.125rem; }
}
/* Mode carte pro-table démarre à 640px — table-card doit laisser passer les coins arrondis */
@media(max-width:640px) {
  .adb-table-card { overflow: visible; }
}
@media(max-width:575px) {
  /* Alert : bouton pleine largeur si trop étroit */
  .adb-alert { flex-wrap: wrap; gap: .625rem; }
  .adb-alert-btn {
    width: 100%; justify-content: center;
    display: flex; align-items: center; gap: .35rem;
    text-align: center;
  }
  /* Donut : empilé verticalement */
  .adb-ring-wrap { flex-direction: column; align-items: center; gap: .75rem; }
  .adb-ring-legend { width: 100%; }
  /* En-têtes de cartes */
  .adb-card-hdr { flex-wrap: wrap; gap: .5rem; }
  .adb-table-hdr { flex-wrap: wrap; gap: .5rem; }
}
@media(max-width:400px) {
  .adb-kpi-grid { gap: .625rem; }
  .adb-kpi { padding: .875rem 1rem; }
  .adb-kpi-val { font-size: 1.5rem; }
  .adb-kpi-top { flex-wrap: wrap; gap: .375rem; }
}

/* ── Bouton installation PWA ── */
.adb-pwa-btn {
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
.adb-pwa-btn:hover { background: rgba(198,161,91,.22); border-color: rgba(198,161,91,.6); }
.adb-pwa-btn i { font-size: .65rem; }
</style>
@endpush

@section('content')
@php
  $total   = max($stats['total_loans'], 1);
  $pct_p   = round($stats['pending_loans']   / $total * 100);
  $pct_a   = round($stats['active_loans']    / $total * 100);
  $pct_f   = round($stats['finalized_loans'] / $total * 100);
  $pct_r   = round($stats['rejected_loans']  / $total * 100);

  $segments = [
    ['lbl' => 'En attente', 'val' => $stats['pending_loans'],   'pct' => $pct_p, 'color' => '#D97706'],
    ['lbl' => 'Actifs',     'val' => $stats['active_loans'],    'pct' => $pct_a, 'color' => '#2563EB'],
    ['lbl' => 'Finalisés',  'val' => $stats['finalized_loans'], 'pct' => $pct_f, 'color' => '#059669'],
    ['lbl' => 'Refusés',    'val' => $stats['rejected_loans'],  'pct' => $pct_r, 'color' => '#DC2626'],
  ];

  $cx = 60; $cy = 60; $r = 46; $circ = 2 * 3.14159 * $r;
  $offset = 0; $svgSegs = [];
  foreach ($segments as $seg) {
    $len = $stats['total_loans'] > 0 ? ($seg['val'] / $total) * $circ : 0;
    $svgSegs[] = array_merge($seg, ['dash' => $len, 'dashoffset' => $circ - $offset]);
    $offset += $len;
  }

  $avatarPalette = ['#2563EB','#059669','#D97706','#7C3AED','#DC2626','#0D9488','#C6A15B'];
@endphp

{{-- ── HERO ── --}}
<div class="adb-hero">
  <div class="adb-hero-left">
    <div class="adb-hero-tag">{{ site_name() }} — Espace Administrateur</div>
    <div class="adb-hero-title">Bonjour, {{ Auth::user()->name }} 👋</div>
    <div class="adb-hero-sub">{{ now()->isoFormat('dddd D MMMM YYYY') }}</div>
    <button id="adb-pwa-btn" class="adb-pwa-btn" aria-label="Installer l'application">
      <i class="fas fa-download"></i> Installer l'application
    </button>
  </div>
  <div class="adb-hero-kpis">
    <div class="adb-hero-kpi">
      <span class="adb-hero-kpi-val">{{ $stats['my_clients'] }}</span>
      <span class="adb-hero-kpi-lbl">Mes clients</span>
    </div>
    <div class="adb-hero-kpi">
      <span class="adb-hero-kpi-val">{{ $stats['month_loans'] }}</span>
      <span class="adb-hero-kpi-lbl">Dossiers ce mois</span>
    </div>
    <div class="adb-hero-kpi">
      <span class="adb-hero-kpi-val">
        {{ $stats['total_amount'] > 0 ? number_format($stats['total_amount'] / 1000, 0, ',', ' ') . 'k' : '0' }}€
      </span>
      <span class="adb-hero-kpi-lbl">Volume total</span>
    </div>
  </div>
</div>

{{-- ── ALERT PENDING ── --}}
@if($stats['pending_loans'] > 0)
<div class="adb-alert">
  <div class="adb-alert-ico"><i class="fas fa-exclamation-triangle"></i></div>
  <div class="adb-alert-body">
    <div class="adb-alert-title">
      {{ $stats['pending_loans'] }} dossier{{ $stats['pending_loans'] > 1 ? 's' : '' }} en attente de traitement
    </div>
    <div class="adb-alert-sub">Ces demandes nécessitent une action de votre part.</div>
  </div>
  <a href="{{ route('admin.loans.index', ['status' => 'pending']) }}" class="adb-alert-btn">
    Traiter <i class="fas fa-arrow-right" style="font-size:.6rem"></i>
  </a>
</div>
@endif

{{-- ── KPI CARDS ── --}}
<div class="adb-kpi-grid">

  <div class="adb-kpi">
    <div class="adb-kpi-top">
      <div class="adb-kpi-ico adb-ico-navy"><i class="fas fa-users"></i></div>
      <span class="adb-kpi-change" style="background:rgba(37,99,235,.08);color:#2563EB">
        Portefeuille
      </span>
    </div>
    <div class="adb-kpi-val">{{ $stats['my_clients'] }}</div>
    <div class="adb-kpi-lbl">Mes clients</div>
    <div class="adb-kpi-bar" style="background:var(--c-navy)"></div>
  </div>

  <div class="adb-kpi">
    <div class="adb-kpi-top">
      <div class="adb-kpi-ico adb-ico-accent"><i class="fas fa-file-invoice-dollar"></i></div>
      <span class="adb-kpi-change" style="background:rgba(198,161,91,.12);color:#a07d20">
        {{ $stats['month_loans'] }} ce mois
      </span>
    </div>
    <div class="adb-kpi-val">{{ $stats['total_loans'] }}</div>
    <div class="adb-kpi-lbl">Mes dossiers</div>
    <div class="adb-kpi-bar" style="background:var(--c-accent)"></div>
  </div>

  <div class="adb-kpi">
    <div class="adb-kpi-top">
      <div class="adb-kpi-ico adb-ico-amber"><i class="fas fa-hourglass-half"></i></div>
      @if($stats['pending_loans'] > 0)
      <span class="adb-kpi-change" style="background:#FEF3C7;color:#D97706">
        <i class="fas fa-exclamation-triangle" style="font-size:.5rem"></i> Urgent
      </span>
      @else
      <span class="adb-kpi-change" style="background:rgba(5,150,105,.1);color:#059669">
        <i class="fas fa-check" style="font-size:.5rem"></i> File vide
      </span>
      @endif
    </div>
    <div class="adb-kpi-val">{{ $stats['pending_loans'] }}</div>
    <div class="adb-kpi-lbl">En attente</div>
    <div class="adb-kpi-bar" style="background:#D97706"></div>
  </div>

  <div class="adb-kpi">
    <div class="adb-kpi-top">
      <div class="adb-kpi-ico adb-ico-green"><i class="fas fa-check-circle"></i></div>
      <span class="adb-kpi-change" style="background:rgba(5,150,105,.1);color:#059669">
        {{ $pct_f }}% du total
      </span>
    </div>
    <div class="adb-kpi-val">{{ $stats['finalized_loans'] }}</div>
    <div class="adb-kpi-lbl">Finalisés</div>
    <div class="adb-kpi-bar" style="background:#059669"></div>
  </div>

</div>

{{-- ── MIDDLE ROW ── --}}
<div class="adb-mid">

  {{-- Répartition --}}
  <div class="adb-card">
    <div class="adb-card-hdr">
      <div class="adb-card-title">
        <div class="adb-card-title-dot" style="background:var(--c-navy)"></div>
        Répartition des dossiers
      </div>
      <span style="font-size:.7rem;font-weight:700;color:var(--c-muted)">{{ $stats['total_loans'] }} total</span>
    </div>
    <div class="adb-card-body">
      @if($stats['total_loans'] === 0)
      <div class="adb-empty">
        <i class="fas fa-inbox"></i>
        <p>Aucun dossier assigné</p>
      </div>
      @else
      <div class="adb-ring-wrap">
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
        <div class="adb-ring-legend">
          @foreach($segments as $seg)
          <div class="adb-ring-item">
            <div class="adb-ring-dot" style="background:{{ $seg['color'] }}"></div>
            <span class="adb-ring-lbl">{{ $seg['lbl'] }}</span>
            <span class="adb-ring-val">{{ $seg['val'] }}</span>
          </div>
          @endforeach
        </div>
      </div>
      <div class="adb-sbars">
        @foreach($segments as $seg)
        <div class="adb-sbar-row">
          <span class="adb-sbar-lbl">{{ $seg['lbl'] }}</span>
          <div class="adb-sbar-track">
            <div class="adb-sbar-fill" style="width:{{ $seg['pct'] }}%;background:{{ $seg['color'] }}"></div>
          </div>
          <span class="adb-sbar-cnt">{{ $seg['val'] }}</span>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </div>

  {{-- Mes clients récents --}}
  <div class="adb-card">
    <div class="adb-card-hdr">
      <div class="adb-card-title">
        <div class="adb-card-title-dot" style="background:#2563EB"></div>
        Mes clients récents
      </div>
      <a href="{{ route('admin.users') }}" class="adb-view-all">
        Voir tout <i class="fas fa-arrow-right" style="font-size:.55rem"></i>
      </a>
    </div>
    <div class="adb-card-body" style="padding:.75rem 1.25rem">
      @forelse($recentClients as $client)
      @php $bg = $avatarPalette[crc32($client->email) % count($avatarPalette)]; @endphp
      <div class="adb-client-row">
        <div class="adb-client-av" style="background:{{ $bg }}18;color:{{ $bg }}">
          {{ strtoupper(mb_substr($client->name, 0, 1)) }}
        </div>
        <div class="adb-client-info">
          <div class="adb-client-name">{{ $client->name }}</div>
          <div class="adb-client-email">{{ $client->email }}</div>
        </div>
        <span class="adb-client-since">{{ $client->created_at->diffForHumans(null, true) }}</span>
      </div>
      @empty
      <div class="adb-empty">
        <i class="fas fa-users"></i>
        <p>Aucun client assigné</p>
      </div>
      @endforelse
    </div>
  </div>

</div>

{{-- ── DERNIERS DOSSIERS ── --}}
<div class="adb-table-card">
  <div class="adb-table-hdr">
    <div class="adb-card-title">
      <div class="adb-card-title-dot" style="background:var(--c-accent)"></div>
      Mes derniers dossiers
    </div>
    <a href="{{ route('admin.loans.index') }}" class="adb-view-all">
      Voir tous <i class="fas fa-arrow-right" style="font-size:.55rem"></i>
    </a>
  </div>
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          <th>Montant</th>
          <th>Objet</th>
          <th>Statut</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @php
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
        @forelse($recentLoans as $loan)
        @php
          $stInfo = $stMap[$loan->status] ?? ['lbl' => ucfirst($loan->status ?? '—'), 'cls' => 'bs-gray'];
          $clientName  = $loan->client?->name  ?? $loan->name  ?? '—';
          $clientEmail = $loan->client?->email ?? $loan->email ?? '';
        @endphp
        <tr>
          <td data-label="Référence" class="cell-mono" style="font-size:.75rem">
            {{ $loan->reference ?? '#'.$loan->id }}
          </td>
          <td data-label="Client">
            @if($loan->client_id)
            <a href="{{ route('admin.users.show', $loan->client) }}" style="text-decoration:none">
              <div class="cell-name" style="color:var(--c-navy)">{{ $clientName }}</div>
            </a>
            @else
            <div class="cell-name">{{ $clientName }}</div>
            @endif
            <div class="cell-sub">{{ $clientEmail }}</div>
          </td>
          <td data-label="Montant" style="font-weight:800;color:var(--c-navy);white-space:nowrap">
            {{ number_format($loan->amount ?? 0, 0, ',', ' ') }}&nbsp;{{ $loan->currency ?? '€' }}
          </td>
          <td data-label="Objet" style="max-width:150px;font-size:.77rem;color:var(--c-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ Str::limit($loan->objet ?? '—', 25) }}
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
            <div class="adb-empty">
              <i class="fas fa-inbox"></i>
              <p>Aucun dossier assigné</p>
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
  const btn = document.getElementById('adb-pwa-btn');
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
