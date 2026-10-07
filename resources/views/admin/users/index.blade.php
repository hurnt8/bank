@extends('layouts.dashboard')
@section('title','Gestion des utilisateurs')
@section('page_title','Clients & Utilisateurs')

@section('content')

{{-- En-tête --}}
<div class="page-hdr-row">
  <div class="page-hdr">
    <h1>{{ $isSuperAdmin ? 'Tous les utilisateurs' : 'Mes clients' }}</h1>
    <p>{{ $isSuperAdmin ? 'Vue globale — tous les comptes enregistrés sur la plateforme' : 'Uniquement les clients que vous avez créés' }}</p>
  </div>
  <div class="page-hdr-actions">
    <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#createUserModal">
      <i class="fas fa-user-plus"></i> Nouveau client
    </button>
  </div>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4">
    <div class="metric-card">
      <div class="metric-card__icon mi-navy"><i class="fas fa-users"></i></div>
      <div class="metric-card__val">{{ $stats['total'] }}</div>
      <div class="metric-card__lbl">{{ $isSuperAdmin ? 'Total utilisateurs' : 'Mes clients' }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="metric-card">
      <div class="metric-card__icon mi-blue"><i class="fas fa-user-check"></i></div>
      <div class="metric-card__val">{{ $stats['client'] }}</div>
      <div class="metric-card__lbl">Clients</div>
    </div>
  </div>
  @if($isSuperAdmin)
  <div class="col-6 col-md-4">
    <div class="metric-card">
      <div class="metric-card__icon mi-amber"><i class="fas fa-user-tie"></i></div>
      <div class="metric-card__val">{{ $stats['staff'] }}</div>
      <div class="metric-card__lbl">Personnel</div>
    </div>
  </div>
  @endif
</div>

{{-- Filtres --}}
<div class="filter-bar mb-4">
  <form method="GET" action="{{ route('admin.users') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
    <input type="text" name="search" placeholder="Rechercher par nom ou email…"
           value="{{ request('search') }}" style="min-width:200px;flex:1">
    @if($isSuperAdmin)
    <select name="type" style="min-width:160px">
      <option value="">Tous les types</option>
      <option value="client" {{ request('type')==='client'?'selected':'' }}>Clients</option>
      <option value="staff"  {{ request('type')==='staff' ?'selected':'' }}>Personnel</option>
    </select>
    @endif
    <button type="submit" class="btn-navy btn-sm-pro">
      <i class="fas fa-search"></i> Filtrer
    </button>
    @if(request()->anyFilled(['search','type']))
    <a href="{{ route('admin.users') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-times"></i> Réinitialiser
    </a>
    @endif
  </form>
</div>

{{-- Table --}}
<div class="card-pro">
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th style="width:52px"></th>
          <th>Identité</th>
          <th>Contact</th>
          <th>Statut</th>
          <th>Rôle</th>
          <th>Inscription</th>
          <th style="text-align:right;width:108px">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          {{-- Avatar --}}
          <td data-label="">
            <div style="width:36px;height:36px;border-radius:50%;
                        background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
                        display:flex;align-items:center;justify-content:center;
                        font-size:.7rem;font-weight:800;color:var(--c-accent)">
              {{ strtoupper(substr($user->name,0,1)) }}
            </div>
          </td>

          {{-- Identité --}}
          <td data-label="Identité">
            <div class="cell-name">{{ $user->name }}</div>
            <div class="cell-sub">{{ $user->email }}</div>
          </td>

          {{-- Contact --}}
          <td data-label="Contact">
            <div style="font-size:.8125rem;color:var(--c-text)">{{ $user->phone ?? '—' }}</div>
            @if($user->address)
            <div class="cell-sub">{{ Str::limit($user->address,30) }}</div>
            @endif
          </td>

          {{-- Statut compte --}}
          <td data-label="Statut">
            @if($user->invitation_token)
              <span class="badge-status bs-amber">
                <i class="fas fa-clock" style="font-size:.55rem"></i> En attente
              </span>
            @else
              <span class="badge-status bs-green">
                <i class="fas fa-check" style="font-size:.55rem"></i> Activé
              </span>
            @endif
          </td>

          {{-- Rôle --}}
          <td data-label="Rôle">
            @foreach($user->getRoleNames() as $r)
            <span class="badge-status bs-blue">{{ ucfirst($r) }}</span>
            @endforeach
          </td>

          {{-- Date --}}
          <td data-label="Inscription" style="color:var(--c-muted);font-size:.78rem;white-space:nowrap">
            {{ $user->created_at->format('d/m/Y') }}
          </td>

          {{-- Actions --}}
          <td data-label="Actions" style="text-align:right">
            <div style="display:flex;gap:.375rem;justify-content:flex-end">
              {{-- Renvoyer invitation si pas encore activé --}}
              @if($user->invitation_token)
              <form data-confirm="Renvoyer l’e-mail d’invitation à {{ $user->email }} ?" data-confirm-title="Renvoyer l’invitation" data-confirm-ok="Renvoyer" action="{{ route('admin.users.resend-invite',$user) }}" method="POST">
                @csrf
                <button type="submit" class="btn-icon btn-icon-success" title="Renvoyer l'invitation">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </form>
              @endif

              {{-- Affecter à un admin (super-admin uniquement, clients seulement) --}}
              @if($isSuperAdmin && $user->hasRole('client'))
              <button class="btn-icon"
                      style="background:rgba(139,92,246,.1);border:1px solid rgba(139,92,246,.25);color:#7c3aed"
                      data-bs-toggle="modal"
                      data-bs-target="#assignModal{{ $user->id }}"
                      title="Affecter à un admin">
                <i class="fas fa-user-tag"></i>
              </button>
              @endif

              <a href="{{ route('admin.users.show', $user) }}"
                 class="btn-icon btn-icon-primary"
                 title="Voir la fiche">
                <i class="fas fa-eye"></i>
              </a>

              <button class="btn-icon"
                      style="background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.2);color:var(--c-blue)"
                      data-bs-toggle="modal"
                      data-bs-target="#editModal{{ $user->id }}"
                      title="Modifier">
                <i class="fas fa-pen"></i>
              </button>

              @if(! $user->hasRole('super-admin') || $isSuperAdmin)
              <form action="{{ route('admin.users.destroy',$user) }}" method="POST"
                    data-confirm="Supprimer {{ $user->name }} ?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon btn-icon-danger" title="Supprimer">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>

        @empty
        <tr>
          <td colspan="7">
            <div style="text-align:center;padding:4rem 2rem">
              <div style="width:72px;height:72px;border-radius:50%;
                          background:var(--c-bg);border:2px dashed var(--c-border);
                          margin:0 auto 1.125rem;
                          display:flex;align-items:center;justify-content:center">
                <i class="fas fa-users" style="font-size:1.5rem;color:var(--c-border)"></i>
              </div>
              <div style="font-weight:700;font-size:.9375rem;color:var(--c-navy);margin-bottom:.375rem">
                Aucun client trouvé
              </div>
              <div style="font-size:.8125rem;color:var(--c-muted);max-width:340px;margin:0 auto 1.25rem">
                @if(request()->anyFilled(['search','type']))
                  Aucun résultat pour ces critères de recherche.
                @else
                  Vous n'avez pas encore créé de client. Cliquez sur <strong>Nouveau client</strong> pour commencer.
                @endif
              </div>
              @if(! request()->anyFilled(['search','type']))
              <button class="btn-navy" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-user-plus"></i> Créer le premier client
              </button>
              @endif
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
  <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border)">
    {{ $users->links() }}
  </div>
  @endif
</div>

{{-- ═════════════════════════════════════════════════════════════════════
     Modals par utilisateur — places ICI, hors du <table>.
     Un <div> ne peut pas etre enfant de <tbody> : le navigateur les sortait
     lui-meme du tableau, avec un ordre imprevisible, ce qui pouvait laisser
     un modal visible apres la fermeture d'un autre.
     ══════════════════════════════════════════════════════════════════════ --}}
@foreach($users as $user)
        {{-- ── Modal Édition ── --}}
        <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:var(--radius);border:none;box-shadow:var(--shadow)">

              <div class="modal-header" style="border-bottom:1px solid var(--c-border);padding:1.125rem 1.5rem">
                <div style="display:flex;align-items:center;gap:.875rem">
                  <div style="width:40px;height:40px;border-radius:50%;
                              background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
                              display:flex;align-items:center;justify-content:center;
                              font-size:.8rem;font-weight:800;color:var(--c-accent);flex-shrink:0">
                    {{ strtoupper(substr($user->name,0,1)) }}
                  </div>
                  <div>
                    <div style="font-size:.9375rem;font-weight:700;color:var(--c-navy)">Modifier {{ $user->name }}</div>
                    <div style="font-size:.75rem;color:var(--c-muted)">{{ $user->email }}</div>
                  </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <form data-confirm="Enregistrer les modifications de {{ $user->name }} ?" data-confirm-title="Modifier le compte" data-confirm-ok="Enregistrer" action="{{ route('admin.users.update',$user) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body" style="padding:1.5rem">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label-pro">Nom complet *</label>
                      <input type="text" name="name" class="form-control-pro"
                             value="{{ old('name',$user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label-pro">Adresse email *</label>
                      <input type="email" name="email" class="form-control-pro"
                             value="{{ old('email',$user->email) }}" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label-pro">Genre</label>
                      <select name="gender" class="form-control-pro">
                        <option value="N" {{ old('gender',$user->gender??'N')==='N'?'selected':'' }}>— Non précisé</option>
                        <option value="M" {{ old('gender',$user->gender??'N')==='M'?'selected':'' }}>♂ Monsieur / Mr.</option>
                        <option value="F" {{ old('gender',$user->gender??'N')==='F'?'selected':'' }}>♀ Madame / Ms.</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label-pro">Téléphone</label>
                      <input type="text" name="phone" class="form-control-pro"
                             value="{{ old('phone',$user->phone) }}" placeholder="+33 6 00 00 00 00">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label-pro">Type de compte</label>
                      <select name="type" class="form-control-pro">
                        <option value="client" {{ old('type',$user->type)==='client'?'selected':'' }}>Client</option>
                        @if($isSuperAdmin)
                        <option value="staff" {{ old('type',$user->type)==='staff'?'selected':'' }}>Personnel</option>
                        @endif
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label-pro">Rôle</label>
                      <select name="role" class="form-control-pro">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name)?'selected':'' }}>
                          {{ ucfirst($role->name) }}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label-pro">Adresse postale</label>
                      <input type="text" name="address" class="form-control-pro"
                             value="{{ old('address',$user->address) }}"
                             placeholder="12 rue de la Paix, 75001 Paris">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label-pro">Date de naissance</label>
                      <input type="date" name="birth_date" class="form-control-pro"
                             value="{{ old('birth_date',$user->birth_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-5">
                      <label class="form-label-pro">Type de pièce d'identité</label>
                      <select name="id_type" class="form-control-pro">
                        <option value="">— Non renseigné</option>
                        <option value="passeport"       {{ old('id_type',$user->id_type)==='passeport'       ?'selected':'' }}>Passeport</option>
                        <option value="cni"             {{ old('id_type',$user->id_type)==='cni'             ?'selected':'' }}>Carte nationale d'identité</option>
                        <option value="permis_conduire" {{ old('id_type',$user->id_type)==='permis_conduire' ?'selected':'' }}>Permis de conduire</option>
                        <option value="titre_sejour"    {{ old('id_type',$user->id_type)==='titre_sejour'    ?'selected':'' }}>Titre de séjour</option>
                        <option value="autre"           {{ old('id_type',$user->id_type)==='autre'           ?'selected':'' }}>Autre document</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label-pro">Numéro de pièce</label>
                      <input type="text" name="id_number" class="form-control-pro"
                             value="{{ old('id_number',$user->id_number) }}"
                             placeholder="Ex : AB123456">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label-pro">Devise</label>
                      <select name="currency" class="form-control-pro">
                        @foreach(\App\Models\Currency::codes() as $cur)
                        <option value="{{ $cur }}" {{ old('currency',$user->currency)===$cur?'selected':'' }}>{{ $cur }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label-pro">Langue</label>
                      <select name="locale" class="form-control-pro">
                        @foreach(['fr'=>'Français','en'=>'English','es'=>'Español','pl'=>'Polski','bg'=>'Български','hu'=>'Magyar','it'=>'Italiano','de'=>'Deutsch','lt'=>'Lietuvių','ro'=>'Română','lv'=>'Latviešu','nl'=>'Nederlands','pt'=>'Português','hr'=>'Hrvatski','sk'=>'Slovenčina','sl'=>'Slovenščina','mt'=>'Malti'] as $lc => $llabel)
                        <option value="{{ $lc }}" {{ old('locale',$user->locale)===$lc?'selected':'' }}>{{ $llabel }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:.875rem 1.5rem;gap:.5rem">
                  <button type="button" class="btn-ghost" data-bs-dismiss="modal">Annuler</button>
                  <button type="submit" class="btn-navy"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        {{-- /Modal Édition --}}

        {{-- ── Modal Affectation Admin (super-admin uniquement) ── --}}
        @if($isSuperAdmin && $user->hasRole('client'))
        @php $currentAdmin = $admins->firstWhere('id', $user->created_by); @endphp
        <div class="modal fade" id="assignModal{{ $user->id }}" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius);border:none;box-shadow:var(--shadow)">

              <div class="modal-header" style="border-bottom:1px solid var(--c-border);padding:1.125rem 1.5rem">
                <div style="display:flex;align-items:center;gap:.875rem">
                  <div style="width:38px;height:38px;border-radius:50%;
                              background:rgba(139,92,246,.1);border:1px solid rgba(139,92,246,.25);
                              display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-user-tag" style="color:#7c3aed;font-size:.85rem"></i>
                  </div>
                  <div>
                    <div style="font-size:.9375rem;font-weight:700;color:var(--c-navy)">Affecter à un administrateur</div>
                    <div style="font-size:.75rem;color:var(--c-muted)">{{ $user->name }} — {{ $user->email }}</div>
                  </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <form data-confirm="Changer l’administrateur responsable de {{ $user->name }} ?" data-confirm-title="Administrateur responsable" data-confirm-ok="Changer" action="{{ route('admin.users.assign-admin', $user) }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:1.5rem">

                  {{-- Admin actuel --}}
                  <div style="background:var(--c-bg);border:1px solid var(--c-border);border-radius:8px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.8125rem">
                    <span style="color:var(--c-muted);font-weight:500">Admin actuel :</span>
                    <strong style="color:var(--c-navy);margin-left:.375rem">
                      {{ $currentAdmin?->name ?? 'Non affecté' }}
                    </strong>
                  </div>

                  <div style="margin-bottom:1rem">
                    <label class="form-label-pro">Nouvel administrateur *</label>
                    <select name="admin_id" class="form-control-pro" required>
                      <option value="">— Sélectionner un administrateur</option>
                      @foreach($admins as $admin)
                      <option value="{{ $admin->id }}" {{ $user->created_by == $admin->id ? 'selected' : '' }}>
                        {{ $admin->name }} — {{ $admin->email }}
                        ({{ $admin->getRoleNames()->first() }})
                      </option>
                      @endforeach
                    </select>
                  </div>

                </div>

                <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:.875rem 1.5rem;gap:.5rem">
                  <button type="button" class="btn-ghost" data-bs-dismiss="modal">Annuler</button>
                  <button type="submit" class="btn-navy" style="background:#7c3aed;border-color:#7c3aed">
                    <i class="fas fa-user-tag"></i> Confirmer l'affectation
                  </button>
                </div>
              </form>

            </div>
          </div>
        </div>
        @endif
        {{-- /Modal Affectation --}}

@endforeach

{{-- ══════════════════════════════════════
     Modal Création — invitation par email
     ══════════════════════════════════════ --}}
<div class="modal fade" id="createUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:var(--radius);border:none;box-shadow:var(--shadow)">

      <div class="modal-header" style="border-bottom:1px solid var(--c-border);padding:1.125rem 1.5rem">
        <div>
          <div style="font-size:.9375rem;font-weight:700;color:var(--c-navy)">Créer un nouveau client</div>
          <div style="font-size:.75rem;color:var(--c-muted);margin-top:.15rem">
            Un email d'invitation sera envoyé automatiquement pour activation du compte
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form data-confirm="Créer ce compte et envoyer l’invitation par e-mail ?" data-confirm-title="Nouveau compte" data-confirm-ok="Créer" action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="modal-body" style="padding:1.5rem">

          {{-- Encart invitation --}}
          <div style="display:flex;gap:.75rem;align-items:flex-start;
                      background:#EFF6FF;border:1px solid #BFDBFE;border-radius:var(--radius-sm);
                      padding:.875rem 1rem;margin-bottom:1.375rem">
            <i class="fas fa-envelope-open-text" style="color:var(--c-blue);font-size:1rem;flex-shrink:0;margin-top:.1rem"></i>
            <div>
              <div style="font-size:.8rem;font-weight:700;color:var(--c-blue);margin-bottom:.2rem">
                Activation par email
              </div>
              <div style="font-size:.75rem;color:#1D4ED8;line-height:1.5">
                Le client recevra un <strong>email d'invitation</strong> avec un lien sécurisé pour créer son propre mot de passe.
                Vous n'avez pas besoin de définir de mot de passe ici.
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label-pro">Genre <span style="font-size:.7rem;color:var(--c-muted);font-weight:400">(adapte l'email d'invitation)</span></label>
              <select name="gender" class="form-control-pro">
                <option value="N" {{ old('gender')==='N'||!old('gender')?'selected':'' }}>— Non précisé</option>
                <option value="M" {{ old('gender')==='M'?'selected':'' }}>♂ Monsieur / Mr.</option>
                <option value="F" {{ old('gender')==='F'?'selected':'' }}>♀ Madame / Ms.</option>
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label-pro">Nom complet *</label>
              <input type="text" name="name" class="form-control-pro"
                     value="{{ old('name') }}" placeholder="Jean Dupont" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-pro">Adresse email * <span style="font-size:.7rem;color:var(--c-muted);font-weight:400">(l'invitation sera envoyée ici)</span></label>
              <input type="email" name="email" class="form-control-pro"
                     value="{{ old('email') }}" placeholder="jean@exemple.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-pro">Langue <span style="font-size:.7rem;color:var(--c-muted);font-weight:400">(langue de l'email d'invitation)</span></label>
              <select name="locale" class="form-control-pro">
                @foreach(['fr'=>'🇫🇷 Français','en'=>'🇬🇧 English','es'=>'🇪🇸 Español','pl'=>'🇵🇱 Polski','bg'=>'🇧🇬 Български','hu'=>'🇭🇺 Magyar','it'=>'🇮🇹 Italiano','de'=>'🇩🇪 Deutsch','lt'=>'🇱🇹 Lietuvių','ro'=>'🇷🇴 Română','lv'=>'🇱🇻 Latviešu','nl'=>'🇳🇱 Nederlands','pt'=>'🇵🇹 Português','hr'=>'🇭🇷 Hrvatski','sk'=>'🇸🇰 Slovenčina','sl'=>'🇸🇮 Slovenščina','mt'=>'🇲🇹 Malti'] as $lc => $llabel)
                <option value="{{ $lc }}" {{ old('locale')===$lc||(!old('locale')&&$lc===app()->getLocale())?'selected':'' }}>{{ $llabel }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Téléphone</label>
              <input type="text" name="phone" class="form-control-pro"
                     value="{{ old('phone') }}" placeholder="+33 6 00 00 00 00">
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Type de compte</label>
              <select name="type" class="form-control-pro">
                <option value="client">Client</option>
                @if($isSuperAdmin)
                <option value="staff">Personnel</option>
                @endif
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label-pro">Rôle</label>
              <select name="role" class="form-control-pro">
                @foreach($roles as $role)
                  @if($role->name !== 'super-admin' || $isSuperAdmin)
                  <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-pro">Adresse postale</label>
              <input type="text" name="address" class="form-control-pro"
                     value="{{ old('address') }}" placeholder="12 rue de la Paix, 75001 Paris">
            </div>
            <div class="col-md-6">
              <label class="form-label-pro">Date de naissance</label>
              <input type="date" name="birth_date" class="form-control-pro"
                     value="{{ old('birth_date') }}">
            </div>
            <div class="col-md-7">
              <label class="form-label-pro">Type de pièce d'identité</label>
              <select name="id_type" class="form-control-pro">
                <option value="">— Non renseigné</option>
                <option value="passeport"       {{ old('id_type')==='passeport'       ?'selected':'' }}>Passeport</option>
                <option value="cni"             {{ old('id_type')==='cni'             ?'selected':'' }}>Carte nationale d'identité</option>
                <option value="permis_conduire" {{ old('id_type')==='permis_conduire' ?'selected':'' }}>Permis de conduire</option>
                <option value="titre_sejour"    {{ old('id_type')==='titre_sejour'    ?'selected':'' }}>Titre de séjour</option>
                <option value="autre"           {{ old('id_type')==='autre'           ?'selected':'' }}>Autre document</option>
              </select>
            </div>
            <div class="col-md-5">
              <label class="form-label-pro">Numéro de pièce</label>
              <input type="text" name="id_number" class="form-control-pro"
                     value="{{ old('id_number') }}" placeholder="Ex : AB123456">
            </div>
            <div class="col-md-6">
              <label class="form-label-pro">Devise</label>
              <select name="currency" class="form-control-pro">
                @foreach(\App\Models\Currency::codes() as $cur)
                <option value="{{ $cur }}" {{ $cur===\App\Models\Currency::default()?'selected':'' }}>{{ $cur }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:.875rem 1.5rem;gap:.5rem">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn-navy">
            <i class="fas fa-paper-plane"></i> Créer &amp; envoyer l'invitation
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
