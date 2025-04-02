<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Distrito;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Distrito|null find($id, $lockMode = null, $lockVersion = null)
 * @method Distrito|null findOneBy(array $criteria, array $orderBy = null)
 * @method Distrito[]    findAll()
 * @method Distrito[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistritoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Distrito::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('distrito')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('distrito.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['distrito.nombre']);

        return $queryBuilder;
    }

    public function findByProvincia($provinciaId)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.provincia = :val')
            ->orWhere('d.nombre = :val2')
            ->setParameter('val', $provinciaId)
            ->setParameter('val2', 'TODOS')
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
