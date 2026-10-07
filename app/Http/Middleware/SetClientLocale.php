<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetClientLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolve($request));
        return $next($request);
    }

    private function resolve(Request $request): string
    {
        $supported = Language::enabledCodes();

        // 1. Langue du profil client (base de données)
        $user = $request->user();
        if ($user && in_array($user->locale, $supported, true)) {
            return $user->locale;
        }

        // 2. Header Accept-Language du navigateur
        foreach (explode(',', $request->header('Accept-Language', '')) as $part) {
            $code = strtolower(substr(trim($part), 0, 2));
            if (in_array($code, $supported, true)) {
                return $code;
            }
        }

        return 'en';
    }
}
