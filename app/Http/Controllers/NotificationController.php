<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    /** Liste des notifications + marque les non-message comme lues. */
    public function index()
    {
        $me = auth()->user();
        $notifications = $me->notifications()->with('actor.profile')->take(50)->get();

        // Marque comme lues les notifications affichées (hors messages, gérés côté messagerie)
        $me->notifications()->whereNull('read_at')->where('type', '!=', 'message')->update(['read_at' => now()]);

        return view('espace.notifications', compact('notifications'));
    }
}
