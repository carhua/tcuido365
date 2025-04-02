<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Usuario;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('usuario')
            ->select(['usuario', 'usuarioRoles'])
            ->leftJoin('usuario.usuarioRoles', 'usuarioRoles')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('usuario.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['usuario.fullName', 'usuario.username']);

        return $queryBuilder;
    }

    public function allNombres(): array
    {
        return $this->createQueryBuilder('usuario')
            ->select('usuario.username as username')
            ->addSelect('usuario.fullName as nombre')
            ->where('usuario.isActive = true')
            ->orderBy('usuario.fullName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
