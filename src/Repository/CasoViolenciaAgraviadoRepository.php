<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CasoViolenciaAgraviado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CasoViolenciaAgraviado|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasoViolenciaAgraviado|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasoViolenciaAgraviado[]    findAll()
 * @method CasoViolenciaAgraviado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasoViolenciaAgraviadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasoViolenciaAgraviado::class);
    }

    // /**
    //  * @return CasoViolenciaAgraviado[] Returns an array of CasoViolenciaAgraviado objects
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
    public function findOneBySomeField($value): ?CasoViolenciaAgraviado
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
