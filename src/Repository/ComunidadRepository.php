<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Comunidad;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comunidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comunidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comunidad[]    findAll()
 * @method Comunidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComunidadRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comunidad::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('comunidad')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('comunidad.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['comunidad.nombre']);

        return $queryBuilder;
    }
}
