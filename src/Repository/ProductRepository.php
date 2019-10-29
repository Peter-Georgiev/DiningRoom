<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findPaymentByClass($class)
    {
        return $this->createQueryBuilder('p')
            ->select( 's.firstName', 's.middleName', 's.lastName', 'c.name',
                'p.id', 'p.price AS productPrice', 'p.dateCreate AS productDateCreate', 'p.feeInDays',
                'p.lastEdit AS productLastEdit', 'p.forMonth', 'p.isPaid AS productIsPaid',
                'pay.price AS paymentPrice', 'pay.payment', 'pay.datePurchases AS paymentDatePurchases',
                'pay.lastEdit AS paymentLastEdit'
                )
            ->innerJoin('p.students', 's')
            ->innerJoin('p.payment', 'pay')
            ->innerJoin('s.classes', 'c')
            ->andWhere('c.name = ?1')
            ->setParameter(1, $class)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDontPaymentByClass($class)
    {
        return $this->createQueryBuilder('p')
            ->select( 's.firstName', 's.middleName', 's.lastName', 'c.name',
                'p.id', 'p.price AS productPrice', 'p.dateCreate AS productDateCreate',
                'p.lastEdit AS productLastEdit', 'p.forMonth', 'p.isPaid AS productIsPaid'
            )
            ->innerJoin('p.students', 's')
            ->innerJoin('s.classes', 'c')
            ->andWhere('c.name = ?1')
            ->setParameter(1, $class)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllActiveStudents()
    {
        return $this->createQueryBuilder('p')
            ->select( )
            ->innerJoin('p.students', 's')
            ->innerJoin('s.classes', 'c')
            ->innerJoin('s.teachers', 't')
            ->where('s.isActive = 1')
            ->getQuery()
            ->getResult()
        ;
    }

    public function updateIsPaidInProduct($productId, $isPaid)
    {
        return $this->createQueryBuilder('p')
            ->update()
            ->set('p.isPaid', '?1')
            ->setParameter(1, $isPaid)
            ->where('p.id = ?2')
            ->setParameter(2, $productId)
            ->getQuery()
            ->getSingleScalarResult();
    }


    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
