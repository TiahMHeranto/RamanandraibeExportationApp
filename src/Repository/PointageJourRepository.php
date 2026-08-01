<?php

namespace App\Repository;

use App\Entity\PointageJour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PointageJour> */
class PointageJourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointageJour::class);
    }

    /**
     * @return list<PointageJour>
     */
    public function findByDate(\DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.personnel', 'pe')->addSelect('pe')
            ->andWhere('p.datePointage = :d')->setParameter('d', $date)
            ->orderBy('pe.numeroPersonnel', 'ASC')
            ->getQuery()->getResult();
    }

    public function findOneByDateAndPersonnel(\DateTimeImmutable $date, int $personnelId): ?PointageJour
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.datePointage = :d')->setParameter('d', $date)
            ->andWhere('IDENTITY(p.personnel) = :pid')->setParameter('pid', $personnelId)
            ->getQuery()->getOneOrNullResult();
    }
}
