<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Field;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Field|null find($id, $lockMode = null, $lockVersion = null)
 * @method Field|null findOneBy(array $criteria, array $orderBy = null)
 * @method Field[]    findAll()
 * @method Field[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Field::class);
    }

    // /**
    //  * @return Field[] Returns an array of Field objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findToClear(array $keysToClear, Building $building)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.building = :building')
            ->andWhere('f.position IN (:ids)')
            ->setParameter('building', $building)
            ->setParameter('ids', $keysToClear)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Building|null $building
     * @return int|mixed|string|Field[]
     */
    public function findEmptyFields(?Building $building)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.building = :building')
            ->andWhere('f.phase = 0')
            ->andWhere('f.productType IS NULL')
            ->setParameter('building', $building)
            ->getQuery()
            ->getResult();
    }
}
