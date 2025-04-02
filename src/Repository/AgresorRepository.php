<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Agresor;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agresor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agresor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agresor[]    findAll()
 * @method Agresor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgresorRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agresor::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('agresor')
            ->select(['agresor', 'centroPoblado'])
            ->join('agresor.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }
        $queryBuilder->groupBy('agresor.numeroDocumento', 'agresor.nombres', 'agresor.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['agresor.nombres', 'agresor.apellidos', 'agresor.edad', 'agresor.sexo']);

        return $queryBuilder;
    }

    protected function filterQueryArray(array $params)
    {
        $queryBuilder = $this->createQueryBuilder('agresor')
            ->select(['agresor', 'centroPoblado'])
            ->join('agresor.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('agresor.numeroDocumento', 'agresor.nombres', 'agresor.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['agresor.nombres', 'agresor.apellidos', 'agresor.edad', 'agresor.sexo']);

        return $queryBuilder->getQuery()->getResult();
    }
}
