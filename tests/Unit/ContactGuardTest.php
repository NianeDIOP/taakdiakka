<?php

namespace Tests\Unit;

use App\Support\ContactGuard;
use PHPUnit\Framework\TestCase;

class ContactGuardTest extends TestCase
{
    /** @dataProvider coordonneesBloquees */
    public function test_bloque_le_partage_de_coordonnees(string $message): void
    {
        $this->assertTrue(ContactGuard::containsContact($message), "Aurait dû bloquer : {$message}");
    }

    public static function coordonneesBloquees(): array
    {
        return [
            'email direct'    => ['écris-moi à fatou.diop@gmail.com'],
            'email obfusqué'  => ['mon mail c\'est fatou at gmail dot com'],
            'handle insta'    => ['mon insta : @lily_dakar'],
            'plateforme val'  => ['snap = khadim227'],
            'invitation wsp'  => ['ajoute-moi sur whatsapp stp'],
            'invitation insta'=> ['follow moi sur instagram'],
        ];
    }

    /** @dataProvider messagesSains */
    public function test_laisse_passer_les_messages_normaux(string $message): void
    {
        $this->assertFalse(ContactGuard::containsContact($message), "N'aurait pas dû bloquer : {$message}");
    }

    public static function messagesSains(): array
    {
        return [
            'question simple' => ['Est-ce que tu as Instagram ?'],
            'phrase normale'  => ['J\'aime beaucoup discuter avec vous, vous semblez sérieuse.'],
            'mot mail seul'   => ['Je consulte rarement mes mails, désolé du retard.'],
        ];
    }
}
