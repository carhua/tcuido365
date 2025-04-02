<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\EstadoCivil;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EstadoCivil|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstadoCivil|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstadoCivil[]    findAll()
 * @method EstadoCivil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoCivilRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstadoCivil::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('estadoCivil')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('estadoCivil.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['estadoCivil.nombre']);

        return $queryBuilder;
    }
}
