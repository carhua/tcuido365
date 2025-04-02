<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Agraviado;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agraviado|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agraviado|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agraviado[]    findAll()
 * @method Agraviado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgraviadoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agraviado::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('agraviado')
            ->select(['agraviado', 'centroPoblado'])
            ->join('agraviado.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('agraviado.numeroDocumento', 'agraviado.nombres', 'agraviado.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['agraviado.nombres', 'agraviado.apellidos', 'agraviado.edad', 'agraviado.sexo']);

        return $queryBuilder;
    }

    protected function filterQueryArray(array $params)
    {
        $queryBuilder = $this->createQueryBuilder('agraviado')
            ->select(['agraviado', 'centroPoblado'])
            ->join('agraviado.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['agraviado.nombres', 'agraviado.apellidos', 'agraviado.edad', 'agraviado.sexo']);

        return $queryBuilder->getQuery()->getResult();
    }
}
