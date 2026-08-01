<?php

namespace App\Repository;

use App\Entity\Article;
use App\Enum\FamilleArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Article> */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /** @return list<Article> */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.actif = true')
            ->orderBy('a.libelle', 'ASC')
            ->getQuery()->getResult();
    }

    public function findOneByCode(string $code): ?Article
    {
        return $this->findOneBy(['code' => strtoupper(trim($code))]);
    }

    /** @return list<Article> */
    public function findByFamille(FamilleArticle $famille): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.famille = :f')->setParameter('f', $famille)
            ->andWhere('a.actif = true')
            ->orderBy('a.libelle', 'ASC')
            ->getQuery()->getResult();
    }
}
