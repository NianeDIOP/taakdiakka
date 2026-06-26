<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'conditions' => [
                'title' => "Conditions d'utilisation",
                'body'  => "## Bienvenue sur TàakDiàkka\n\nEn utilisant TàakDiàkka, vous acceptez les présentes conditions d'utilisation. Notre plateforme est dédiée aux rencontres matrimoniales sérieuses dans le respect des valeurs de la communauté.\n\n### 1. Accès au service\nVous devez avoir au moins 18 ans et fournir des informations exactes lors de votre inscription.\n\n### 2. Comportement attendu\nLes membres s'engagent à interagir avec respect et bienveillance. Tout propos haineux, harcèlement ou faux profil est interdit et peut entraîner la suspension du compte.\n\n### 3. Abonnements\nCertaines fonctionnalités nécessitent un abonnement premium. Les conditions tarifaires sont détaillées sur la page Abonnements.\n\n### 4. Responsabilité\nTàakDiàkka facilite la mise en relation mais ne garantit pas l'issue des échanges entre membres.",
            ],
            'confidentialite' => [
                'title' => 'Politique de confidentialité (RGPD)',
                'body'  => "## Protection de vos données\n\nVotre vie privée est essentielle. Cette politique explique quelles données nous collectons et comment elles sont utilisées.\n\n### Données collectées\nNom, e-mail, informations de profil, photos et activité sur la plateforme.\n\n### Utilisation\nVos données servent uniquement à proposer des profils compatibles et à améliorer le service. Elles ne sont jamais revendues.\n\n### Vos droits\nVous pouvez à tout moment consulter, modifier ou supprimer vos données depuis vos paramètres, ou en nous contactant.\n\n### Sécurité\nNous mettons en œuvre des mesures techniques pour protéger vos informations.",
            ],
            'mentions-legales' => [
                'title' => 'Mentions légales',
                'body'  => "## Éditeur du site\n\nTàakDiàkka — Maison matrimoniale.\n\n### Hébergement\nLes informations relatives à l'hébergeur sont disponibles sur demande.\n\n### Propriété intellectuelle\nL'ensemble des contenus présents sur TàakDiàkka est protégé. Toute reproduction sans autorisation est interdite.\n\n### Contact\nPour toute question, écrivez-nous via la page de contact.",
            ],
        ];

        foreach ($defaults as $slug => $data) {
            Page::firstOrCreate(['slug' => $slug], $data);
        }
    }
}
