<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Tutor;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tutor[]    findAll()
 * @method Tutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TutorRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutor::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('tutor')
            ->select(['tutor', 'centroPoblado'])
            ->join('tutor.centroPoblado', 'centroPoblado')
        ;

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('tutor.numeroDocumento', 'tutor.nombres', 'tutor.apellidos');
        Paginator::queryTexts($queryBuilder, $params, ['tutor.nombres', 'tutor.apellidos', 'tutor.edad', 'tutor.sexo']);

        return $queryBuilder;
    }
}
