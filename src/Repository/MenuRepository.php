<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Menu;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select(['menu', 'padre'])
            ->leftJoin('menu.padre', 'padre')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('menu.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['menu.nombre', 'padre.nombre']);

        return $queryBuilder;
    }

    public function findAllActive()
    {
        return $this->createQueryBuilder('menu')
            ->select('padre.nombre as padre_nombre')
            ->addSelect('menu.nombre as nombre')
            ->addSelect('menu.ruta as ruta')
            ->addSelect('menu.icono as icono')
            ->leftJoin('menu.padre', 'padre')
            ->where('menu.isActive = TRUE')
            ->orderBy('padre.orden', 'ASC')
            ->addOrderBy('menu.orden', 'ASC')
            ->addOrderBy('menu.nombre', 'ASC')
            ->getQuery()->getArrayResult();
    }

    /** NO VA A SER USADA */
    public function findListPadres(array $params): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->where('menu.padre is null')
        ;

        return Paginator::create($queryBuilder, $params);
    }
}
