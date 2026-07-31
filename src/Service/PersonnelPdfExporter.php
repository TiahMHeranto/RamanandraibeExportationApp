<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PersonnelPdfExporter
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param list<\App\Entity\Personnel> $personnels
     */
    public function export(array $personnels, ?string $searchQuery = null): string
    {
        $html = $this->twig->render('personnel/pdf.html.twig', [
            'personnels' => $personnels,
            'q' => $searchQuery,
            'generatedAt' => new \DateTimeImmutable(),
            'total' => count($personnels),
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
