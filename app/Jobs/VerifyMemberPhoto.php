<?php

namespace App\Jobs;

use App\Models\AppNotification;
use App\Models\ProfilePhoto;
use App\Models\User;
use App\Support\HumanImage;
use App\Support\ImageOptimizer;
use App\Support\Notifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Vérifie en arrière-plan qu'une photo téléversée montre bien un visage humain.
 * Exécuté hors requête HTTP (pas de limite de temps), il retire la photo et
 * prévient le membre si aucun visage n'est détecté. Les uploads ne sont donc
 * jamais bloqués/ralentis par la détection.
 */
class VerifyMemberPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;

    /** @param string $kind 'profile' | 'gallery' */
    public function __construct(
        public int $userId,
        public string $photoFile,
        public string $kind,
        public ?int $galleryId = null,
    ) {}

    public function handle(): void
    {
        @set_time_limit(0);

        $path = public_path('img/' . $this->photoFile);
        if (! is_file($path)) {
            return;
        }

        // Visage détecté → rien à faire
        if (HumanImage::hasFace($path)) {
            return;
        }

        $user = User::find($this->userId);
        if (! $user) {
            return;
        }

        // Retrait de la photo non conforme
        if ($this->kind === 'gallery' && $this->galleryId) {
            $photo = ProfilePhoto::find($this->galleryId);
            if ($photo && $photo->user_id === $user->id) {
                ImageOptimizer::delete($photo->path);
                $photo->delete();
            }
        } elseif ($this->kind === 'profile') {
            if ($user->profile && $user->profile->photo === $this->photoFile) {
                $user->profile->update(['photo' => null]);
            }
            $user->photos()->where('path', $this->photoFile)->get()->each(fn ($p) => $p->delete());
            ImageOptimizer::delete($this->photoFile);
        }

        // Notifie le membre (in-app + e-mail si activé)
        AppNotification::record(
            $user->id, null, 'system',
            'Votre photo a été retirée : aucun visage net n\'a pu être détecté. Merci d\'envoyer une vraie photo de vous.',
            route('profile.edit'),
        );
        Notifier::email(
            $user,
            'Votre photo doit être reprise',
            'Photo non validée',
            ['Votre photo n\'a pas pu être validée car aucun visage humain net n\'y a été détecté.',
             'Merci d\'envoyer une photo claire montrant bien votre visage.'],
            'Mettre à jour ma photo',
            route('profile.edit'),
        );
    }
}
