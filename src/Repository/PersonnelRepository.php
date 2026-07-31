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
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.numeroPersonnel', 'ASC');

        if ($query !== null && $query !== '') {
            $qb
                ->andWhere('p.numeroPersonnel LIKE :q OR LOWER(p.nom) LIKE LOWER(:q)')
                ->setParameter('q', '%'.$query.'%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneByNumero(string $numero): ?Personnel
    {
        return $this->findOneBy(['numeroPersonnel' => strtoupper(trim($numero))]);
    }
}
