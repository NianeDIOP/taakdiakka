<?php

namespace App\Http\Middleware;

use App\Support\FeatureGate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /** Usage : ->middleware('module:community'). Les admins gardent l'accès. */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! FeatureGate::moduleEnabled($module) && ! ($request->user()?->isAdminUser())) {
            if ($request->expectsJson()) {
                abort(404);
            }

            return redirect()->route('home')->with('status', 'Cette fonctionnalité est temporairement indisponible.');
        }

        return $next($request);
    }
}
