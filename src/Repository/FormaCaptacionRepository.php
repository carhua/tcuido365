<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\FormaCaptacion;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormaCaptacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormaCaptacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormaCaptacion[]    findAll()
 * @method FormaCaptacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormaCaptacionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormaCaptacion::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('formaCaptacion')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('formaCaptacion.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['formaCaptacion.nombre']);

        return $queryBuilder;
    }
}
