<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;

class RedirectToFrontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Si la requête est déjà une API ou un fichier statique, on passe
        if ($request->is('api/*') || $request->is('storage/*')) {
            return $next($request);
        }

        // Récupérer le chemin actuel
        $path = $request->path();

        // Redirection vers le frontend approprié
        if (str_starts_with($path, 'admin')) {
            return Redirect::to(config('app.frontend_web_path', '/systeme-gestion-tickets-frontend'));
        } elseif (str_starts_with($path, 'mobile')) {
            return Redirect::to(config('app.frontend_mobile_path', '/mobile'));
        }

        return $next($request);
    }
}
