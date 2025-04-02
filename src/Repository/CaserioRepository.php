<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Caserio;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Caserio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Caserio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Caserio[]    findAll()
 * @method Caserio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaserioRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Caserio::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('caserio')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('caserio.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['caserio.nombre']);

        return $queryBuilder;
    }
}
