<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Setting::enabled('site.maintenance', false)) {
            return $next($request);
        }

        // Les admins gardent l'accès ; l'authentification reste possible.
        if ($request->user()?->isAdminUser()) {
            return $next($request);
        }

        $allowed = ['login', 'logout', 'register', 'password.request', 'password.email', 'password.reset', 'password.update'];
        if ($request->routeIs(...$allowed) || $request->is('admin', 'admin/*')) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'siteName' => Setting::get('site.name', 'TàakDiàkka'),
        ], 503);
    }
}
