<?php

namespace App\Repository;

use App\Entity\ProposeTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProposeTheme>
 *
 * @method ProposeTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProposeTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProposeTheme[]    findAll()
 * @method ProposeTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposeThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProposeTheme::class);
    }

//    /**
//     * @return ProposeTheme[] Returns an array of ProposeTheme objects
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

//    public function findOneBySomeField($value): ?ProposeTheme
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}