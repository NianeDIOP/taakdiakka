<?php

namespace Database\Seeders;

use App\Models\Demande;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Demande::query()->delete();

        $demandes = [
            [
                'name' => 'Awa', 'age' => 28, 'seeking' => 'Un époux', 'profession' => 'Enseignante',
                'region' => 'Dakar, Sénégal',
                'quote' => 'Croyante et posée, je recherche un homme sincère et respectueux pour bâtir un foyer.',
                'tags' => ['Première union', 'Pratiquante'], 'photo' => 'profil-1.png',
                'is_discret' => false, 'verification_level' => 'Or', 'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'name' => null, 'age' => 34, 'seeking' => 'Une épouse', 'profession' => 'Commerçant',
                'region' => 'Touba, Sénégal',
                'quote' => 'Commerçant établi, je souhaite une union bénie. Discrétion et sérieux, dans le respect des familles.',
                'tags' => ['Cherche épouse', 'Discret'], 'photo' => null,
                'is_discret' => true, 'verification_level' => 'Argent', 'published_at' => Carbon::now()->subDay(),
            ],
            [
                'name' => 'Ibrahima', 'age' => 36, 'seeking' => 'Une épouse', 'profession' => 'Ingénieur',
                'region' => 'Paris · Diaspora',
                'quote' => 'Installé en France, je cherche une compagne pour la vie, attachée à nos valeurs.',
                'tags' => ['Diaspora', 'Vérifié Or'], 'photo' => 'profil-2.png',
                'is_discret' => false, 'verification_level' => 'Or', 'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'name' => 'Ndèye', 'age' => 26, 'seeking' => 'Un époux', 'profession' => 'Infirmière',
                'region' => 'Thiès, Sénégal',
                'quote' => 'Douce et travailleuse, je rêve d\'un foyer paisible avec un homme honnête et patient.',
                'tags' => ['Première union', 'Fonder une famille'], 'photo' => 'profil-3.png',
                'is_discret' => false, 'verification_level' => 'Argent', 'published_at' => Carbon::now()->subDays(4),
            ],
            [
                'name' => null, 'age' => 30, 'seeking' => 'Un époux', 'profession' => 'Fonctionnaire',
                'region' => 'Saint-Louis, Sénégal',
                'quote' => 'Fonctionnaire, croyante, je privilégie la discrétion. Je cherche un homme sérieux en vue du mariage.',
                'tags' => ['Cherche époux', 'Pratiquante'], 'photo' => null,
                'is_discret' => true, 'verification_level' => 'Argent', 'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'name' => 'Cheikh', 'age' => 31, 'seeking' => 'Une épouse', 'profession' => 'Entrepreneur',
                'region' => 'Dakar, Sénégal',
                'quote' => 'Ambitieux et croyant, je cherche une épouse complice pour avancer ensemble, dans la foi.',
                'tags' => ['Première union', 'Pratiquant'], 'photo' => 'profil-4.png',
                'is_discret' => false, 'verification_level' => 'Or', 'published_at' => Carbon::now()->subDays(6),
            ],
        ];

        foreach ($demandes as $d) {
            Demande::create($d);
        }
    }
}
