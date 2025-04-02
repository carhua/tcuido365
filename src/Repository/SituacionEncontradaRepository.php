<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\SituacionEncontrada;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SituacionEncontrada|null find($id, $lockMode = null, $lockVersion = null)
 * @method SituacionEncontrada|null findOneBy(array $criteria, array $orderBy = null)
 * @method SituacionEncontrada[]    findAll()
 * @method SituacionEncontrada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SituacionEncontradaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SituacionEncontrada::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('situacionEncontrada')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('situacionEncontrada.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['situacionEncontrada.nombre']);

        return $queryBuilder;
    }
}
