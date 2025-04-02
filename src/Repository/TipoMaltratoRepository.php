<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\TipoMaltrato;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipoMaltrato|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoMaltrato|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoMaltrato[]    findAll()
 * @method TipoMaltrato[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoMaltratoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoMaltrato::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('tipoMaltrato')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('tipoMaltrato.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['tipoMaltrato.nombre']);

        return $queryBuilder;
    }
}
