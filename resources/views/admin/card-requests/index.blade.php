@extends('layouts.dashboard')
@section('title', 'Demandes de carte')
@section('page_title', 'Demandes de carte')

@section('content')
<div class="page-hdr-row">
  <div class="page-hdr">
    <h1>Demandes de carte</h1>
    <p>Cartes Visa demandées par les clients.</p>
  </div>
</div>


<div class="card-pro">
  @forelse($requests as $r)
  <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;padding:.9rem 1.25rem;border-top:1px solid var(--c-border)">
    <div style="flex:1;min-width:200px">
      <strong>{{ $r->user->name }}</strong>
      <div style="font-size:.78rem;color:var(--c-muted)">{{ $r->user->email }} · demandée le {{ $r->created_at->format('d/m/Y à H:i') }}</div>
      @if($r->reason)<div style="font-size:.78rem;color:var(--c-muted)">Motif : {{ $r->reason }}</div>@endif
    </div>

    @if($r->status === \App\Models\CardRequest::STATUS_PENDING)
    <form data-confirm="Émettre une carte Visa pour {{ $r->user->name }} ?" data-confirm-title="Émettre la carte" data-confirm-ok="Émettre" method="POST" action="{{ route('admin.card-requests.approve', $r) }}">@csrf
      <button class="btn-accent btn-sm-pro" type="submit"><i class="fab fa-cc-visa"></i> Émettre la carte Visa</button>
    </form>
    <form data-confirm="Refuser la demande de carte de {{ $r->user->name }} ? Le client sera prévenu." data-confirm-title="Refuser la demande" data-confirm-ok="Refuser" data-confirm-danger="1" method="POST" action="{{ route('admin.card-requests.reject', $r) }}" style="display:flex;gap:.4rem">@csrf
      <input type="text" name="reason" class="form-control-pro" placeholder="Motif (facultatif)" maxlength="500" style="min-width:160px">
      <button class="btn-ghost btn-sm-pro" type="submit">Refuser</button>
    </form>
    @elseif($r->status === \App\Models\CardRequest::STATUS_APPROVED)
    <span style="color:var(--c-green);font-weight:700;font-size:.8rem"><i class="fas fa-circle-check"></i> Émise</span>
    @else
    <span style="color:var(--c-muted);font-weight:700;font-size:.8rem"><i class="fas fa-ban"></i> Refusée</span>
    @endif
  </div>
  @empty
  <div style="padding:2rem;text-align:center;color:var(--c-muted)">Aucune demande de carte.</div>
  @endforelse
</div>

<div style="margin-top:1rem">{{ $requests->links() }}</div>
@endsection
