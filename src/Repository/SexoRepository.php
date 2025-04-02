<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Sexo;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sexo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sexo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sexo[]    findAll()
 * @method Sexo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SexoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sexo::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('sexo')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('sexo.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['sexo.nombre']);

        return $queryBuilder;
    }
}
