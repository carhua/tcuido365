<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\TipoExplotacion;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoExplotacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoExplotacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoExplotacion[]    findAll()
 * @method TipoExplotacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoExplotacionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoExplotacion::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('tipoExplotacion')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('tipoExplotacion.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['tipoExplotacion.nombre']);

        return $queryBuilder;
    }
}
