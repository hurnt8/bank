@extends('layouts.dashboard')
@section('title', 'Transferts — ' . site_name())
@section('page_title', 'Transferts clients')

@section('content')

{{-- Page header ── --}}
<div class="page-hdr-row">
  <div class="page-hdr">
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
      <h1>Transferts clients</h1>
      @if($isSuperAdmin)
        <span class="badge-status bs-violet"><i class="fas fa-shield-alt" style="font-size:.6rem"></i> Vue globale</span>
      @endif
    </div>
    <p>Validation et suivi des virements soumis par les clients</p>
  </div>
</div>

{{-- KPI ── --}}
<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-card__icon mi-amber"><i class="fas fa-hourglass-half"></i></div>
    <div class="metric-card__val" style="color:var(--c-amber)">{{ $stats['pending'] }}</div>
    <div class="metric-card__lbl">En attente</div>
    <div class="metric-card__accent" style="background:var(--c-amber)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-blue"><i class="fas fa-file-invoice"></i></div>
    <div class="metric-card__val" style="color:var(--c-blue)">{{ $stats['fee_required'] }}</div>
    <div class="metric-card__lbl">Frais requis</div>
    <div class="metric-card__accent" style="background:var(--c-blue)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-check-circle"></i></div>
    <div class="metric-card__val" style="color:var(--c-green)">{{ $stats['completed'] }}</div>
    <div class="metric-card__lbl">Validés</div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-red"><i class="fas fa-times-circle"></i></div>
    <div class="metric-card__val" style="color:var(--c-red)">{{ $stats['rejected'] }}</div>
    <div class="metric-card__lbl">Rejetés</div>
    <div class="metric-card__accent" style="background:var(--c-red)"></div>
  </div>
</div>

{{-- Filters ── --}}
<div class="card-pro" style="margin-bottom:1.25rem">
  <div class="card-pro-body" style="padding:.75rem 1.25rem">
    <form method="GET" style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:center">
      <div style="position:relative;flex:1;min-width:200px">
        <i class="fas fa-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--c-muted);font-size:.75rem;pointer-events:none"></i>
        <input type="text" name="search" value="{{ request('search') }}"
          placeholder="Référence, client, bénéficiaire…" class="form-control-pro" style="padding-left:2.25rem">
      </div>
      <select name="status" class="form-control-pro" style="width:auto;min-width:180px" onchange="this.form.submit()">
        <option value="">En attente + frais</option>
        <option value="pending"       {{ request('status') === 'pending'       ? 'selected':'' }}>En attente</option>
        <option value="fee_required"  {{ request('status') === 'fee_required'  ? 'selected':'' }}>Frais requis</option>
        <option value="completed"     {{ request('status') === 'completed'     ? 'selected':'' }}>Validés</option>
        <option value="rejected"      {{ request('status') === 'rejected'      ? 'selected':'' }}>Rejetés</option>
      </select>
      <button type="submit" class="btn-navy btn-sm-pro"><i class="fas fa-search"></i> Filtrer</button>
      @if(request()->hasAny(['search','status']))
      <a href="{{ route('admin.transfers.index') }}" class="btn-ghost btn-sm-pro">
        <i class="fas fa-times"></i> Réinitialiser
      </a>
      @endif
    </form>
  </div>
</div>

{{-- Transfer list ── --}}
@if($transfers->isEmpty())
<div class="card-pro" style="text-align:center;padding:5rem 2rem">
  <i class="fas fa-exchange-alt" style="font-size:2.5rem;color:var(--c-muted);opacity:.2;display:block;margin-bottom:1rem"></i>
  <p style="font-size:.9375rem;font-weight:600;color:var(--c-muted)">Aucun transfert à afficher.</p>
  <p style="font-size:.8125rem;color:var(--c-muted);margin-top:.35rem">Modifiez les filtres ou attendez de nouveaux virements.</p>
</div>
@else

<div style="display:flex;flex-direction:column;gap:1rem">
@foreach($transfers as $trf)
@php
  $isPending = in_array($trf->status, [\App\Models\Transfer::STATUS_PENDING, \App\Models\Transfer::STATUS_FEE_REQUIRED]);
  $statusMap = [
    'pending'      => ['cls'=>'bs-amber',  'icon'=>'hourglass-half', 'bar'=>'var(--c-amber)'],
    'fee_required' => ['cls'=>'bs-blue',   'icon'=>'file-invoice',   'bar'=>'var(--c-blue)'],
    'completed'    => ['cls'=>'bs-green',  'icon'=>'check',          'bar'=>'var(--c-green)'],
    'rejected'     => ['cls'=>'bs-red',    'icon'=>'times',          'bar'=>'var(--c-red)'],
  ];
  $sm = $statusMap[$trf->status] ?? ['cls'=>'bs-gray','icon'=>'circle','bar'=>'var(--c-muted)'];
@endphp

<div class="card-pro" style="border-left:4px solid {{ $sm['bar'] }};{{ $trf->status === 'rejected' ? 'opacity:.8' : '' }}">

  {{-- Head ── --}}
  <div class="trf-head" style="display:grid;grid-template-columns:auto 1fr auto auto;gap:1.25rem;align-items:center;padding:1rem 1.25rem">

    {{-- Client ── --}}
    <div style="display:flex;align-items:center;gap:.75rem">
      <div style="width:40px;height:40px;border-radius:50%;flex-shrink:0;
        background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
        display:flex;align-items:center;justify-content:center;
        color:var(--c-accent);font-weight:800;font-size:.875rem">
        {{ strtoupper(substr($trf->user->name, 0, 1)) }}
      </div>
      <div>
        <div class="cell-name">{{ $trf->user->name }}</div>
        <div class="cell-sub">{{ $trf->user->email }}</div>
        <div style="font-family:monospace;font-size:.72rem;font-weight:700;color:var(--c-navy);margin-top:.15rem">{{ $trf->reference }}</div>
      </div>
    </div>

    {{-- Beneficiary ── --}}
    <div>
      <div style="font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);font-weight:700;margin-bottom:.2rem">Bénéficiaire</div>
      <div style="font-size:.875rem;font-weight:600;color:var(--c-navy)">{{ $trf->beneficiary_name ?? '—' }}</div>
      @if($trf->beneficiary_iban)
      <div class="cell-mono" style="font-size:.72rem">{{ $trf->beneficiary_iban }}</div>
      @endif
    </div>

    {{-- Amount ── --}}
    <div style="text-align:right">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:1.25rem;font-weight:900;color:var(--c-navy)">
        {{ number_format($trf->amount, 2, ',', ' ') }}
        <span style="font-size:.75rem;font-weight:600;color:var(--c-muted)">{{ $trf->currency }}</span>
      </div>
      <div class="cell-sub">{{ $trf->created_at->format('d/m/Y · H:i') }}</div>
    </div>

    {{-- Status ── --}}
    <div style="text-align:right">
      <span class="badge-status {{ $sm['cls'] }}">
        <i class="fas fa-{{ $sm['icon'] }}" style="font-size:.6rem"></i>
        {{ $trf->statusLabel() }}
      </span>
      @if($trf->invoice)
      <div class="cell-sub" style="margin-top:.35rem">Facture : {{ $trf->invoice->reference }}</div>
      @endif
    </div>
  </div>

  {{-- Notes ── --}}
  @if($trf->note || $trf->admin_note)
  <div style="padding:.625rem 1.25rem 1rem;border-top:1px solid var(--c-border);display:flex;gap:2rem;flex-wrap:wrap">
    @if($trf->note)
    <div>
      <div style="font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);font-weight:700;margin-bottom:.2rem">Note client</div>
      <div style="font-size:.8125rem;color:var(--c-text)">{{ $trf->note }}</div>
    </div>
    @endif
    @if($trf->admin_note)
    <div>
      <div style="font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);font-weight:700;margin-bottom:.2rem">Note admin</div>
      <div style="font-size:.8125rem;color:var(--c-text)">{{ $trf->admin_note }}
        @if($trf->admin)<span style="color:var(--c-muted)"> · {{ $trf->admin->name }}</span>@endif
      </div>
    </div>
    @endif
  </div>
  @endif

  {{-- Progression + code de déblocage (virements en attente) ── --}}
  @if($isPending && $trf->status === \App\Models\Transfer::STATUS_PENDING)
  <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border)">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:.45rem">
      <span style="font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);font-weight:700">Avancement du virement</span>
      <strong style="font-size:.85rem">{{ $trf->progressValue() }} %</strong>
    </div>
    <div style="height:8px;border-radius:999px;background:var(--c-border);overflow:hidden">
      <div style="height:100%;width:{{ $trf->progressValue() }}%;border-radius:999px;background:{{ $trf->code_required ? '#D97706' : 'var(--c-accent, #C6A15B)' }};transition:width .3s"></div>
    </div>
    @if($trf->code_required)
    <div style="margin-top:.75rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;padding:.65rem .9rem;border-radius:10px;background:#FEF3C7;border:1px solid #FDE68A">
      <i class="fas fa-lock" style="color:#D97706"></i>
      <div style="flex:1;min-width:180px;font-size:.78rem;color:#92400E;line-height:1.45">
        <strong>Bloqué à {{ $trf->progress }} %</strong> — le client doit saisir ce code, à lui communiquer vous-même :
      </div>
      <code style="font-size:1.25rem;font-weight:800;letter-spacing:.25em;color:#92400E;background:#fff;padding:.25rem .7rem;border-radius:8px;border:1px dashed #D97706">{{ $trf->unlock_code }}</code>
      <button type="button" class="btn-ghost btn-sm-pro" onclick="navigator.clipboard&&navigator.clipboard.writeText('{{ $trf->unlock_code }}');this.textContent='Copié'">Copier</button>
    </div>
    @elseif($trf->code_verified_at)
    <div style="margin-top:.6rem;font-size:.75rem;color:var(--c-green)"><i class="fas fa-circle-check"></i> Code saisi par le client le {{ $trf->code_verified_at->format('d/m/Y à H:i') }} — vous pouvez faire avancer le virement.</div>
    @endif
  </div>
  @endif

  {{-- Actions ── --}}
  @if($isPending)
  <div style="padding:.75rem 1.25rem;background:var(--c-bg);border-top:1px solid var(--c-border);display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
    <button type="button" class="btn-navy btn-sm-pro" style="background:var(--c-green)"
      onclick="toggleForm('approve-{{ $trf->id }}',{{ $trf->id }})">
      <i class="fas fa-check"></i> Valider
    </button>
    <button type="button" class="btn-navy btn-sm-pro" style="background:var(--c-red)"
      onclick="toggleForm('reject-{{ $trf->id }}',{{ $trf->id }})">
      <i class="fas fa-times"></i> Rejeter
    </button>
    @if($trf->status === \App\Models\Transfer::STATUS_PENDING)
    <button type="button" class="btn-ghost btn-sm-pro"
      onclick="toggleForm('progress-{{ $trf->id }}',{{ $trf->id }})">
      <i class="fas fa-bars-progress"></i> Progression
    </button>
    <button type="button" class="btn-ghost btn-sm-pro"
      onclick="toggleForm('invoice-{{ $trf->id }}',{{ $trf->id }})">
      <i class="fas fa-file-invoice"></i> Facturer les frais
    </button>
    @endif
    <span style="margin-left:auto;font-size:.75rem;color:var(--c-muted)">
      <i class="fas fa-clock" style="margin-right:.3rem"></i>Soumis {{ $trf->created_at->diffForHumans() }}
    </span>
  </div>

  {{-- Progress form ── --}}
  @if($trf->status === \App\Models\Transfer::STATUS_PENDING)
  <div class="trf-form" id="progress-{{ $trf->id }}" style="display:none;padding:1rem 1.25rem;border-top:1px solid var(--c-border);background:rgba(198,161,91,.05)">
    <form method="POST" action="{{ route('admin.transfers.progress', $trf) }}">
      @csrf
      <label class="form-label-pro" style="display:flex;justify-content:space-between">
        <span>Pourcentage d'avancement</span>
        <strong id="pval-{{ $trf->id }}">{{ $trf->progress }} %</strong>
      </label>
      <input type="range" name="progress" min="0" max="99" step="1" value="{{ $trf->progress }}" style="width:100%;margin-bottom:.9rem"
             oninput="document.getElementById('pval-{{ $trf->id }}').textContent=this.value+' %'">

      <label style="display:flex;align-items:flex-start;gap:.55rem;font-size:.82rem;margin-bottom:.6rem;cursor:pointer">
        <input type="checkbox" name="require_code" value="1" {{ $trf->code_required ? 'checked' : '' }} style="margin-top:.2rem">
        <span><strong>Bloquer à ce niveau (code requis)</strong><br>
          <span style="color:var(--c-muted);font-size:.75rem">Un code à 6 chiffres est généré. Le client ne peut poursuivre qu'en le saisissant : communiquez-le-lui vous-même.</span></span>
      </label>
      @if($trf->unlock_code || $trf->code_verified_at)
      <label style="display:flex;align-items:center;gap:.55rem;font-size:.8rem;margin-bottom:.9rem;cursor:pointer">
        <input type="checkbox" name="regenerate" value="1"> Générer un nouveau code
      </label>
      @endif
      <div style="display:flex;gap:.5rem">
        <button type="submit" class="btn-accent btn-sm-pro"><i class="fas fa-floppy-disk"></i> Enregistrer la progression</button>
        <button type="button" class="btn-ghost btn-sm-pro" onclick="toggleForm('progress-{{ $trf->id }}',{{ $trf->id }})">Annuler</button>
      </div>
    </form>
  </div>
  @endif

  {{-- Approve form ── --}}
  <div class="trf-form" id="approve-{{ $trf->id }}" style="display:none;padding:1rem 1.25rem;border-top:1px solid var(--c-border);background:rgba(5,150,105,.03)">
    {{-- Validation d un virement : mouvement d argent irreversible, le rejet etait
         confirme mais pas l approbation. --}}
    <form method="POST" action="{{ route('admin.transfers.approve', $trf) }}"
          data-confirm="Le virement sera valide et le montant debite du compte client."
          data-confirm-title="Valider ce virement ?" data-confirm-ok="Valider">
      @csrf
      <label class="form-label-pro">Note de validation (optionnel)</label>
      <input type="text" name="admin_note" class="form-control-pro" style="margin-bottom:.875rem"
        placeholder="Ex : virement traité — délai estimé 2 jours ouvrés" maxlength="500">
      <div style="display:flex;gap:.5rem">
        <button type="submit" class="btn-navy btn-sm-pro" style="background:var(--c-green)">
          <i class="fas fa-check"></i> Confirmer la validation
        </button>
        <button type="button" class="btn-ghost btn-sm-pro"
          onclick="toggleForm('approve-{{ $trf->id }}',{{ $trf->id }})">Annuler</button>
      </div>
    </form>
  </div>

  {{-- Reject form ── --}}
  <div class="trf-form" id="reject-{{ $trf->id }}" style="display:none;padding:1rem 1.25rem;border-top:1px solid var(--c-border);background:rgba(220,38,38,.03)">
    <form method="POST" action="{{ route('admin.transfers.reject', $trf) }}"
          data-confirm="Rejeter ce virement ? Les fonds seront recrédités au client.">
      @csrf
      <label class="form-label-pro">Motif du rejet (optionnel)</label>
      <input type="text" name="admin_note" class="form-control-pro" style="margin-bottom:.875rem"
        placeholder="Ex : IBAN invalide, KYC incomplet, limite atteinte…" maxlength="500">
      <div style="display:flex;gap:.5rem">
        <button type="submit" class="btn-navy btn-sm-pro" style="background:var(--c-red)">
          <i class="fas fa-times"></i> Confirmer le rejet
        </button>
        <button type="button" class="btn-ghost btn-sm-pro"
          onclick="toggleForm('reject-{{ $trf->id }}',{{ $trf->id }})">Annuler</button>
      </div>
    </form>
  </div>

  {{-- Invoice form ── --}}
  @if($trf->status === \App\Models\Transfer::STATUS_PENDING)
  <div class="trf-form" id="invoice-{{ $trf->id }}" style="display:none;padding:1rem 1.25rem;border-top:1px solid var(--c-border);background:rgba(37,99,235,.03)">
    <form method="POST" action="{{ route('admin.transfers.invoice', $trf) }}"
          data-confirm="Créer la facture de frais et l'envoyer au client ?">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.875rem">
        <div>
          <label class="form-label-pro">Montant des frais *</label>
          <input type="number" name="fee_amount" class="form-control-pro" step="0.01" min="0.01" placeholder="0,00" required>
        </div>
        <div>
          <label class="form-label-pro">Description</label>
          <input type="text" name="description" class="form-control-pro"
            placeholder="Frais de traitement…" maxlength="500">
        </div>
      </div>
      <div style="display:flex;gap:.5rem">
        <button type="submit" class="btn-accent btn-sm-pro">
          <i class="fas fa-paper-plane"></i> Créer &amp; envoyer
        </button>
        <button type="button" class="btn-ghost btn-sm-pro"
          onclick="toggleForm('invoice-{{ $trf->id }}',{{ $trf->id }})">Annuler</button>
      </div>
    </form>
  </div>
  @endif

  @else
  <div style="padding:.625rem 1.25rem;border-top:1px solid var(--c-border);font-size:.78rem;color:var(--c-muted)">
    @if($trf->processed_at)
      <i class="fas fa-clock" style="margin-right:.35rem"></i>
      Traité le {{ $trf->processed_at->format('d/m/Y à H:i') }}
      @if($trf->admin) · par {{ $trf->admin->name }} @endif
    @endif
  </div>
  @endif

</div>
@endforeach
</div>

@if($transfers->hasPages())
<div style="margin-top:1.5rem">{{ $transfers->links('partials.pagination') }}</div>
@endif
@endif

<script>
function toggleForm(id, trfId) {
  ['approve','reject','invoice','progress'].forEach(t => {
    const el = document.getElementById(t + '-' + trfId);
    if (el && el.id !== id) el.style.display = 'none';
  });
  const el = document.getElementById(id);
  if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

@endsection
