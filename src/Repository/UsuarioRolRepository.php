<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\UsuarioRol;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsuarioRol|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioRol|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioRol[]    findAll()
 * @method UsuarioRol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRolRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioRol::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('usuarioRol')
            ->select(['usuarioRol', 'owner', 'permisos'])
            ->leftJoin('usuarioRol.owner', 'owner')
            ->leftJoin('usuarioRol.permisos', 'permisos')
        ;

        Paginator::queryTexts($queryBuilder, $params, ['usuarioRol.nombre']);

        return $queryBuilder;
    }
}
