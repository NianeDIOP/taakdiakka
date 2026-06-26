<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Les comptes administrateur/modérateur n'ont pas de profil membre : ils ne
 * doivent pas accéder à l'espace membre. On les renvoie vers le back-office.
 */
class DenyAdminFromMemberArea
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
