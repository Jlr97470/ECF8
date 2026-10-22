<?php

namespace App\Repository;

use App\Entity\Possede;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Possede>
 *
 * @method Possede|null find($id, $lockMode = null, $lockVersion = null)
 * @method Possede|null findOneBy(array $criteria, array $orderBy = null)
 * @method Possede[]    findAll()
 * @method Possede[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PossedeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Possede::class);
    }

//    /**
//     * @return Possede[] Returns an array of Possede objects
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

//    public function findOneBySomeField($value): ?Possede
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
