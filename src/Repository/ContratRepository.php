<?php

namespace App\Repository;

use App\Entity\Contrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Contrat> */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }

    /** @return list<Contrat> */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.actif = true')
            ->orderBy('c.reference', 'ASC')
            ->getQuery()->getResult();
    }

    public function findOneByReference(string $reference): ?Contrat
    {
        return $this->findOneBy(['reference' => strtoupper(trim($reference))]);
    }
}
