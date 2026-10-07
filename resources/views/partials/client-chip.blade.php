{{-- Compte connecté : avatar + prénom, visible à partir de la tablette (mène au profil) --}}
@auth
<a href="{{ route('client.app.profile') }}" class="ca-chip" aria-label="{{ __('app.nav_profile') }}">
  <span class="ca-chip__avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
  <span class="ca-chip__name">{{ \Illuminate\Support\Str::words(auth()->user()->name, 2, '') }}</span>
  <i class="fas fa-chevron-down"></i>
</a>
@endauth
