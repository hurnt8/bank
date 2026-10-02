@extends('layouts.dashboard')
@section('title', 'Vérifications KYC — ' . site_name())
@section('page_title', 'Vérifications d\'identité')

@section('content')

<div class="page-hdr-row">
  <div class="page-hdr">
    <h1>Vérifications d'identité (KYC)</h1>
    <p>Validation des documents d'identité soumis par les clients</p>
  </div>
</div>

<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-card__icon mi-amber"><i class="fas fa-hourglass-half"></i></div>
    <div class="metric-card__val" style="color:var(--c-amber)">{{ $stats['en_attente'] }}</div>
    <div class="metric-card__lbl">En attente</div>
    <div class="metric-card__accent" style="background:var(--c-amber)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-check-circle"></i></div>
    <div class="metric-card__val" style="color:var(--c-green)">{{ $stats['approuve'] }}</div>
    <div class="metric-card__lbl">Approuvés</div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-red"><i class="fas fa-times-circle"></i></div>
    <div class="metric-card__val" style="color:var(--c-red)">{{ $stats['rejete'] }}</div>
    <div class="metric-card__lbl">Rejetés</div>
    <div class="metric-card__accent" style="background:var(--c-red)"></div>
  </div>
</div>

<div class="card-pro" style="margin-bottom:1.25rem">
  <div class="card-pro-body" style="padding:.75rem 1.25rem">
    <form method="GET" style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:center">
      <select name="status" class="form-control-pro" style="width:auto;min-width:180px" onchange="this.form.submit()">
        <option value="">Toutes (hors non soumis)</option>
        <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
        <option value="approuve"   {{ request('status') === 'approuve'   ? 'selected' : '' }}>Approuvés</option>
        <option value="rejete"     {{ request('status') === 'rejete'     ? 'selected' : '' }}>Rejetés</option>
      </select>
      <button type="submit" class="btn-navy btn-sm-pro"><i class="fas fa-search"></i> Filtrer</button>
    </form>
  </div>
</div>

@if($verifications->isEmpty())
<div class="card-pro" style="text-align:center;padding:5rem 2rem">
  <i class="fas fa-id-card" style="font-size:2.5rem;color:var(--c-muted);opacity:.2;display:block;margin-bottom:1rem"></i>
  <p style="font-size:.9375rem;font-weight:600;color:var(--c-muted)">Aucune vérification à afficher.</p>
</div>
@else

<div style="display:flex;flex-direction:column;gap:1rem">
@foreach($verifications as $kyc)
@php
  $statusMap = [
    'en_attente' => ['cls'=>'bs-amber', 'icon'=>'hourglass-half', 'bar'=>'var(--c-amber)'],
    'approuve'   => ['cls'=>'bs-green', 'icon'=>'check',          'bar'=>'var(--c-green)'],
    'rejete'     => ['cls'=>'bs-red',   'icon'=>'times',          'bar'=>'var(--c-red)'],
  ];
  $sm = $statusMap[$kyc->status] ?? ['cls'=>'bs-gray','icon'=>'circle','bar'=>'var(--c-muted)'];
@endphp
<a href="{{ route('admin.kyc.show', $kyc) }}" class="card-pro" style="border-left:4px solid {{ $sm['bar'] }};display:block;text-decoration:none">
  <div style="display:grid;grid-template-columns:1fr auto auto;gap:1.25rem;align-items:center;padding:1rem 1.25rem">
    <div>
      <div class="cell-name">{{ $kyc->user->name }}</div>
      <div class="cell-sub">{{ $kyc->user->email }}</div>
    </div>
    <div style="text-align:right">
      <div class="cell-sub">Soumis le</div>
      <div style="font-size:.85rem;font-weight:600;color:var(--c-navy)">{{ $kyc->submitted_at?->format('d/m/Y · H:i') ?? '—' }}</div>
    </div>
    <div style="text-align:right">
      <span class="badge-status {{ $sm['cls'] }}">
        <i class="fas fa-{{ $sm['icon'] }}" style="font-size:.6rem"></i>
        {{ $kyc->statusLabel() }}
      </span>
    </div>
  </div>
</a>
@endforeach
</div>

<div style="margin-top:1.5rem">{{ $verifications->links() }}</div>

@endif

@endsection
