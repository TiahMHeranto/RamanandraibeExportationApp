<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Arrivage;
use App\Entity\Couleur;
use App\Entity\Fournisseur;
use App\Entity\Hangar;
use App\Entity\Contrat;
use App\Entity\Magasin;
use App\Entity\MouvementStock;
use App\Entity\Traitement;
use App\Enum\SensMouvement;
use App\Enum\TypeOperationStock;
use App\Repository\MouvementStockRepository;
use Doctrine\ORM\EntityManagerInterface;

class StockService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MouvementStockRepository $mouvementStockRepository,
    ) {
    }

    public function createMouvement(
        TypeOperationStock $type,
        Article $article,
        Couleur $couleur,
        Magasin $magasin,
        string $poids,
        \DateTimeImmutable $date,
        ?SensMouvement $sens = null,
        ?string $reference = null,
        ?Fournisseur $fournisseur = null,
        ?Contrat $contrat = null,
        ?Hangar $hangar = null,
        ?Arrivage $arrivage = null,
        ?Traitement $traitement = null,
        ?string $observations = null,
        bool $flush = true,
        bool $allowNegative = true,
    ): MouvementStock {
        $sens ??= $type->defaultSens();

        if ($sens === SensMouvement::Sortie && !$allowNegative) {
            $solde = (float) $this->mouvementStockRepository->computeSolde($article, $couleur, $magasin);
            if ($solde < (float) $poids) {
                throw new \RuntimeException(sprintf(
                    'Stock insuffisant pour %s / %s (%s kg disponible, %s kg demandé).',
                    $article->getLibelle(),
                    $couleur->getLibelle(),
                    number_format($solde, 3, '.', ''),
                    $poids
                ));
            }
        }

        $mouvement = (new MouvementStock())
            ->setDateMouvement($date)
            ->setSens($sens)
            ->setTypeOperation($type)
            ->setArticle($article)
            ->setCouleur($couleur)
            ->setMagasin($magasin)
            ->setPoids($poids)
            ->setReference($reference)
            ->setFournisseur($fournisseur)
            ->setContrat($contrat)
            ->setHangar($hangar)
            ->setArrivage($arrivage)
            ->setTraitement($traitement)
            ->setObservations($observations);

        $this->em->persist($mouvement);
        if ($flush) {
            $this->em->flush();
        }

        return $mouvement;
    }

    public function registerArrivage(Arrivage $arrivage, bool $flush = true): ?MouvementStock
    {
        if (!$arrivage->getArticle() || !$arrivage->getCouleur() || !$arrivage->getMagasin()) {
            return null;
        }

        return $this->createMouvement(
            TypeOperationStock::Arrivage,
            $arrivage->getArticle(),
            $arrivage->getCouleur(),
            $arrivage->getMagasin(),
            (string) $arrivage->getPoids(),
            $arrivage->getDateArrivage() ?? new \DateTimeImmutable('today'),
            SensMouvement::Entree,
            $arrivage->getNumero(),
            $arrivage->getFournisseur(),
            $arrivage->getContrat(),
            null,
            $arrivage,
            null,
            sprintf('Arrivage %s — %s', $arrivage->getNumero(), $arrivage->getOrigine()),
            $flush,
        );
    }

    public function getSoldes(): array
    {
        return $this->mouvementStockRepository->findSoldes();
    }
}
