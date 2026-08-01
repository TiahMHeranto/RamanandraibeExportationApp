<?php

namespace App\Service;

use App\Repository\TraitementRepository;

class RendementService
{
    public function __construct(private readonly TraitementRepository $traitementRepository)
    {
    }

    /**
     * @return list<array{personnel_id: int, nom: string, numero: string, poids_sortie: float, poids_entree: float, nb_pieces: int, sessions: int}>
     */
    public function byPersonnel(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->traitementRepository->rendementByPersonnel($from, $to);
    }
}
