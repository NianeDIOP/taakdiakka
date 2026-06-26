<?php

namespace App\Console\Commands;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;

class GenerateGuide extends Command
{
    protected $signature = 'taak:guide';

    protected $description = 'Génère le guide technique & de lancement (PDF) de TàakDiàkka.';

    public function handle(): int
    {
        $logoPath = public_path('img/logo-mark.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $html = view('guide', compact('logo'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->getCanvas()->page_text(495, 812, 'TàakDiàkka · Guide de lancement · {PAGE_NUM}/{PAGE_COUNT}', null, 8, [0.5, 0.47, 0.41]);

        $out = public_path('guide-lancement-taakdiakka.pdf');
        file_put_contents($out, $dompdf->output());

        $this->info('Guide généré : ' . $out . ' (' . round(filesize($out) / 1024) . ' Ko)');
        $this->line('Accessible via : ' . url('guide-lancement-taakdiakka.pdf'));

        return self::SUCCESS;
    }
}
