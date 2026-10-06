@extends('layouts.dashboard')
@section('title', 'Nouvelle facture — ' . site_name())
@section('page_title', 'Nouvelle facture')

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Créer une facture</h4>
    <p>Les lignes sont calculées automatiquement.</p>
  </div>
  <a href="{{ route('admin.invoices.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.invoices.store') }}" method="POST"
      x-data="invoiceForm()" x-init="addLine()" @submit.prevent="submitForm()">
  @csrf

  <div class="row g-4">

    {{-- Colonne gauche : infos générales --}}
    <div class="col-xl-5">
      <div class="card-pro mb-4">
        <div class="card-pro-hdr"><div class="card-pro-title"><span class="icon-dot"></span>Informations générales</div></div>
        <div class="card-pro-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label-pro">Client *</label>
              <select name="client_id" class="form-control-pro" required>
                <option value="">— Sélectionner un client —</option>
                @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->email }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label-pro">Date d'émission *</label>
              <input type="date" name="issue_date" class="form-control-pro"
                     value="{{ old('issue_date', now()->toDateString()) }}" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label-pro">Date d'échéance</label>
              <input type="date" name="due_date" class="form-control-pro"
                     value="{{ old('due_date') }}">
            </div>
            <div class="col-sm-6">
              <label class="form-label-pro">Devise *</label>
              <select name="currency" class="form-control-pro" x-model="currency">
                @foreach($currencies as $cur)
                <option value="{{ $cur }}" {{ old('currency', \App\Models\Currency::default()) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label-pro">TVA (%)</label>
              <input type="number" name="tax_rate" class="form-control-pro"
                     value="{{ old('tax_rate', 0) }}" min="0" max="100" step="0.1"
                     x-model.number="taxRate" @input="calcTotals()">
            </div>
            <div class="col-12">
              <label class="form-label-pro">Description / objet</label>
              <textarea name="description" class="form-control-pro" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label-pro">Note interne</label>
              <input type="text" name="note" class="form-control-pro"
                     value="{{ old('note') }}" placeholder="Note visible sur la facture client">
            </div>
          </div>
        </div>
      </div>

      {{-- Récapitulatif --}}
      <div class="card-pro" style="background:linear-gradient(135deg,#1B4976,#0D2E52);color:#fff;border:none">
        <div class="card-pro-body">
          <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.8rem;color:rgba(255,255,255,.6)">
            <span>Sous-total</span>
            <span x-text="fmt(subtotal) + ' ' + currency">0</span>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.8rem;color:rgba(255,255,255,.6)">
            <span>TVA (<span x-text="taxRate">0</span>%)</span>
            <span x-text="fmt(taxAmt) + ' ' + currency">0</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding-top:.625rem;border-top:1px solid rgba(255,255,255,.15);font-size:1.25rem;font-weight:800;font-family:'Space Grotesk',sans-serif">
            <span>Total TTC</span>
            <span x-text="fmt(total) + ' ' + currency">0</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Colonne droite : lignes --}}
    <div class="col-xl-7">
      <div class="card-pro">
        <div class="card-pro-hdr">
          <div class="card-pro-title"><span class="icon-dot"></span>Lignes de facturation</div>
          <button type="button" @click="addLine()" class="btn-ghost btn-sm-pro">
            <i class="fas fa-plus"></i> Ajouter
          </button>
        </div>
        <div class="card-pro-body" style="padding:0">

          {{-- Entêtes --}}
          <div style="display:grid;grid-template-columns:1fr 80px 100px 100px 36px;gap:.5rem;padding:.625rem 1rem;background:rgba(0,0,0,.03);border-bottom:1px solid var(--c-border);font-size:.7rem;font-weight:700;color:var(--c-muted);text-transform:uppercase;letter-spacing:.06em">
            <span>Description</span><span>Qté</span><span>Prix unit.</span><span>Total</span><span></span>
          </div>

          <div id="inv-lines" style="padding:.75rem 1rem;display:flex;flex-direction:column;gap:.625rem">
            <template x-for="(line, idx) in lines" :key="idx">
              <div style="display:grid;grid-template-columns:1fr 80px 100px 100px 36px;gap:.5rem;align-items:center">
                <input type="text" :name="'items['+idx+'][description]'" class="form-control-pro"
                       x-model="line.description" placeholder="Prestation…" required>
                <input type="number" :name="'items['+idx+'][quantity]'" class="form-control-pro"
                       x-model.number="line.qty" min="0.01" step="0.01" @input="calcTotals()" required>
                <input type="number" :name="'items['+idx+'][unit_price]'" class="form-control-pro"
                       x-model.number="line.price" min="0" step="0.01" @input="calcTotals()" required>
                <div style="padding:.5rem .625rem;background:rgba(198,161,91,.08);border:1.5px solid rgba(198,161,91,.25);border-radius:var(--radius-sm);font-size:.8125rem;font-weight:700;color:var(--c-navy);text-align:right"
                     x-text="fmt(line.qty * line.price)">0</div>
                <button type="button" @click="removeLine(idx)" x-show="lines.length > 1"
                        style="width:36px;height:36px;border:none;background:rgba(220,38,38,.1);color:#dc2626;border-radius:var(--radius-sm);cursor:pointer;font-size:.8rem">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </template>
          </div>

        </div>
      </div>

      <div style="margin-top:1.25rem;display:flex;gap:.75rem;justify-content:flex-end">
        <a href="{{ route('admin.invoices.index') }}" class="btn-ghost btn-sm-pro">Annuler</a>
        <button type="submit" class="btn-navy btn-sm-pro">
          <i class="fas fa-save"></i> Enregistrer en brouillon
        </button>
      </div>
    </div>

  </div>
</form>

<script>
function invoiceForm() {
  return {
    lines: [],
    currency: '{{ old('currency', \App\Models\Currency::default()) }}',
    taxRate: {{ old('tax_rate', 0) }},
    subtotal: 0, taxAmt: 0, total: 0,

    addLine() {
      this.lines.push({ description: '', qty: 1, price: 0 });
      this.calcTotals();
    },
    removeLine(i) {
      this.lines.splice(i, 1);
      this.calcTotals();
    },
    calcTotals() {
      this.subtotal = this.lines.reduce((s, l) => s + (l.qty * l.price), 0);
      this.taxAmt   = this.subtotal * this.taxRate / 100;
      this.total    = this.subtotal + this.taxAmt;
    },
    fmt(n) {
      return new Intl.NumberFormat('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}).format(n || 0);
    },
    submitForm() {
      this.$el.submit();
    }
  }
}
</script>
@endsection
