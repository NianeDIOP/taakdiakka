<?php

namespace Database\Seeders;

use App\Models\SuccessStory;
use Illuminate\Database\Seeder;

class SuccessStorySeeder extends Seeder
{
    public function run(): void
    {
        SuccessStory::query()->delete();

        $stories = [
            ['couple' => 'Awa & Modou', 'initials' => 'AM', 'location' => 'Dakar · Sénégal',
             'badge_label' => 'Mariés en 2025', 'badge_icon' => 'rings', 'badge_heart' => false,
             'quote' => 'Trois mois après notre premier message, il demandait ma main.'],

            ['couple' => 'Fatou & Cheikh', 'initials' => 'FC', 'location' => 'Paris · Diaspora',
             'badge_label' => 'Fiancés', 'badge_icon' => 'heart', 'badge_heart' => true,
             'quote' => 'La compatibilité affichait 94%. Le destin avait raison.'],

            ['couple' => 'Aïcha & Ibrahima', 'initials' => 'AI', 'location' => 'Thiès · Sénégal',
             'badge_label' => 'Mariés en 2024', 'badge_icon' => 'rings', 'badge_heart' => false,
             'quote' => 'Une plateforme sérieuse, des intentions claires. Tout a été simple.'],

            ['couple' => 'Mariama & Ousmane', 'initials' => 'MO', 'location' => 'Saint-Louis · Sénégal',
             'badge_label' => 'Mariés en 2025', 'badge_icon' => 'rings', 'badge_heart' => false,
             'quote' => 'Nous cherchions la même chose : une union sincère et bénie. Alhamdoulillah.'],

            ['couple' => 'Khady & Abdou', 'initials' => 'KA', 'location' => 'Rufisque · Sénégal',
             'badge_label' => 'Fiancés', 'badge_icon' => 'heart', 'badge_heart' => true,
             'quote' => 'Nos familles se sont rencontrées le mois dernier. Tout est allé naturellement.'],

            ['couple' => 'Sokhna & Babacar', 'initials' => 'SB', 'location' => 'Touba · Sénégal',
             'badge_label' => 'Mariés en 2023', 'badge_icon' => 'rings', 'badge_heart' => false,
             'quote' => 'Deux ans de bonheur, et un premier enfant. Merci TàakDiàkka.'],

            ['couple' => 'Bineta & Alioune', 'initials' => 'BA', 'location' => 'Milan · Diaspora',
             'badge_label' => 'Fiancés', 'badge_icon' => 'heart', 'badge_heart' => true,
             'quote' => 'La distance n\'a pas résisté à la sincérité de nos échanges.'],

            ['couple' => 'Rama & Serigne', 'initials' => 'RS', 'location' => 'Dakar · Sénégal',
             'badge_label' => 'Mariés en 2025', 'badge_icon' => 'rings', 'badge_heart' => false,
             'quote' => 'Des valeurs communes, une même vision : tout a été clair dès le début.'],

            ['couple' => 'Adja & Moussa', 'initials' => 'AM', 'location' => 'Ziguinchor · Sénégal',
             'badge_label' => 'Mariés en 2024', 'badge_icon' => 'rings', 'badge_heart' => false,
             'quote' => 'Nos familles étaient présentes du premier message au grand jour.'],
        ];

        foreach ($stories as $s) {
            SuccessStory::create($s);
        }
    }
}
