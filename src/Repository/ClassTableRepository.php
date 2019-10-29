<?php

namespace App\Repository;

use App\Entity\ClassTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClassTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassTable[]    findAll()
 * @method ClassTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassTable::class);
    }

    public function findByNameClass(ClassTable $classTable)
    {
        return $this->createQueryBuilder('c')
            ->where('c.name = ?1')
            ->setParameter(1, $classTable->getName())
            ->getQuery()
            ->getResult();
    }

    public function findByNameExclusionCurrentId($className, $classId)
    {
        $query = 'SELECT if(COUNT(*) > 0, true, false) AS isName 
                FROM class_table AS c 
                WHERE c.name = ? 
                AND c.id NOT IN (SELECT c.id FROM class_table AS c WHERE c.id = ?)';
        $conn = $this->getEntityManager()->getConnection();
        $stm = $conn->prepare($query);
        //$params = array('className' => $className, 'classId' => $classId);
        //return $this->getEntityManager()->getConnection()
          //  ->executeQuery($query, $params)
            //->execute();
        $stm->bindParam(1, $className);
        $stm->bindParam(2, $classId);
        $stm->execute();
        return $stm->fetch()['isName'];
    }

    public function updateClassName($className, $classId)
    {
        return $this->createQueryBuilder('c')
            ->update()
            ->set('c.name', '?1')
            ->setParameter(1, $className)
            ->where('c.id = ?2')
            ->setParameter(2, $classId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllActiveStudents()
    {
        return $this->createQueryBuilder('c')
            ->select( )
            ->innerJoin('c.students', 's')
            ->where('s.isActive = 1')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return ClassTable[] Returns an array of ClassTable objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ClassTable
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
