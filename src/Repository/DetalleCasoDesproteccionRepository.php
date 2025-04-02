<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\DetalleCasoDesproteccion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DetalleCasoDesproteccion|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetalleCasoDesproteccion|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetalleCasoDesproteccion[]    findAll()
 * @method DetalleCasoDesproteccion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetalleCasoDesproteccionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetalleCasoDesproteccion::class);
    }

    // /**
    //  * @return DetalleCasoDesproteccion[] Returns an array of DetalleCasoDesproteccion objects
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
    public function findOneBySomeField($value): ?DetalleCasoDesproteccion
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
