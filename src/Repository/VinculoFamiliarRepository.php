<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\VinculoFamiliar;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VinculoFamiliar|null find($id, $lockMode = null, $lockVersion = null)
 * @method VinculoFamiliar|null findOneBy(array $criteria, array $orderBy = null)
 * @method VinculoFamiliar[]    findAll()
 * @method VinculoFamiliar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VinculoFamiliarRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VinculoFamiliar::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('vinculoFamiliar')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('vinculoFamiliar.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['vinculoFamiliar.nombre']);

        return $queryBuilder;
    }
}
