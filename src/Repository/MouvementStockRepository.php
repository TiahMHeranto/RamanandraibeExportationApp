<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Couleur;
use App\Entity\Magasin;
use App\Entity\MouvementStock;
use App\Enum\SensMouvement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<MouvementStock> */
class MouvementStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementStock::class);
    }

    /**
     * @return list<MouvementStock>
     */
    public function findFiltered(?\DateTimeImmutable $from, ?\DateTimeImmutable $to, ?Article $article = null, ?Magasin $magasin = null, int $limit = 200): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.article', 'a')->addSelect('a')
            ->leftJoin('m.couleur', 'c')->addSelect('c')
            ->leftJoin('m.magasin', 'mag')->addSelect('mag')
            ->orderBy('m.dateMouvement', 'DESC')
            ->addOrderBy('m.id', 'DESC')
            ->setMaxResults($limit);

        if ($from) {
            $qb->andWhere('m.dateMouvement >= :from')->setParameter('from', $from);
        }
        if ($to) {
            $qb->andWhere('m.dateMouvement <= :to')->setParameter('to', $to);
        }
        if ($article) {
            $qb->andWhere('m.article = :article')->setParameter('article', $article);
        }
        if ($magasin) {
            $qb->andWhere('m.magasin = :magasin')->setParameter('magasin', $magasin);
        }

        return $qb->getQuery()->getResult();
    }

    public function computeSolde(Article $article, Couleur $couleur, Magasin $magasin): string
    {
        $rows = $this->createQueryBuilder('m')
            ->select('m.sens, m.poids')
            ->andWhere('m.article = :a')->setParameter('a', $article)
            ->andWhere('m.couleur = :c')->setParameter('c', $couleur)
            ->andWhere('m.magasin = :m')->setParameter('m', $magasin)
            ->getQuery()->getArrayResult();

        $total = 0.0;
        foreach ($rows as $row) {
            $w = (float) $row['poids'];
            $sens = $row['sens'];
            $isEntree = $sens === SensMouvement::Entree
                || $sens === SensMouvement::Entree->value
                || ($sens instanceof SensMouvement && $sens === SensMouvement::Entree)
                || $sens === 'entree';
            $total += $isEntree ? $w : -$w;
        }

        return number_format($total, 3, '.', '');
    }

    /**
     * @return list<array{article_id: int, couleur_id: int, magasin_id: int, solde: string, article: string, couleur: string, magasin: string, famille: string}>
     */
    public function findSoldes(): array
    {
        $mouvements = $this->createQueryBuilder('m')
            ->leftJoin('m.article', 'a')->addSelect('a')
            ->leftJoin('m.couleur', 'c')->addSelect('c')
            ->leftJoin('m.magasin', 'mag')->addSelect('mag')
            ->getQuery()->getResult();

        $map = [];
        /** @var MouvementStock $m */
        foreach ($mouvements as $m) {
            $key = $m->getArticle()->getId().'|'.$m->getCouleur()->getId().'|'.$m->getMagasin()->getId();
            if (!isset($map[$key])) {
                $map[$key] = [
                    'article_id' => $m->getArticle()->getId(),
                    'couleur_id' => $m->getCouleur()->getId(),
                    'magasin_id' => $m->getMagasin()->getId(),
                    'article' => $m->getArticle()->getLibelle(),
                    'couleur' => $m->getCouleur()->getLibelle(),
                    'magasin' => $m->getMagasin()->getNom(),
                    'famille' => $m->getArticle()->getFamille()->label(),
                    'solde' => 0.0,
                ];
            }
            $map[$key]['solde'] += $m->signedPoids();
        }

        $result = [];
        foreach ($map as $row) {
            $row['solde'] = number_format($row['solde'], 3, '.', '');
            $result[] = $row;
        }

        usort($result, static fn ($a, $b) => [$a['article'], $a['couleur']] <=> [$b['article'], $b['couleur']]);

        return $result;
    }
}
