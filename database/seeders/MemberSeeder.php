<?php

namespace Database\Seeders;

use App\Models\Demande;
use App\Models\Profile;
use App\Models\ProfilePhoto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent : supprime les membres de démonstration précédents (cascade profils/demandes/photos)
        User::query()->where('email', 'like', '%@membre.taakdiakka.test')->delete();
        // Nettoie aussi les anciennes demandes anonymes (on passe au tout-membres)
        Demande::query()->whereNull('user_id')->delete();

        $women = ['Awa Diop', 'Ndèye Fall', 'Fatou Sarr', 'Aïssatou Ba', 'Khadija Ndiaye', 'Rama Gueye',
            'Mariama Sow', 'Coumba Diallo', 'Bineta Faye', 'Sokhna Mbaye', 'Adja Cissé', 'Maïmouna Touré',
            'Astou Diouf', 'Ndella Sène', 'Yacine Kane', 'Dior Camara', 'Penda Bâ', 'Fatim Niang',
            'Salimata Diédhiou', 'Oumou Wade', 'Seynabou Dièye', 'Marème Lô', 'Aida Mbodj', 'Nafissatou Sall'];
        $men = ['Modou Ndiaye', 'Ibrahima Sarr', 'Cheikh Ba', 'Ousmane Diop', 'Mamadou Fall', 'Abdou Gueye',
            'Pape Sow', 'Serigne Mbacké', 'Alioune Diallo', 'Babacar Faye', 'Moussa Cissé', 'Lamine Touré',
            'Mor Diagne', 'Samba Kâ', 'Assane Sy', 'Daouda Diatta', 'Saliou Mbengue', 'Khalifa Seck',
            'Malick Thiam', 'Demba Ndour', 'El Hadji Gaye', 'Souleymane Dia', 'Boubacar Baldé', 'Ismaïl Sané'];

        $regions   = ['Dakar', 'Thiès', 'Saint-Louis', 'Touba', 'Ziguinchor', 'Rufisque', 'Diaspora'];
        $profW     = ['Enseignante', 'Infirmière', 'Commerçante', 'Comptable', 'Sage-femme', 'Juriste', 'Pharmacienne', 'Couturière'];
        $profM     = ['Ingénieur', 'Entrepreneur', 'Médecin', 'Commerçant', 'Fonctionnaire', 'Informaticien', 'Professeur', 'Technicien'];
        $religions = ['Islam', 'Islam', 'Islam', 'Christianisme'];
        $practices = ['Pratiquant(e)', 'Modéré(e)', 'Pratiquant(e)', 'Modéré(e)'];
        $marital   = ['Célibataire', 'Célibataire', 'Célibataire', 'Divorcé(e)', 'Veuf(ve)'];
        $wants     = ['Oui', 'Oui', 'Plus tard', 'Non'];
        $unions    = ['Monogame', 'Monogame', 'Indifférent', 'Polygame'];
        $educ      = ['Baccalauréat', 'Bac +2', 'Licence', 'Master', 'Doctorat', 'Brevet / BFEM'];
        $complex   = ['Clair', 'Caramel', 'Foncé'];
        $langsPool = ['Wolof', 'Français', 'Anglais', 'Arabe', 'Pulaar', 'Sérère', 'Diola'];
        $levels    = ['Bronze', 'Argent', 'Or'];
        $tagsPool  = ['Première union', 'Pratiquant(e)', 'Fonder une famille', 'Diaspora', 'Vérifié Or'];

        $bioW = [
            "Croyante et posée, je recherche un homme sincère et respectueux pour bâtir un foyer béni.",
            "Douce et travailleuse, je rêve d'un foyer paisible avec un homme honnête et patient.",
            "Attachée à mes valeurs, je souhaite une union bénie fondée sur le respect mutuel et la foi.",
            "Bienveillante et de bonne intention, je cherche une relation sérieuse menant au mariage.",
        ];
        $bioM = [
            "Ambitieux et croyant, je cherche une épouse complice pour avancer ensemble, dans la foi.",
            "Installé et stable, je souhaite fonder une famille unie avec une personne sincère.",
            "Simple et sérieux, je recherche une compagne attachée à nos valeurs et à la famille.",
            "Respectueux et posé, je souhaite une union sincère et bénie, inch'Allah.",
        ];

        $photosW = ['profil-w1.jpg', 'profil-w2.jpg', 'profil-w3.jpg', 'profil-w4.jpg'];
        $photosM = ['profil-m1.jpg', 'profil-m2.jpg', 'profil-m3.jpg'];

        $all = [];
        foreach ($women as $i => $name) {
            $all[] = ['name' => $name, 'gender' => 'Femme', 'i' => $i];
        }
        foreach ($men as $i => $name) {
            $all[] = ['name' => $name, 'gender' => 'Homme', 'i' => $i];
        }

        foreach ($all as $k => $m) {
            $isW = $m['gender'] === 'Femme';
            $i = $m['i'];
            $first = Str::before($m['name'], ' ');
            $slug = Str::slug(Str::ascii($m['name']), '.');
            $age = $isW ? 22 + ($i * 3) % 17 : 26 + ($i * 3) % 20;

            $user = User::create([
                'name'     => $m['name'],
                'email'    => $slug . '@membre.taakdiakka.test',
                'password' => Hash::make('password123'),
            ]);

            $region = $regions[$k % count($regions)];
            $photo  = $isW ? $photosW[$i % 4] : $photosM[$i % 3];

            $user->profile()->create([
                'gender'         => $m['gender'],
                'birthdate'      => Carbon::now()->subYears($age)->subDays(($i * 37) % 360),
                'religion'       => $religions[$k % 4],
                'practice'       => $practices[$k % 4],
                'marital_status' => $marital[$k % 5],
                'children_count' => $k % 4 === 3 ? ($k % 3) : 0,
                'has_children'   => $k % 4 === 3 && ($k % 3) > 0,
                'wants_children' => $wants[$k % 4],
                'union_type'     => $unions[$k % 4],
                'education'      => $educ[$k % 6],
                'profession'     => $isW ? $profW[$i % 8] : $profM[$i % 8],
                'languages'      => array_values(array_slice($langsPool, 0, 2 + $k % 3)),
                'height_cm'      => $isW ? 158 + ($i * 2) % 20 : 170 + ($i * 2) % 20,
                'complexion'     => $complex[$k % 3],
                'region'         => $region,
                'bio'            => $isW ? $bioW[$i % 4] : $bioM[$i % 4],
                'seeking'        => $isW ? 'Un époux' : 'Une épouse',
                'photo'          => $photo,
            ]);

            // Demande publique adossée au membre (annonce de mariage)
            Demande::create([
                'user_id'            => $user->id,
                'name'               => $first,
                'age'                => $age,
                'seeking'            => $isW ? 'Un époux' : 'Une épouse',
                'profession'         => $isW ? $profW[$i % 8] : $profM[$i % 8],
                'region'             => $region . ($region === 'Diaspora' ? '' : ', Sénégal'),
                'quote'              => $isW ? $bioW[$i % 4] : $bioM[$i % 4],
                'tags'               => array_values(array_unique([
                    $tagsPool[$k % 5],
                    $region === 'Diaspora' ? 'Diaspora' : 'Première union',
                ])),
                'photo'              => $photo,
                'is_discret'         => false,
                'is_verified'        => true,
                'verification_level' => $levels[$k % 3],
                'published_at'       => Carbon::now()->subDays($k)->subHours(($k * 3) % 24),
            ]);

            // Galerie : ~1 membre sur 2 a 2 photos supplémentaires (même genre)
            if ($k % 2 === 0) {
                $pool = $isW ? $photosW : $photosM;
                for ($g = 1; $g <= 2; $g++) {
                    $user->photos()->create(['path' => $pool[($i + $g) % count($pool)]]);
                }
            }
        }
    }
}
