<?php

namespace App\Repository;

use App\Entity\Fournisseur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fournisseur>
 */
class FournisseurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fournisseur::class);
    }

    /**
     * @return list<Fournisseur>
     */
    public function search(?string $query = null, ?string $zone = null): array
    {
        return $this->createSearchQueryBuilder($query, $zone)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array{
     *     items: list<Fournisseur>,
     *     total: int,
     *     page: int,
     *     pages: int,
     *     limit: int,
     *     from: int,
     *     to: int
     * }
     */
    public function searchPaginated(?string $query, ?string $zone, int $page, int $limit = 15): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $total = (int) $this->createSearchQueryBuilder($query, $zone)
            ->select('COUNT(f.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $pages = max(1, (int) ceil($total / $limit));
        $page = min($page, $pages);

        /** @var list<Fournisseur> $items */
        $items = $this->createSearchQueryBuilder($query, $zone)
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
     * @return list<string>
     */
    public function findDistinctZones(): array
    {
        $rows = $this->createQueryBuilder('f')
            ->select('DISTINCT f.zone')
            ->orderBy('f.zone', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        return array_values(array_filter($rows, static fn ($z) => $z !== null && $z !== ''));
    }

    public function findOneByCode(string $code): ?Fournisseur
    {
        return $this->findOneBy(['code' => strtoupper(trim($code))]);
    }

    private function createSearchQueryBuilder(?string $query, ?string $zone)
    {
        $qb = $this->createQueryBuilder('f')
            ->orderBy('f.code', 'ASC');

        if ($query !== null && $query !== '') {
            $qb
                ->andWhere('f.code LIKE :q OR LOWER(f.nom) LIKE LOWER(:q) OR LOWER(f.zone) LIKE LOWER(:q)')
                ->setParameter('q', '%'.$query.'%');
        }

        if ($zone !== null && $zone !== '') {
            $qb
                ->andWhere('f.zone = :zone')
                ->setParameter('zone', $zone);
        }

        return $qb;
    }
}
