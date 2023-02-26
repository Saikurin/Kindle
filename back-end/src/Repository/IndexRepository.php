<?php

namespace App\Repository;

use App\Entity\Index;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Index>
 *
 * @method Index|null find($id, $lockMode = null, $lockVersion = null)
 * @method Index|null findOneBy(array $criteria, array $orderBy = null)
 * @method Index[]    findAll()
 * @method Index[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndexRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Index::class);
    }

    public function save(Index $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Index $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Index[] Returns an array of Index objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Index
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getTokens(): array|float|int|string
    {
        $qb = $this->createQueryBuilder('i');
        $qb->select('i.token');
        $qb->distinct();

        return $qb->getQuery()->getScalarResult();
    }

    public function searchRegexp(string $regexp)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->select('i');
        $qb->distinct();
        $qb->where('REGEXP(i.token, :regexp) = true');
        $qb->setParameter('regexp', $regexp);

        return $qb->getQuery()->getArrayResult();
    }
}
