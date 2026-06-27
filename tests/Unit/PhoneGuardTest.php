<?php

namespace Tests\Unit;

use App\Support\PhoneGuard;
use PHPUnit\Framework\TestCase;

class PhoneGuardTest extends TestCase
{
    /** @dataProvider numerosBloques */
    public function test_bloque_les_numeros(string $message): void
    {
        $this->assertTrue(PhoneGuard::containsPhone($message), "Aurait dû bloquer : {$message}");
    }

    public static function numerosBloques(): array
    {
        return [
            'collé'        => ['771588903'],
            'espacé'       => ['appelle moi 77 15 88 903'],
            'points'       => ['7.7.1.5.8.8.9.0.3'],
            'en lettres'   => ['sept sept un cinq huit huit neuf zero trois'],
            'leet'         => ['mon num OO7 l5 88 9O3 environ'],
            'indicatif'    => ['+221 77 158 89 03'],
        ];
    }

    /** @dataProvider messagesSains */
    public function test_laisse_passer_les_messages_normaux(string $message): void
    {
        $this->assertFalse(PhoneGuard::containsPhone($message), "N'aurait pas dû bloquer : {$message}");
    }

    public static function messagesSains(): array
    {
        return [
            'phrase'   => ['Bonjour, j\'ai 35 ans et 2 enfants, ravie de vous lire.'],
            'annee'    => ['Je suis née en 1990 incha Allah.'],
            'court'    => ['As-salâm aleykoum, comment allez-vous ?'],
        ];
    }

    public function test_detecte_un_numero_envoye_par_petits_lots(): void
    {
        $precedents = ['mon numéro c\'est', '77', '158'];
        $this->assertTrue(PhoneGuard::assembledPhone($precedents, '89 03'));
    }

    public function test_assemblage_ignore_le_texte_entre_les_fragments(): void
    {
        $precedents = ['77', 'coucou ça va', '15 88'];
        $this->assertTrue(PhoneGuard::assembledPhone($precedents, '903'));
    }

    public function test_assemblage_ne_bloque_pas_des_chiffres_anodins(): void
    {
        $precedents = ['j\'ai 35 ans', '2 enfants'];
        $this->assertFalse(PhoneGuard::assembledPhone($precedents, 'à bientôt inchaAllah'));
    }
}
