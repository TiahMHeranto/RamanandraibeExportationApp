<?php

namespace App\Repository;

use App\Entity\Personnel;
use App\Entity\Traitement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Traitement> */
class TraitementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traitement::class);
    }

    /**
     * @return array{items: list<Traitement>, total: int, page: int, pages: int, limit: int, from: int, to: int}
     */
    public function searchPaginated(?\DateTimeImmutable $date, ?int $hangarId, ?int $trieuseId, int $page, int $limit = 15): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $applyFilters = static function ($qb) use ($date, $hangarId, $trieuseId) {
            if ($date) {
                $qb->andWhere('t.dateTraitement = :d')->setParameter('d', $date);
            }
            if ($hangarId) {
                $qb->andWhere('IDENTITY(t.hangar) = :hid')->setParameter('hid', $hangarId);
            }
            if ($trieuseId) {
                $qb->andWhere('IDENTITY(t.trieuse) = :tid')->setParameter('tid', $trieuseId);
            }
            return $qb;
        };

        $countQb = $applyFilters($this->createQueryBuilder('t')->select('COUNT(t.id)'));
        $total = (int) $countQb->getQuery()->getSingleScalarResult();
        $pages = max(1, (int) ceil($total / $limit));
        $page = min($page, $pages);

        $qb = $applyFilters($this->createQueryBuilder('t')
            ->leftJoin('t.hangar', 'h')->addSelect('h')
            ->leftJoin('t.trieuse', 'tr')->addSelect('tr')
            ->leftJoin('t.fournisseur', 'f')->addSelect('f')
            ->orderBy('t.dateTraitement', 'DESC')
            ->addOrderBy('t.id', 'DESC'));

        /** @var list<Traitement> $items */
        $items = $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit)->getQuery()->getResult();

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

    public function nextReference(\DateTimeImmutable $date, string $hangarNumero): string
    {
        $prefix = sprintf('BT-%s-%s-', $date->format('Ymd'), strtoupper($hangarNumero));
        $last = $this->createQueryBuilder('t')
            ->andWhere('t.reference LIKE :p')->setParameter('p', $prefix.'%')
            ->orderBy('t.reference', 'DESC')->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        $seq = 1;
        if ($last instanceof Traitement && preg_match('/-(\d+)$/', (string) $last->getReference(), $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @return list<array{personnel_id: int, nom: string, numero: string, poids_sortie: float, poids_entree: float, nb_pieces: int, sessions: int}>
     */
    public function rendementByPersonnel(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        /** @var list<Traitement> $traitements */
        $traitements = $this->createQueryBuilder('t')
            ->leftJoin('t.trieuse', 'tr')->addSelect('tr')
            ->leftJoin('t.lignes', 'l')->addSelect('l')
            ->andWhere('t.dateTraitement BETWEEN :f AND :to')
            ->setParameter('f', $from)->setParameter('to', $to)
            ->getQuery()->getResult();

        $map = [];
        foreach ($traitements as $t) {
            $p = $t->getTrieuse();
            if (!$p) {
                continue;
            }
            $id = $p->getId();
            if (!isset($map[$id])) {
                $map[$id] = [
                    'personnel_id' => $id,
                    'nom' => $p->getNom(),
                    'numero' => $p->getNumeroPersonnel(),
                    'poids_sortie' => 0.0,
                    'poids_entree' => 0.0,
                    'nb_pieces' => 0,
                    'sessions' => 0,
                ];
            }
            $map[$id]['sessions']++;
            $map[$id]['poids_sortie'] += (float) $t->getPoidsSortie();
            $map[$id]['poids_entree'] += (float) $t->getPoidsEntrees();
            foreach ($t->getLignes() as $ligne) {
                $map[$id]['nb_pieces'] += (int) ($ligne->getNombre() ?? 0);
            }
        }

        return array_values($map);
    }

    public function sumPoidsSortieToday(): float
    {
        $v = $this->createQueryBuilder('t')
            ->select('COALESCE(SUM(t.poidsSortie), 0)')
            ->andWhere('t.dateTraitement = :d')
            ->setParameter('d', new \DateTimeImmutable('today'))
            ->getQuery()->getSingleScalarResult();

        return (float) $v;
    }
}
