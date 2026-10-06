@php
    $locale = app()->getLocale(); $route = Route::currentRouteName() ?? 'home';
    $siteContact = \App\Models\SiteContact::current();
@endphp

<header id="site-header" class="site-header fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="max-w-screen-xl mx-auto px-3 flex items-center justify-between h-20">

        <!-- Logo -->
        <a href="{{ route('home', ['locale' => $locale]) }}" class="flex items-center gap-3 flex-shrink-0">
            <span class="logo-img logo-img--white">
                <x-logo variant="full" theme="dark" size="lg" :dark="$siteContact->logo_dark_path ? Storage::url($siteContact->logo_dark_path) : null" :name="$siteContact->name" />
            </span>
            <span class="logo-img logo-img--color">
                <x-logo variant="full" theme="light" size="lg" :light="$siteContact->logo_light_path ? Storage::url($siteContact->logo_light_path) : null" :name="$siteContact->name" />
            </span>
        </a>

        <!-- Desktop nav -->
        <nav class="hidden lg:flex items-center gap-1">
            @php
            $links = [
                ['route' => 'home',     'label' => 'menu.home'],
                ['route' => 'about',    'label' => 'menu.about'],
                ['route' => 'services', 'label' => 'menu.services'],
                ['route' => 'faq',      'label' => 'menu.faq'],
                ['route' => 'contact',  'label' => 'menu.contact'],
            ];
            $serviceRoutes = ['services','services.auto','services.personal','services.home','services.study','services.business','services.bike'];
            @endphp
            @foreach ($links as $link)
            @php
            $active = $link['route'] === 'services'
                ? in_array($route, $serviceRoutes)
                : $route === $link['route'];
            @endphp
            <a href="{{ route($link['route'], ['locale' => $locale]) }}"
               class="nav-link px-4 py-2 text-sm font-semibold rounded-lg transition-colors duration-200 {{ $active ? 'nav-link--active' : '' }}">
                @lang($link['label'])
            </a>
            @endforeach
        </nav>

        <!-- Right actions -->
        <div class="flex items-center gap-3">

            <!-- Language switcher -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="lang-btn flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200">
                    @php $flagExt = \App\Models\Language::enabledList()->firstWhere('code', $locale)->flag_ext ?? 'png'; @endphp
                    <img src="{{ asset('images/' . $locale . '.' . $flagExt) }}" alt="{{ $locale }}" class="w-5 h-auto rounded-sm">
                    <span class="hidden sm:inline">{{ strtoupper($locale) }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-card-hover border border-gray-100 py-1.5 z-50" style="display:none">
                    @foreach (\App\Models\Language::enabledList() as $lang)
                    @if ($lang->code !== $locale)
                    <a href="{{ route($route ?? 'home', ['locale' => $lang->code]) }}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 hover:bg-cream hover:text-navy transition-colors duration-150 rounded-lg mx-1">
                        <img src="{{ asset('images/' . $lang->code . '.' . $lang->flag_ext) }}" alt="{{ $lang->code }}" class="w-5 h-auto rounded-sm">
                        {{ strtoupper($lang->code) }}
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>

            <!-- CTA -->
            <a href="{{ route('signup', ['locale' => $locale]) }}"
               class="hidden lg:inline-flex items-center gap-2 btn-primary text-sm">
                <span>@lang('menu.open_account')</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>

            <!-- Mobile toggle -->
            <button @click="mobileOpen = !mobileOpen"
                class="lg:hidden p-2 rounded-lg mobile-menu-btn transition-colors duration-200"
                aria-label="Menu">
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden mobile-menu border-t" style="display:none; position:relative; z-index:9999;">
        <div class="max-w-screen-xl mx-auto px-3 py-4 space-y-1">
            @foreach ($links as $link)
            @php
            $active = $link['route'] === 'services'
                ? in_array($route, $serviceRoutes)
                : $route === $link['route'];
            @endphp
            <a href="{{ route($link['route'], ['locale' => $locale]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors duration-150 {{ $active ? 'bg-navy text-white' : 'text-gray-700 hover:bg-gray-50' }}">
                @lang($link['label'])
            </a>
            @endforeach
            <div class="pt-3 border-t border-gray-100 mt-3">
                <a href="{{ route('signup', ['locale' => $locale]) }}"
                   class="flex items-center justify-center gap-2 btn-primary w-full py-3">
                    <span>@lang('menu.open_account')</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Header spacer (non-home pages) -->
@if(!request()->routeIs('home'))
<div class="h-20"></div>
@endif
