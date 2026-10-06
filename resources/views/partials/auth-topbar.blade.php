@php
  $cur   = app()->getLocale();
  $langs = \App\Models\Language::enabledList()->mapWithKeys(fn($l) => [$l->code => [$l->native_name, $l->flag_ext]])->all();
  // Page localisée (/{locale}/...) : le segment d'URL l'emporte sur la session, on change donc l'URL.
  $routeName = request()->route()?->getName();
  $isLocalized = request()->route('locale') && $routeName;
@endphp
<div class="topbar">
  <a href="{{ url('/') }}" class="topbar__back"><i class="fas fa-arrow-left"></i><span>{{ __('auth.back_site') }}</span></a>

  @if(isset($langs[$cur]))
  <div class="ls" x-data="{open:false}" @keydown.escape.window="open=false">
    <button class="ls__btn" type="button" @click="open=!open" @click.outside="open=false"
            :aria-expanded="open.toString()" aria-haspopup="listbox">
      <i class="fas fa-globe ls__globe"></i>
      <img src="{{ asset('images/'.$cur.'.'.$langs[$cur][1]) }}" alt="">
      <span class="ls__code">{{ strtoupper($cur) }}</span>
      <i class="fas fa-chevron-down ls__chev" :class="open && 'is-open'"></i>
    </button>
    <div class="ls__menu" x-show="open" x-transition.opacity style="display:none" role="listbox">
      @foreach($langs as $code => [$label, $ext])
      @php
        $langUrl = $isLocalized
            ? route($routeName, array_merge(request()->route()->parameters(), ['locale' => $code]))
            : route('lang.switch', $code);
      @endphp
      <a href="{{ $langUrl }}" class="ls__opt {{ $cur === $code ? 'cur' : '' }}" role="option" @if($cur === $code) aria-selected="true" @endif>
        <img src="{{ asset('images/'.$code.'.'.$ext) }}" alt="">
        <span>{{ $label }}</span>
        @if($cur === $code)<i class="fas fa-check"></i>@endif
      </a>
      @endforeach
    </div>
  </div>
  @endif
</div>
