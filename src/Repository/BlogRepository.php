<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Blog;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('blog')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('blog.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['blog.titulo']);

        return $queryBuilder;
    }

    public function findByNoticiasTotal()
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.provincia', 'provincia')
            ->innerJoin('a.distrito', 'distrito')
            ->innerJoin('a.centroPoblado', 'centroPoblado')
            ->where('provincia.nombre = :nprovincia')
            ->where('distrito.nombre = :ndistrito')
            ->where('centroPoblado.nombre = :ncentro')
            ->where('a.isActive = :active')
            ->setParameter('active', 1)
            ->setParameter('nprovincia', 'TODOS')
            ->setParameter('ndistrito', 'TODOS')
            ->setParameter('ncentro', 'TODOS')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByNoticiasDistrito($provinciaId, $distritoId, $centroId, $tipo)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.provincia', 'provincia')
            ->innerJoin('a.distrito', 'distrito')
            ->innerJoin('a.centroPoblado', 'centroPoblado')
            ->where('provincia.nombre = :nprovincia OR provincia.id = :pid')
            ->andWhere('distrito.nombre = :ndistrito OR distrito.id =:did')
            ->andWhere('centroPoblado.nombre = :ncentro OR centroPoblado.id = :cid')
            ->andWhere('a.isActive = :active')
            ->andWhere('a.tipo =:tipo')
            ->setParameter('active', 1)
            ->setParameter('nprovincia', 'TODOS')
            ->setParameter('ndistrito', 'TODOS')
            ->setParameter('ncentro', 'TODOS')
            ->setParameter('pid', $provinciaId)
            ->setParameter('did', $distritoId)
            ->setParameter('cid', $centroId)
            ->setParameter('tipo', $tipo)
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}
