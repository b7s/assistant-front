<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale', config('app.locale'));

        $availableLocales = array_keys(config('app.available_locales', []));

        if (in_array($locale, $availableLocales, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
