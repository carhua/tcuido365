<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Provincia;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Provincia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provincia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provincia[]    findAll()
 * @method Provincia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProvinciaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provincia::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('provincia')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('provincia.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['provincia.nombre']);

        return $queryBuilder;
    }
}
