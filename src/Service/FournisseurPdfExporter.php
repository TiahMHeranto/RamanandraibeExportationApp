<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class FournisseurPdfExporter
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param list<\App\Entity\Fournisseur> $fournisseurs
     */
    public function export(array $fournisseurs, ?string $searchQuery = null, ?string $zone = null): string
    {
        $html = $this->twig->render('fournisseur/pdf.html.twig', [
            'fournisseurs' => $fournisseurs,
            'q' => $searchQuery,
            'zone' => $zone,
            'generatedAt' => new \DateTimeImmutable(),
            'total' => count($fournisseurs),
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
