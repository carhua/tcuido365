<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Desaparecido;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Desaparecido|null find($id, $lockMode = null, $lockVersion = null)
 * @method Desaparecido|null findOneBy(array $criteria, array $orderBy = null)
 * @method Desaparecido[]    findAll()
 * @method Desaparecido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DesaparecidoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Desaparecido::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('desaparecido')
            ->select(['desaparecido', 'centroPoblado'])
            ->distinct()
            ->join('desaparecido.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['desaparecido.nombres', 'desaparecido.apellidos', 'desaparecido.edad', 'desaparecido.sexo']);

        $queryBuilder->groupBy('desaparecido.numeroDocumento', 'desaparecido.nombres', 'desaparecido.apellidos');

        return $queryBuilder;
    }

    protected function filterQueryArray(array $params)
    {
        $queryBuilder = $this->createQueryBuilder('desaparecido')
            ->select(['desaparecido', 'centroPoblado'])
            ->join('desaparecido.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('desaparecido.numeroDocumento', 'desaparecido.nombres', 'desaparecido.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['desaparecido.nombres', 'desaparecido.apellidos', 'desaparecido.edad', 'desaparecido.sexo']);

        return $queryBuilder->getQuery()->getResult();
    }
}
