<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Profile;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{

    /** Affiche l'étape en cours du parcours d'accueil (1: profil, 2: photo, 3: demande). */
    public function show(Request $request)
    {
        $step = (int) $request->input('step', 1);
        $step = max(1, min(3, $step));

        $user = $request->user();
        $profile = $user->profileOrNew();

        return view('espace.onboarding', [
            'step'    => $step,
            'profile' => $profile,
            'options' => Profile::OPTIONS,
            'user'    => $user,
        ]);
    }

    /** Étape 1 : informations essentielles du profil. */
    public function saveProfile(Request $request)
    {
        $o = Profile::OPTIONS;

        $data = $request->validate([
            'gender'     => ['required', Rule::in($o['gender'])],
            'birthdate'  => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
            'region'     => ['required', Rule::in($o['region'])],
            'religion'   => ['nullable', Rule::in($o['religion'])],
            'practice'   => ['nullable', Rule::in($o['practice'])],
            'profession' => ['nullable', 'string', 'max:120'],
            'bio'        => ['nullable', 'string', 'max:600'],
        ], [], [
            'gender' => 'genre', 'birthdate' => 'date de naissance', 'region' => 'région',
        ]);

        $data['seeking'] = $data['gender'] === 'Homme' ? 'Une épouse' : 'Un époux';

        $request->user()->profileOrNew()->update($data);

        return redirect()->route('onboarding', ['step' => 2]);
    }

    /** Étape 2 : photo de profil (optionnelle). */
    public function savePhoto(Request $request)
    {
        $request->validate([
            'photo'      => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'photo_data' => ['nullable', 'string'],
        ]);

        $prefix = 'p' . $request->user()->id;
        $photo = null;

        if ($request->filled('photo_data') && str_starts_with($request->input('photo_data'), 'data:image')) {
            $photo = ImageOptimizer::fromBase64($request->input('photo_data'), $prefix);
        } elseif ($request->hasFile('photo')) {
            $photo = ImageOptimizer::fromUpload($request->file('photo'), $prefix);
        }

        if ($photo) {
            $user = $request->user();
            $user->profileOrNew()->update(['photo' => $photo]);
            // La photo d'accueil alimente aussi la galerie
            $user->photos()->firstOrCreate(['path' => $photo]);
            // Vérification « visage humain » en arrière-plan
            \App\Jobs\VerifyMemberPhoto::dispatch($user->id, $photo, 'profile');
        }

        return redirect()->route('onboarding', ['step' => 3]);
    }

    /** Étape 3 : active la demande et termine le parcours. */
    public function finish(Request $request)
    {
        $user = $request->user();

        if ($user->profileOrNew()->gender) {
            Demande::activateFor($user->fresh());
        }

        return redirect()->route('dashboard')
            ->with('status', 'Votre profil est prêt — bienvenue parmi nous ! Qu\'Allah facilite votre union 🤲');
    }
}
