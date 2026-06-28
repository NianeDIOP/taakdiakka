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

            if (! $user->last_seen_at || $user->last_seen_at->lt(now()->subMinute())) {
                $ua = $request->userAgent() ?? '';
                $device = preg_match('/Mobile|Android|iPhone|iPad/i', $ua) ? 'mobile' : 'desktop';
                $browser = $this->detectBrowser($ua);

                $user->forceFill([
                    'last_seen_at' => now(),
                    'last_device'  => $device,
                    'last_browser' => $browser,
                    'last_ip'      => $request->ip(),
                ])->saveQuietly();
            }
        }

        return $next($request);
    }

    private function detectBrowser(string $ua): string
    {
        if (preg_match('/Edg\//i', $ua)) return 'Edge';
        if (preg_match('/OPR|Opera/i', $ua)) return 'Opera';
        if (preg_match('/Chrome/i', $ua)) return 'Chrome';
        if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) return 'Safari';
        if (preg_match('/Firefox/i', $ua)) return 'Firefox';
        if (preg_match('/MSIE|Trident/i', $ua)) return 'IE';
        return 'Autre';
    }
}
