<?php

namespace App\Repository;

use App\Entity\Adapte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Adapte>
 *
 * @method Adapte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adapte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adapte[]    findAll()
 * @method Adapte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdapteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adapte::class);
    }

//    /**
//     * @return Adapte[] Returns an array of Adapte objects
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

//    public function findOneBySomeField($value): ?Adapte
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}