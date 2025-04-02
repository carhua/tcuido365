<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\ArchivoImportado;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArchivoImportado|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArchivoImportado|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArchivoImportado[]    findAll()
 * @method ArchivoImportado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchivoImportadoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArchivoImportado::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('name')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('archivo_importado.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['archivo_importado.name']);

        return $queryBuilder;
    }
}
