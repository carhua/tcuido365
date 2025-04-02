<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Denunciante;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Denunciante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Denunciante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Denunciante[]    findAll()
 * @method Denunciante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DenuncianteRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Denunciante::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('denunciante')
            ->select(['denunciante', 'centroPoblado'])
            ->distinct()
            ->join('denunciante.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['denunciante.nombres', 'denunciante.apellidos', 'denunciante.edad', 'denunciante.sexo']);

        $queryBuilder->groupBy('denunciante.numeroDocumento', 'denunciante.nombres', 'denunciante.apellidos');

        return $queryBuilder;
    }

    protected function filterQueryArray(array $params)
    {
        $queryBuilder = $this->createQueryBuilder('denunciante')
            ->select(['denunciante', 'centroPoblado'])
            ->join('denunciante.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('denunciante.numeroDocumento', 'denunciante.nombres', 'denunciante.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['denunciante.nombres', 'denunciante.apellidos', 'denunciante.edad', 'denunciante.sexo']);

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterExcel(array $params): array
    {
        $queryBuilder = $this->createQueryBuilder('denunciante')
            ->select(['denunciante', 'centroPoblado'])
            ->join('denunciante.centroPoblado', 'centroPoblado');

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
