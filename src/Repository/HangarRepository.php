<?php

namespace App\Repository;

use App\Entity\Hangar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hangar>
 */
class HangarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hangar::class);
    }

    /**
     * @return list<Hangar>
     */
    public function search(?string $query = null): array
    {
        return $this->createSearchQueryBuilder($query)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array{
     *     items: list<Hangar>,
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
            ->select('COUNT(h.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $pages = max(1, (int) ceil($total / $limit));
        $page = min($page, $pages);

        /** @var list<Hangar> $items */
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

    public function findOneByNumero(string $numero): ?Hangar
    {
        return $this->findOneBy(['numero' => strtoupper(trim($numero))]);
    }

    public function findOneByCode(string $code): ?Hangar
    {
        return $this->findOneBy(['code' => strtoupper(trim($code))]);
    }

    private function createSearchQueryBuilder(?string $query)
    {
        $qb = $this->createQueryBuilder('h')
            ->orderBy('h.numero', 'ASC');

        if ($query !== null && $query !== '') {
            $qb
                ->andWhere('h.numero LIKE :q OR h.code LIKE :q')
                ->setParameter('q', '%'.strtoupper($query).'%');
        }

        return $qb;
    }
}
