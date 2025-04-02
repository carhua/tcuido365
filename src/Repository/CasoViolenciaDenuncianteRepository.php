<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CasoViolenciaDenunciante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CasoViolenciaDenunciante|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasoViolenciaDenunciante|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasoViolenciaDenunciante[]    findAll()
 * @method CasoViolenciaDenunciante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasoViolenciaDenuncianteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasoViolenciaDenunciante::class);
    }

    // /**
    //  * @return CasoViolenciaDenunciante[] Returns an array of CasoViolenciaDenunciante objects
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
    public function findOneBySomeField($value): ?CasoViolenciaDenunciante
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
