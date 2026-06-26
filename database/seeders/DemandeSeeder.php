<?php

namespace Database\Seeders;

use App\Models\Demande;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemandeSeeder extends Seeder
{
    public function run(): void
    {
        Demande::query()->whereNull('user_id')->delete();

        $women = ['Awa', 'Ndèye', 'Fatou', 'Aïssatou', 'Khadija', 'Rama', 'Mariama', 'Coumba', 'Bineta', 'Sokhna', 'Adja', 'Maïmouna'];
        $men   = ['Modou', 'Ibrahima', 'Cheikh', 'Ousmane', 'Mamadou', 'Abdou', 'Pape', 'Serigne', 'Alioune', 'Babacar', 'Moussa', 'Lamine'];
        $regions = ['Dakar, Sénégal', 'Thiès, Sénégal', 'Saint-Louis, Sénégal', 'Touba, Sénégal', 'Ziguinchor, Sénégal', 'Rufisque, Sénégal', 'Paris · Diaspora', 'Milan · Diaspora', 'Montréal · Diaspora'];
        $profW = ['Enseignante', 'Infirmière', 'Commerçante', 'Comptable', 'Sage-femme', 'Juriste', 'Pharmacienne', 'Couturière'];
        $profM = ['Ingénieur', 'Entrepreneur', 'Médecin', 'Commerçant', 'Fonctionnaire', 'Informaticien', 'Professeur', 'Technicien'];
        $quotesW = [
            'Croyante et posée, je recherche un homme sincère et respectueux pour bâtir un foyer.',
            'Douce et travailleuse, je rêve d\'un foyer paisible avec un homme honnête et patient.',
            'Attachée à mes valeurs, je souhaite une union bénie fondée sur le respect mutuel.',
            'Bienveillante et de bonne intention, je cherche une relation sérieuse menant au mariage.',
        ];
        $quotesM = [
            'Ambitieux et croyant, je cherche une épouse complice pour avancer ensemble, dans la foi.',
            'Installé et stable, je souhaite fonder une famille unie avec une personne sincère.',
            'Simple et sérieux, je recherche une compagne attachée à nos valeurs et à la famille.',
            'Respectueux et posé, je souhaite une union sincère et bénie, inch\'Allah.',
        ];
        $tagsPool = ['Première union', 'Pratiquant(e)', 'Fonder une famille', 'Diaspora', 'Discret', 'Vérifié Or'];
        $levels = ['Bronze', 'Argent', 'Or'];
        $photosW = ['profil-w1.png', 'profil-w2.png', 'profil-w3.png', 'profil-w4.png'];
        $photosM = ['profil-m1.png', 'profil-m2.png', 'profil-m3.png'];

        for ($i = 0; $i < 24; $i++) {
            $isW = $i % 2 === 0;
            $idx = intdiv($i, 2);
            $discret = $i % 3 === 2; // ~1/3 en profil discret
            $region = $regions[$i % count($regions)];

            Demande::create([
                'user_id'            => null,
                'name'               => $discret ? null : ($isW ? $women[$idx] : $men[$idx]),
                'age'                => 24 + ($i * 7) % 20,
                'seeking'            => $isW ? 'Un époux' : 'Une épouse',
                'profession'         => $discret ? null : ($isW ? $profW[$i % 8] : $profM[$i % 8]),
                'region'             => $region,
                'quote'              => $isW ? $quotesW[$i % 4] : $quotesM[$i % 4],
                'tags'               => array_values(array_unique([
                    $tagsPool[$i % 5],
                    str_contains($region, 'Diaspora') ? 'Diaspora' : 'Première union',
                ])),
                'photo'              => $discret ? null : ($isW ? $photosW[$idx % 4] : $photosM[$idx % 3]),
                'is_discret'         => $discret,
                'is_verified'        => true,
                'verification_level' => $levels[$i % 3],
                'published_at'       => Carbon::now()->subDays($i)->subHours(($i * 3) % 24),
            ]);
        }
    }
}
