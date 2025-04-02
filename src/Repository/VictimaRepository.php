<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Victima;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Victima|null find($id, $lockMode = null, $lockVersion = null)
 * @method Victima|null findOneBy(array $criteria, array $orderBy = null)
 * @method Victima[]    findAll()
 * @method Victima[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VictimaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Victima::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('detenido')
            ->select(['detenido', 'centroPoblado'])
            ->join('detenido.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('detenido.numeroDocumento', 'detenido.nombres', 'detenido.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['detenido.nombres', 'detenido.apellidos', 'detenido.edad', 'detenido.sexo']);

        return $queryBuilder;
    }

    protected function filterQueryArray(array $params)
    {
        $queryBuilder = $this->createQueryBuilder('detenido')
            ->select(['detenido', 'centroPoblado'])
            ->join('detenido.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['detenido.nombres', 'detenido.apellidos', 'detenido.edad', 'detenido.sexo']);

        return $queryBuilder->getQuery()->getResult();
    }
}
