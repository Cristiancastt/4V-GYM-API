<?php

namespace App\Repository;

use App\Entity\ActivityNew;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivityNew>
 *
 * @method ActivityNew|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityNew|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityNew[]    findAll()
 * @method ActivityNew[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityNewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityNew::class);
    }

//    /**
//     * @return ActivityNew[] Returns an array of ActivityNew objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActivityNew
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
