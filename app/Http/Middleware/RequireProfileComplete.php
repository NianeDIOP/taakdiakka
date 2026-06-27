<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Oblige un membre fraîchement inscrit à compléter l'essentiel de son profil
 * (genre, date de naissance, région) avant d'accéder au reste du site.
 * Tant que ce n'est pas fait, toute page le renvoie sur l'onboarding (/bienvenue),
 * sauf l'onboarding lui-même, l'édition du profil et la déconnexion.
 */
class RequireProfileComplete
{
    /** Routes accessibles même si le profil n'est pas complété. */
    private const ALLOWED = [
        'onboarding', 'onboarding.*',
        'profile.show', 'profile.edit', 'profile.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdminUser() && ! $request->routeIs(self::ALLOWED)) {
            $p = $user->profile;
            $complete = $p && filled($p->gender) && filled($p->birthdate) && filled($p->region);

            if (! $complete) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Profil à compléter.'], 423);
                }

                return redirect()->route('onboarding')
                    ->with('status', 'Bienvenue ! Complétez d\'abord votre profil pour accéder à la plateforme. 🤲');
            }
        }

        return $next($request);
    }
}
