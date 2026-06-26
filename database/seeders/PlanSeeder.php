<?php

namespace Database\Seeders;

use App\Models\BoostPack;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug'             => 'gratuit',
                'name'             => 'Découverte',
                'tagline'          => 'Pour explorer la communauté',
                'price'            => 0,
                'compare_at_price' => null,
                'duration_days'    => null,
                'is_premium'       => false,
                'sort_order'       => 1,
                'features'         => [
                    'Créer son profil et sa demande',
                    'Parcourir les membres compatibles',
                    'Accès à la communauté',
                    '2 messages gratuits par contact',
                    '1 photo visible par profil',
                ],
            ],
            [
                'slug'             => 'mensuel',
                'name'             => 'Premium Mensuel',
                'tagline'          => 'L\'expérience complète',
                'price'            => 4900,
                'compare_at_price' => 6000,
                'duration_days'    => 30,
                'is_premium'       => true,
                'sort_order'       => 2,
                'features'         => [
                    'Envoyer des demandes d\'ami',
                    'Messages illimités avec tous vos contacts',
                    'Voir toutes les photos des profils',
                    'Voir qui a visité votre profil',
                    'Badge Premium sur votre profil',
                    'Priorité dans les résultats',
                ],
            ],
            [
                'slug'             => 'annuel',
                'name'             => 'Premium Annuel',
                'tagline'          => '2 mois offerts',
                'price'            => 49000,
                'compare_at_price' => 58800,
                'duration_days'    => 365,
                'is_premium'       => true,
                'sort_order'       => 3,
                'features'         => [
                    'Tous les avantages du Premium Mensuel',
                    'Économisez l\'équivalent de 2 mois',
                    'Engagement annuel au meilleur tarif',
                    'Support prioritaire',
                ],
            ],
        ];

        foreach ($plans as $p) {
            Plan::updateOrCreate(['slug' => $p['slug']], $p);
        }

        $boosts = [
            ['slug' => 'boost-24h', 'name' => 'Mise en avant 24h', 'price' => 1500, 'duration_days' => 1, 'sort_order' => 1],
            ['slug' => 'boost-7j', 'name' => 'Mise en avant 7 jours', 'price' => 5000, 'duration_days' => 7, 'sort_order' => 2],
            ['slug' => 'boost-30j', 'name' => 'Mise en avant 30 jours', 'price' => 15000, 'duration_days' => 30, 'sort_order' => 3],
        ];

        foreach ($boosts as $b) {
            BoostPack::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
