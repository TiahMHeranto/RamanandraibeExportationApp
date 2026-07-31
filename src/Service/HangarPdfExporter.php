<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class HangarPdfExporter
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param list<\App\Entity\Hangar> $hangars
     */
    public function export(array $hangars, ?string $searchQuery = null): string
    {
        $html = $this->twig->render('hangar/pdf.html.twig', [
            'hangars' => $hangars,
            'q' => $searchQuery,
            'generatedAt' => new \DateTimeImmutable(),
            'total' => count($hangars),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
