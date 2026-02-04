<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('x-lang');

        if (!in_array($lang, ["ar", "en"])) {

            $lang = config('app.locale');
            
        }

        if ($lang != app()->getLocale()) {
            app()->setLocale($lang);
        }
        return $next($request);
    }
}
