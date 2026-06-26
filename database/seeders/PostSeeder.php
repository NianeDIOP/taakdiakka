<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        Post::query()->delete();

        $posts = [
            [
                'author_name' => null, 'is_anonymous' => true, 'author_verified' => false,
                'theme' => 'Confession', 'theme_emoji' => '🌙', 'location' => 'Dakar',
                'body' => "J'ai 34 ans. Je souhaite me marier, mais je n'ai pas encore les moyens d'organiser une grande cérémonie. Est-ce que la sincérité suffit aujourd'hui ? 🤲",
                'hearts' => 248, 'replies' => 62, 'reactions' => ['❤️', '🤲', '🌹'],
                'comments' => [
                    ['name' => 'Fatou D.', 'verified' => true, 'body' => 'La sincérité passe avant tout. Une union sincère vaut mille cérémonies. Reste fidèle à tes valeurs. ❤️', 'likes' => 84],
                    ['name' => 'Ibrahima S.', 'verified' => true, 'body' => "Beaucoup de mariages réussis ont commencé simplement. L'essentiel, c'est l'intention. Courage 💪🤲", 'likes' => 51],
                ],
                'published_at' => Carbon::now()->subHours(3),
            ],
            [
                'author_name' => 'Maïmouna F.', 'is_anonymous' => false, 'author_verified' => true,
                'theme' => 'Témoignage', 'theme_emoji' => '💍', 'location' => 'Rufisque',
                'body' => "Alhamdoulillah, fiancée depuis hier à quelqu'un rencontré ici. La bienveillance de cette communauté m'a portée tout au long du chemin. 🤲💍✨",
                'hearts' => 1204, 'replies' => 203, 'reactions' => ['❤️', '🎉', '😍'],
                'comments' => [
                    ['name' => 'Khady B.', 'verified' => false, 'body' => "Quelle belle nouvelle, qu'Allah bénisse votre union ! 🎉🤲", 'likes' => 67],
                ],
                'published_at' => Carbon::now()->subHours(16),
            ],
            [
                'author_name' => 'Ousmane D.', 'is_anonymous' => false, 'author_verified' => true,
                'theme' => 'Conseil', 'theme_emoji' => '💡', 'location' => 'Diaspora · Italie',
                'body' => "La dot, la cérémonie, l'implication des familles… La transparence dès le départ évite bien des malentendus. Quel est votre regard ? 🤝",
                'hearts' => 540, 'replies' => 128, 'reactions' => ['👍', '🤝', '🙏'],
                'comments' => [
                    ['name' => 'Aïssatou N.', 'verified' => true, 'body' => 'Tellement vrai. La transparence évite bien des déceptions. Merci pour ce rappel 🙏', 'likes' => 38],
                ],
                'published_at' => Carbon::now()->subDay(),
            ],
            [
                'author_name' => null, 'is_anonymous' => true, 'author_verified' => false,
                'theme' => 'Question', 'theme_emoji' => '💬', 'location' => 'Thiès',
                'body' => "Comment savoir si je suis vraiment prêt(e) au mariage ? J'ai peur de m'engager trop vite. Vos expériences m'aideraient 🙏",
                'hearts' => 176, 'replies' => 47, 'reactions' => ['🤲', '💭', '❤️'],
                'comments' => [
                    ['name' => 'Mamadou S.', 'verified' => true, 'body' => "Personne n'est jamais « totalement » prêt. La bonne personne te rassure. Prie et avance avec confiance.", 'likes' => 29],
                ],
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'author_name' => 'Cheikh M.', 'is_anonymous' => false, 'author_verified' => true,
                'theme' => 'Conseil', 'theme_emoji' => '💡', 'location' => 'Dakar',
                'body' => "Pour une première rencontre réussie : la sincérité, le respect, et impliquer les familles tôt. Pas de précipitation. 🤝",
                'hearts' => 312, 'replies' => 41, 'reactions' => ['👍', '🤝', '✨'],
                'comments' => [],
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'author_name' => 'Ndèye S.', 'is_anonymous' => false, 'author_verified' => true,
                'theme' => 'Témoignage', 'theme_emoji' => '💍', 'location' => 'Saint-Louis',
                'body' => "Après une déception, j'avais perdu espoir. Cette communauté m'a redonné confiance. Aujourd'hui, je suis fiancée. 🌷",
                'hearts' => 758, 'replies' => 96, 'reactions' => ['❤️', '🌷', '🎉'],
                'comments' => [
                    ['name' => 'Rama G.', 'verified' => false, 'body' => 'Mabrouk ! Ton parcours est une inspiration 🤍', 'likes' => 22],
                ],
                'published_at' => Carbon::now()->subDays(4),
            ],
            [
                'author_name' => null, 'is_anonymous' => true, 'author_verified' => false,
                'theme' => 'Confession', 'theme_emoji' => '🌙', 'location' => 'Touba',
                'body' => "J'ai 29 ans et ma famille me presse de me marier. Comment concilier leurs attentes et mon propre rythme ? 🤲",
                'hearts' => 204, 'replies' => 58, 'reactions' => ['🤲', '❤️', '💭'],
                'comments' => [
                    ['name' => 'Sokhna D.', 'verified' => true, 'body' => 'Communique avec douceur. Le mariage est ta vie : prends le temps qu\'il te faut.', 'likes' => 41],
                ],
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'author_name' => 'Abdou K.', 'is_anonymous' => false, 'author_verified' => true,
                'theme' => 'Conseil', 'theme_emoji' => '💡', 'location' => 'Diaspora · France',
                'body' => "Pour la diaspora : impliquez vos familles par appel vidéo tôt. Ça rassure tout le monde et accélère les choses. 🌍",
                'hearts' => 421, 'replies' => 73, 'reactions' => ['👍', '🌍', '🤝'],
                'comments' => [],
                'published_at' => Carbon::now()->subDays(6),
            ],
            [
                'author_name' => 'Maïmouna B.', 'is_anonymous' => false, 'author_verified' => true,
                'theme' => 'Témoignage', 'theme_emoji' => '💍', 'location' => 'Rufisque',
                'body' => "Mariée il y a 6 mois. Le secret ? Des intentions claires dès le premier échange. Qu'Allah vous facilite à tous. ✨",
                'hearts' => 893, 'replies' => 142, 'reactions' => ['❤️', '🎉', '🤲'],
                'comments' => [
                    ['name' => 'Pape S.', 'verified' => true, 'body' => 'BarakAllah fik, beau témoignage 🤲', 'likes' => 33],
                ],
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'author_name' => null, 'is_anonymous' => true, 'author_verified' => false,
                'theme' => 'Question', 'theme_emoji' => '💬', 'location' => 'Dakar',
                'body' => "Faut-il parler de ses projets d'enfants dès les premières discussions ? Je ne veux pas brusquer mais c'est important pour moi.",
                'hearts' => 167, 'replies' => 39, 'reactions' => ['💭', '🤲', '👶'],
                'comments' => [
                    ['name' => 'Khadija T.', 'verified' => false, 'body' => 'Oui, avec tact. Mieux vaut être alignés tôt sur l\'essentiel.', 'likes' => 26],
                ],
                'published_at' => Carbon::now()->subDays(8),
            ],
            [
                'author_name' => 'Serigne F.', 'is_anonymous' => false, 'author_verified' => true,
                'theme' => 'Conseil', 'theme_emoji' => '💡', 'location' => 'Thiès',
                'body' => "Un profil vérifié change tout : on échange en confiance. Faites l'effort de la vérification, ça vous distingue. 🟢",
                'hearts' => 356, 'replies' => 54, 'reactions' => ['👍', '🟢', '✨'],
                'comments' => [],
                'published_at' => Carbon::now()->subDays(9),
            ],
            [
                'author_name' => 'Coumba N.', 'is_anonymous' => false, 'author_verified' => false,
                'theme' => 'Témoignage', 'theme_emoji' => '💍', 'location' => 'Saint-Louis',
                'body' => "Premier échange timide, aujourd'hui nos familles préparent le mariage. Merci à cette belle communauté. 🌷🤲",
                'hearts' => 612, 'replies' => 88, 'reactions' => ['❤️', '🌷', '🎉'],
                'comments' => [
                    ['name' => 'Alioune D.', 'verified' => true, 'body' => 'Félicitations, qu\'Allah bénisse votre union !', 'likes' => 19],
                ],
                'published_at' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($posts as $p) {
            Post::create($p);
        }
    }
}
