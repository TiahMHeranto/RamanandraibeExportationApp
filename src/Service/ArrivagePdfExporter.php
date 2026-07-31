<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class ArrivagePdfExporter
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param list<\App\Entity\Arrivage> $arrivages
     */
    public function export(array $arrivages, ?string $searchQuery = null): string
    {
        $html = $this->twig->render('arrivage/pdf.html.twig', [
            'arrivages' => $arrivages,
            'q' => $searchQuery,
            'generatedAt' => new \DateTimeImmutable(),
            'total' => count($arrivages),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
}
