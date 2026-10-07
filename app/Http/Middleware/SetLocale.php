<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);
        App::setLocale($locale);
        return $next($request);
    }

    private function resolve(Request $request): string
    {
        $supported = Language::enabledCodes();

        // 1. Choix explicite dans CETTE requête (segment d'URL {locale} ou ?lang=)
        //    — un changement de langue volontaire prime toujours sur le reste.
        $explicit = $request->route('locale') ?? $request->query('lang');
        if (in_array($explicit, $supported, true)) {
            session(['locale' => $explicit]);
            return $explicit;
        }

        // 2. Utilisateur connecté : sa préférence enregistrée fait autorité.
        //    Prioritaire sur la session pour éviter qu'une langue restée en
        //    session depuis une navigation anonyme n'écrase le profil du client.
        $user = $request->user();
        if ($user && in_array($user->locale, $supported, true)) {
            session(['locale' => $user->locale]);
            return $user->locale;
        }

        // 3. Session (visiteur anonyme ayant déjà choisi une langue)
        $sessionLocale = session('locale');
        if (in_array($sessionLocale, $supported, true)) {
            return $sessionLocale;
        }

        // 4. Langue préférée du navigateur / appareil (Accept-Language)
        $header = $request->header('Accept-Language', '');
        foreach (explode(',', $header) as $part) {
            $tag  = trim(explode(';', $part)[0]);       // ex: "fr-FR" ou "en"
            $code = strtolower(substr($tag, 0, 2));
            if (in_array($code, $supported, true)) {
                return $code;
            }
        }

        // 5. Défaut : l'anglais quand la langue de l'utilisateur n'est pas disponible
        return 'en';
    }
}
