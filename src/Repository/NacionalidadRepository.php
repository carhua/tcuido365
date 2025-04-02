<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Nacionalidad;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Nacionalidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nacionalidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nacionalidad[]    findAll()
 * @method Nacionalidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NacionalidadRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nacionalidad::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('nacionalidad')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('nacionalidad.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['nacionalidad.nombre']);

        return $queryBuilder;
    }
}
