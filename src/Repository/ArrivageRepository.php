<?php

namespace App\Repository;

use App\Entity\Arrivage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Arrivage>
 */
class ArrivageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Arrivage::class);
    }

    /**
     * @return list<Arrivage>
     */
    public function search(?string $query = null): array
    {
        return $this->createSearchQueryBuilder($query)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array{
     *     items: list<Arrivage>,
     *     total: int,
     *     page: int,
     *     pages: int,
     *     limit: int,
     *     from: int,
     *     to: int
     * }
     */
    public function searchPaginated(?string $query, int $page, int $limit = 15): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $total = (int) $this->createSearchQueryBuilder($query)
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $pages = max(1, (int) ceil($total / $limit));
        $page = min($page, $pages);

        /** @var list<Arrivage> $items */
        $items = $this->createSearchQueryBuilder($query)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'limit' => $limit,
            'from' => $total === 0 ? 0 : (($page - 1) * $limit) + 1,
            'to' => min($page * $limit, $total),
        ];
    }

    /**
     * @return list<Arrivage>
     */
    public function findLatest(int $limit = 8): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.fournisseur', 'f')->addSelect('f')
            ->orderBy('a.dateArrivage', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function nextNumero(\DateTimeImmutable $date): string
    {
        $prefix = 'ARR-'.$date->format('Ymd').'-';
        $last = $this->createQueryBuilder('a')
            ->andWhere('a.numero LIKE :prefix')
            ->setParameter('prefix', $prefix.'%')
            ->orderBy('a.numero', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $seq = 1;
        if ($last instanceof Arrivage && preg_match('/-(\d+)$/', (string) $last->getNumero(), $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    private function createSearchQueryBuilder(?string $query)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.fournisseur', 'f')->addSelect('f')
            ->orderBy('a.dateArrivage', 'DESC')
            ->addOrderBy('a.id', 'DESC');

        if ($query !== null && $query !== '') {
            $qb
                ->andWhere('a.numero LIKE :q OR LOWER(a.origine) LIKE LOWER(:q) OR LOWER(f.nom) LIKE LOWER(:q) OR f.code LIKE :q')
                ->setParameter('q', '%'.$query.'%');
        }

        return $qb;
    }
}
