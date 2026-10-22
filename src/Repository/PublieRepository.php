<?php

namespace App\Repository;

use App\Entity\Publie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publie>
 *
 * @method Publie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publie[]    findAll()
 * @method Publie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publie::class);
    }

//    /**
//     * @return Publie[] Returns an array of Publie objects
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

//    public function findOneBySomeField($value): ?Publie
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}