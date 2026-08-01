<?php

namespace App\Service;

use App\Repository\TraitementRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class TraitementPdfExporter
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function export(\App\Entity\Traitement $traitement): string
    {
        $html = $this->twig->render('traitement/pdf.html.twig', [
            'traitement' => $traitement,
            'generatedAt' => new \DateTimeImmutable(),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
