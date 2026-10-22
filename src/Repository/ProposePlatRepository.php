<?php

namespace App\Repository;

use App\Entity\ProposePlat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProposePlat>
 *
 * @method ProposePlat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProposePlat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProposePlat[]    findAll()
 * @method ProposePlat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposePlatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProposePlat::class);
    }

//    /**
//     * @return ProposePlat[] Returns an array of ProposePlat objects
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

//    public function findOneBySomeField($value): ?ProposePlat
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}