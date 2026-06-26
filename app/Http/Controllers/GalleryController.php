<?php

namespace App\Http\Controllers;

use App\Models\ProfilePhoto;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public const MAX_PHOTOS = 6;

    /** Ajoute une photo à la galerie du membre. */
    public function store(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ], [], ['photo' => 'photo']);

        $user = $request->user();

        if ($user->photos()->count() >= self::MAX_PHOTOS) {
            return back()->with('status', 'Galerie pleine — ' . self::MAX_PHOTOS . ' photos maximum.');
        }

        $name = ImageOptimizer::fromUpload($request->file('photo'), 'g' . $user->id);

        if (! $name) {
            return back()->with('status', "Image illisible — réessayez avec un autre fichier.");
        }

        $photo = $user->photos()->create(['path' => $name]);

        // Vérification « visage humain » en arrière-plan (retrait + notification si invalide)
        \App\Jobs\VerifyMemberPhoto::dispatch($user->id, $name, 'gallery', $photo->id);

        return back()->with('status', 'Photo ajoutée à votre galerie. ✨');
    }

    /** Retire une photo de la galerie. */
    public function destroy(ProfilePhoto $photo)
    {
        abort_unless($photo->user_id === auth()->id(), 403);

        ImageOptimizer::delete($photo->path);
        $photo->delete();

        return back()->with('status', 'Photo retirée de votre galerie.');
    }
}
