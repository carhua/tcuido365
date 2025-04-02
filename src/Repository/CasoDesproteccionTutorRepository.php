<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CasoDesproteccionTutor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CasoDesproteccionTutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasoDesproteccionTutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasoDesproteccionTutor[]    findAll()
 * @method CasoDesproteccionTutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasoDesproteccionTutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasoDesproteccionTutor::class);
    }

    // /**
    //  * @return CasoDesproteccionTutor[] Returns an array of CasoDesproteccionTutor objects
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
    public function findOneBySomeField($value): ?CasoDesproteccionTutor
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
