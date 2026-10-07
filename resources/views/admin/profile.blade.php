@extends('layouts.dashboard')
@section('title', 'Mon Profil — ' . site_name())
@section('page_title', 'Mon Profil')

@section('content')
<div style="max-width:600px;margin:0 auto">

  {{-- Avatar card --}}
  <div class="card-pro" style="margin-bottom:1.5rem;padding:1.75rem;text-align:center">
    <div style="width:72px;height:72px;border-radius:50%;margin:0 auto .875rem;
      background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
      display:flex;align-items:center;justify-content:center;
      font-size:1.75rem;font-weight:800;color:var(--c-accent)">
      {{ strtoupper(substr($user->name,0,1)) }}
    </div>
    <div style="font-size:1.0625rem;font-weight:800;color:var(--c-navy)">{{ $user->name }}</div>
    <div style="font-size:.75rem;color:var(--c-muted);margin-top:.25rem">
      @foreach($user->getRoleNames() as $role)
        <span style="display:inline-block;padding:.2rem .65rem;border-radius:999px;background:var(--c-amber-l);color:var(--c-amber);font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em">{{ $role }}</span>
      @endforeach
    </div>
  </div>

  {{-- Infos contact --}}
  <div class="card-pro" style="margin-bottom:1.25rem">
    <div class="card-pro-hdr">
      <div class="card-pro-title"><span class="icon-dot"></span> Informations de contact</div>
    </div>
    <div class="card-pro-body">
      <form data-confirm="Enregistrer les modifications de votre profil ?" data-confirm-title="Profil" data-confirm-ok="Enregistrer" method="POST" action="{{ route('admin.profile.update') }}">
        @csrf

        <div style="margin-bottom:1.125rem">
          <label style="display:block;font-size:.75rem;font-weight:600;color:var(--c-muted);margin-bottom:.375rem">
            Nom complet
          </label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}"
            style="width:100%;padding:.625rem .875rem;border-radius:var(--radius-sm);
              border:1.5px solid {{ $errors->has('name') ? 'var(--c-red)' : 'var(--c-border)' }};
              background:var(--c-bg);color:var(--c-text);font-size:.8375rem;outline:none;transition:.15s"
            onfocus="this.style.borderColor='var(--c-accent)'" onblur="this.style.borderColor='var(--c-border)'">
          @error('name')
          <div style="font-size:.72rem;color:var(--c-red);margin-top:.3rem">{{ $message }}</div>
          @enderror
        </div>

        <div style="margin-bottom:1.125rem">
          <label style="display:block;font-size:.75rem;font-weight:600;color:var(--c-muted);margin-bottom:.375rem">
            Adresse e-mail
          </label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}"
            style="width:100%;padding:.625rem .875rem;border-radius:var(--radius-sm);
              border:1.5px solid {{ $errors->has('email') ? 'var(--c-red)' : 'var(--c-border)' }};
              background:var(--c-bg);color:var(--c-text);font-size:.8375rem;outline:none;transition:.15s"
            onfocus="this.style.borderColor='var(--c-accent)'" onblur="this.style.borderColor='var(--c-border)'">
          @error('email')
          <div style="font-size:.72rem;color:var(--c-red);margin-top:.3rem">{{ $message }}</div>
          @enderror
        </div>

        <div style="margin-bottom:1.25rem">
          <label style="display:block;font-size:.75rem;font-weight:600;color:var(--c-muted);margin-bottom:.375rem">
            Numéro de téléphone <span style="color:var(--c-muted);font-weight:400">(WhatsApp)</span>
          </label>
          <div style="display:flex;gap:.5rem;align-items:center">
            <span style="padding:.625rem .875rem;border-radius:var(--radius-sm);border:1.5px solid var(--c-border);
              background:var(--c-surface);color:var(--c-muted);font-size:.8375rem;flex-shrink:0">
              <i class="fab fa-whatsapp" style="color:#25D366"></i>
            </span>
            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
              placeholder="+33 6 00 00 00 00"
              style="flex:1;padding:.625rem .875rem;border-radius:var(--radius-sm);
                border:1.5px solid var(--c-border);background:var(--c-bg);
                color:var(--c-text);font-size:.8375rem;outline:none;transition:.15s"
              onfocus="this.style.borderColor='var(--c-accent)'" onblur="this.style.borderColor='var(--c-border)'">
          </div>
          <div style="font-size:.7rem;color:var(--c-muted);margin-top:.375rem">
            Ce numéro sera visible par vos clients pour vous contacter sur WhatsApp.
          </div>
          @error('phone')
          <div style="font-size:.72rem;color:var(--c-red);margin-top:.3rem">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit"
          style="padding:.625rem 1.5rem;border-radius:var(--radius-sm);border:none;
            background:var(--c-navy);color:var(--c-accent);font-size:.8125rem;font-weight:700;cursor:pointer;transition:.15s"
          onmouseover="this.style.background='var(--c-navy-3)'" onmouseout="this.style.background='var(--c-navy)'">
          <i class="fas fa-save" style="margin-right:.4rem"></i> Enregistrer
        </button>
      </form>
    </div>
  </div>

  {{-- Mot de passe --}}
  <div class="card-pro">
    <div class="card-pro-hdr">
      <div class="card-pro-title"><span class="icon-dot"></span> Changer le mot de passe</div>
    </div>
    <div class="card-pro-body">
      <form data-confirm="Changer votre mot de passe ?" data-confirm-title="Mot de passe" data-confirm-ok="Changer" method="POST" action="{{ route('admin.profile.password') }}">
        @csrf

        <div style="margin-bottom:1rem">
          <label style="display:block;font-size:.75rem;font-weight:600;color:var(--c-muted);margin-bottom:.375rem">Mot de passe actuel</label>
          <input type="password" name="current_password"
            style="width:100%;padding:.625rem .875rem;border-radius:var(--radius-sm);
              border:1.5px solid {{ $errors->has('current_password') ? 'var(--c-red)' : 'var(--c-border)' }};
              background:var(--c-bg);color:var(--c-text);font-size:.8375rem;outline:none;transition:.15s"
            onfocus="this.style.borderColor='var(--c-accent)'" onblur="this.style.borderColor='var(--c-border)'">
          @error('current_password')
          <div style="font-size:.72rem;color:var(--c-red);margin-top:.3rem">{{ $message }}</div>
          @enderror
        </div>

        <div style="margin-bottom:1rem">
          <label style="display:block;font-size:.75rem;font-weight:600;color:var(--c-muted);margin-bottom:.375rem">Nouveau mot de passe</label>
          <input type="password" name="password"
            style="width:100%;padding:.625rem .875rem;border-radius:var(--radius-sm);
              border:1.5px solid var(--c-border);background:var(--c-bg);
              color:var(--c-text);font-size:.8375rem;outline:none;transition:.15s"
            onfocus="this.style.borderColor='var(--c-accent)'" onblur="this.style.borderColor='var(--c-border)'">
        </div>

        <div style="margin-bottom:1.25rem">
          <label style="display:block;font-size:.75rem;font-weight:600;color:var(--c-muted);margin-bottom:.375rem">Confirmer le mot de passe</label>
          <input type="password" name="password_confirmation"
            style="width:100%;padding:.625rem .875rem;border-radius:var(--radius-sm);
              border:1.5px solid var(--c-border);background:var(--c-bg);
              color:var(--c-text);font-size:.8375rem;outline:none;transition:.15s"
            onfocus="this.style.borderColor='var(--c-accent)'" onblur="this.style.borderColor='var(--c-border)'">
        </div>

        <button type="submit"
          style="padding:.625rem 1.5rem;border-radius:var(--radius-sm);border:none;
            background:var(--c-red);color:#fff;font-size:.8125rem;font-weight:700;cursor:pointer;transition:.15s"
          onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
          <i class="fas fa-lock" style="margin-right:.4rem"></i> Modifier le mot de passe
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
