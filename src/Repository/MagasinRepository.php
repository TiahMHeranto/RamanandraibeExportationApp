<?php

namespace App\Repository;

use App\Entity\Magasin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Magasin> */
class MagasinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Magasin::class);
    }

    /** @return list<Magasin> */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.actif = true')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findDefault(): ?Magasin
    {
        return $this->findOneBy(['code' => 'MORAFENO']) ?? $this->findOneBy([]);
    }
}
