<?php

namespace App\Console\Commands;

use App\Models\BoostPack;
use App\Models\Plan;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;

class GenerateDossier extends Command
{
    protected $signature = 'taak:dossier';

    protected $description = 'Génère le dossier de présentation (PDF business) de TàakDiàkka.';

    public function handle(): int
    {
        $logoPath = public_path('img/logo-mark.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $plans = Plan::query()->orderBy('sort_order')->get();
        $boosts = BoostPack::query()->orderBy('sort_order')->get();

        $html = view('dossier', compact('logo', 'plans', 'boosts'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Numérotation des pages
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(520, 812, 'TàakDiàkka · {PAGE_NUM}/{PAGE_COUNT}', null, 8, [0.5, 0.47, 0.41]);

        $out = public_path('dossier-taakdiakka.pdf');
        file_put_contents($out, $dompdf->output());

        $this->info('Dossier généré : ' . $out . ' (' . round(filesize($out) / 1024) . ' Ko)');
        $this->line('Accessible via : ' . url('dossier-taakdiakka.pdf'));

        return self::SUCCESS;
    }
}
