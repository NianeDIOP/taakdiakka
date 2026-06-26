<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isBlocked()) {
                $reason = $user->status === 'banned' ? 'Votre compte a été banni.' : 'Votre compte est suspendu.';
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors(['email' => $reason]);
            }

            // Limite à une écriture par minute
            if (! $user->last_seen_at || $user->last_seen_at->lt(now()->subMinute())) {
                $user->forceFill(['last_seen_at' => now()])->saveQuietly();
            }
        }

        return $next($request);
    }
}
