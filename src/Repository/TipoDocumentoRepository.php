<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\TipoDocumento;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoDocumento|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoDocumento|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoDocumento[]    findAll()
 * @method TipoDocumento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoDocumentoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoDocumento::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('tipoDocumento')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('tipoDocumento.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['tipoDocumento.nombre']);

        return $queryBuilder;
    }
}
