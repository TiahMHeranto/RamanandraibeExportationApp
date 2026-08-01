<?php

namespace App\Repository;

use App\Entity\Couleur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Couleur> */
class CouleurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Couleur::class);
    }

    /** @return list<Couleur> */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.actif = true')
            ->orderBy('c.libelle', 'ASC')
            ->getQuery()->getResult();
    }

    public function findOneByCode(string $code): ?Couleur
    {
        return $this->findOneBy(['code' => strtoupper(trim($code))]);
    }
}
