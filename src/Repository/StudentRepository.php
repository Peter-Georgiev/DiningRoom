<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{

    /**
     * @param string|null $term
     */
    public function getWithSearchQueryBuilder(?string $term): QueryBuilder
    {
        return $term;
            //->orderBy('c.createdAt', 'DESC');
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * @return mixed
     */
    public function findAllStudent()
    {

       return $this->createQueryBuilder('s')
            ->select('s.id', 's.fullName', 's.isActive', 'c.name AS className',
                't.fullName AS teacherFullName',
                'p.dateCreate', 'p.price', 'p.forMonth', 'p.feeInDays', 'p.isPaid'
            )
            ->innerJoin('s.classes', 'c')
            ->innerJoin('s.teachers', 't')
            ->innerJoin('s.products', 'p')
            ->getQuery()
            ->getResult();
    }

    public function findByFullName(Student $student)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.classes', 'c')
            ->where('s.fullName = ?1')
            ->andWhere('c.name = ?2')
            ->setParameter(1, $student->getFullName())
            ->setParameter(2, $student->getClass()->getName())
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Student[] Returns an array of Student objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
