<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /** Tableau de bord — vue d'ensemble. */
    public function dashboard()
    {
        $user = auth()->user();
        $profile = $user->profileOrNew();

        $stats = [
            'matchs'    => $user->matchedUsers()->count(),
            'interests' => $user->interestsReceived()->count(),
            'visitors'  => $user->profileVisitors()->count(),
            'followers' => $user->followers()->count(),
        ];

        return view('espace.dashboard', compact('profile', 'stats'));
    }

    /** Vue lecture « Mon profil » (infos renseignées). */
    public function show()
    {
        $profile = auth()->user()->profileOrNew();

        return view('espace.profil-show', compact('profile'));
    }

    /** Formulaire « Modifier mon profil ». */
    public function edit()
    {
        $profile = auth()->user()->profileOrNew();

        return view('espace.profil', [
            'profile'   => $profile,
            'options'   => Profile::OPTIONS,
            'templates' => Profile::BIO_TEMPLATES,
            'photos'    => auth()->user()->photos,
            'maxPhotos' => \App\Http\Controllers\GalleryController::MAX_PHOTOS,
        ]);
    }

    /** Enregistrement du profil. */
    public function update(Request $request)
    {
        $o = Profile::OPTIONS;

        $data = $request->validate([
            'gender'         => ['nullable', Rule::in($o['gender'])],
            'birthdate'      => ['nullable', 'date', 'before:' . now()->subYears(18)->toDateString()],
            'religion'       => ['nullable', Rule::in($o['religion'])],
            'practice'       => ['nullable', Rule::in($o['practice'])],
            'marital_status' => ['nullable', Rule::in($o['marital_status'])],
            'children_count' => ['nullable', 'integer', 'min:0', 'max:15'],
            'wants_children' => ['nullable', Rule::in($o['wants_children'])],
            'union_type'     => ['nullable', Rule::in($o['union_type'])],
            'education'      => ['nullable', Rule::in($o['education'])],
            'profession'     => ['nullable', 'string', 'max:120'],
            'languages'      => ['nullable', 'array'],
            'languages.*'    => [Rule::in($o['languages'])],
            'height_cm'      => ['nullable', 'integer', 'min:120', 'max:220'],
            'complexion'     => ['nullable', Rule::in($o['complexion'])],
            'region'         => ['nullable', Rule::in($o['region'])],
            'bio'            => ['nullable', 'string', 'max:600'],
            'photo'          => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'photo_data'     => ['nullable', 'string'],
        ], [], [
            'birthdate' => 'date de naissance',
        ]);

        // Champs déduits automatiquement
        $data['has_children'] = ! is_null($request->input('children_count'))
            ? (int) $request->input('children_count') > 0
            : null;
        $data['seeking'] = match ($data['gender'] ?? null) {
            'Homme' => 'Une épouse',
            'Femme' => 'Un époux',
            default => null,
        };

        // Photo : capture caméra (base64) prioritaire, sinon fichier téléversé.
        $prefix = 'p' . auth()->id();
        if ($request->filled('photo_data') && str_starts_with($request->input('photo_data'), 'data:image')) {
            if ($p = ImageOptimizer::fromBase64($request->input('photo_data'), $prefix)) {
                $data['photo'] = $p;
            }
        } elseif ($request->hasFile('photo')) {
            if ($p = ImageOptimizer::fromUpload($request->file('photo'), $prefix)) {
                $data['photo'] = $p;
            }
        }

        $profile = auth()->user()->profileOrNew();
        $profile->update($data);

        // Vérification « visage humain » en arrière-plan si une nouvelle photo a été fournie
        if (! empty($data['photo'])) {
            \App\Jobs\VerifyMemberPhoto::dispatch(auth()->id(), $data['photo'], 'profile');
        }

        // La demande de mariage s'active / se synchronise automatiquement depuis le profil
        \App\Models\Demande::activateFor(auth()->user()->fresh());

        return redirect()->route('profile.show')
            ->with('status', 'Profil enregistré — complété à ' . $profile->fresh()->completion . '%. 🤲');
    }
}
