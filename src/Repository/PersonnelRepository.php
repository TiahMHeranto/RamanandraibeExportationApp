<?php

namespace App\Repository;

use App\Entity\Personnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personnel>
 */
class PersonnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnel::class);
    }

    /**
     * @return list<Personnel>
     */
    public function search(?string $query = null): array
    {
        return $this->createSearchQueryBuilder($query)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array{
     *     items: list<Personnel>,
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

        $countQb = $this->createSearchQueryBuilder($query)
            ->select('COUNT(p.id)');
        $total = (int) $countQb->getQuery()->getSingleScalarResult();
        $pages = max(1, (int) ceil($total / $limit));
        $page = min($page, $pages);

        /** @var list<Personnel> $items */
        $items = $this->createSearchQueryBuilder($query)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $from = $total === 0 ? 0 : (($page - 1) * $limit) + 1;
        $to = min($page * $limit, $total);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'limit' => $limit,
            'from' => $from,
            'to' => $to,
        ];
    }

    public function findOneByNumero(string $numero): ?Personnel
    {
        return $this->findOneBy(['numeroPersonnel' => strtoupper(trim($numero))]);
    }

    private function createSearchQueryBuilder(?string $query)
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.numeroPersonnel', 'ASC');

        if ($query !== null && $query !== '') {
            $qb
                ->andWhere('p.numeroPersonnel LIKE :q OR LOWER(p.nom) LIKE LOWER(:q)')
                ->setParameter('q', '%'.$query.'%');
        }

        return $qb;
    }
}
