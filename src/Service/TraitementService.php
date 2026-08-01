<?php

namespace App\Service;

use App\Entity\Traitement;
use App\Entity\TraitementLigne;
use App\Enum\SensMouvement;
use App\Enum\TypeOperationStock;
use App\Repository\TraitementRepository;
use Doctrine\ORM\EntityManagerInterface;

class TraitementService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly StockService $stockService,
        private readonly TraitementRepository $traitementRepository,
    ) {
    }

    public function prepareNew(Traitement $traitement): void
    {
        if (!$traitement->getReference() && $traitement->getHangar() && $traitement->getDateTraitement()) {
            $traitement->setReference(
                $this->traitementRepository->nextReference(
                    $traitement->getDateTraitement(),
                    (string) $traitement->getHangar()->getNumero()
                )
            );
        }
        if ($traitement->getLignes()->isEmpty()) {
            $traitement->addLigne(new TraitementLigne());
        }
    }

    public function save(Traitement $traitement, bool $isNew = true): void
    {
        if ($isNew && (!$traitement->getReference() || str_starts_with((string) $traitement->getReference(), 'BT-TMP'))) {
            $traitement->setReference(
                $this->traitementRepository->nextReference(
                    $traitement->getDateTraitement() ?? new \DateTimeImmutable('today'),
                    (string) ($traitement->getHangar()?->getNumero() ?? 'H')
                )
            );
        }

        $this->em->persist($traitement);
        $this->em->flush();

        // Remove previous stock movements if updating
        foreach ($traitement->getMouvements()->toArray() as $mouvement) {
            $this->em->remove($mouvement);
        }
        $this->em->flush();

        $this->stockService->createMouvement(
            TypeOperationStock::TriageSortie,
            $traitement->getArticleSource(),
            $traitement->getCouleurSource(),
            $traitement->getMagasin(),
            (string) $traitement->getPoidsSortie(),
            $traitement->getDateTraitement(),
            SensMouvement::Sortie,
            $traitement->getReference(),
            $traitement->getFournisseur(),
            $traitement->getContrat(),
            $traitement->getHangar(),
            null,
            $traitement,
            'Sortie triage',
            false,
            true,
        );

        foreach ($traitement->getLignes() as $ligne) {
            if (!$ligne->getArticle() || !$ligne->getCouleur() || !$ligne->getPoids()) {
                continue;
            }
            $type = match ($ligne->getCategorie()->value) {
                'retour' => TypeOperationStock::Retour,
                default => TypeOperationStock::TriageEntree,
            };
            $this->stockService->createMouvement(
                $type,
                $ligne->getArticle(),
                $ligne->getCouleur(),
                $traitement->getMagasin(),
                (string) $ligne->getPoids(),
                $traitement->getDateTraitement(),
                SensMouvement::Entree,
                $traitement->getReference(),
                $traitement->getFournisseur(),
                $traitement->getContrat(),
                $traitement->getHangar(),
                null,
                $traitement,
                $ligne->getCategorie()->label(),
                false,
                true,
            );
        }

        $this->em->flush();
    }
}
