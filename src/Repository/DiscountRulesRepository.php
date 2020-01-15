<?php

namespace App\Repository;

use App\Entity\DiscountRules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DiscountRules|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscountRules|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscountRules[]    findAll()
 * @method DiscountRules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscountRulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscountRules::class);
    }

    // /**
    //  * @return DiscountRules[] Returns an array of DiscountRules objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DiscountRules
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
