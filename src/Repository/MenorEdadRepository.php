<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\MenorEdad;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenorEdad|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenorEdad|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenorEdad[]    findAll()
 * @method MenorEdad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenorEdadRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenorEdad::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('menor')
            ->select(['menor', 'centroPoblado'])
            ->join('menor.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('menor.numeroDocumento', 'menor.nombres', 'menor.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['menor.nombres', 'menor.apellidos', 'menor.edad', 'menor.sexo']);

        return $queryBuilder;
    }
}
