<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CentroPoblado;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CentroPoblado|null find($id, $lockMode = null, $lockVersion = null)
 * @method CentroPoblado|null findOneBy(array $criteria, array $orderBy = null)
 * @method CentroPoblado[]    findAll()
 * @method CentroPoblado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentroPobladoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CentroPoblado::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('centroPoblado')
        ;

        if ('' !== $params['active']) {
            $queryBuilder->where('centroPoblado.isActive = :active')
                ->setParameter('active', (bool) $params['active']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    public function findByDistrito($distritoId)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.distrito = :val')
            ->orWhere('d.nombre = :val2')
            ->setParameter('val', $distritoId)
            ->setParameter('val2', 'TODOS')
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByProvinciaDistrito($provincia, $distrito)
    {
        $provinciaId = $provincia->getId();
        $distritoId = $distrito->getId();
        if ('TODOS' === $provincia->getNombre()) {
            return $this->createQueryBuilder('c')
                ->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult();
        }
        if ('TODOS' === $distrito->getNombre()) {
            return $this->createQueryBuilder('c')
                    ->innerJoin('c.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->where('provincia.id = :val1')
                    ->orWhere('c.nombre = :val2')
                    ->setParameter('val1', $provinciaId)
                    ->setParameter('val2', 'TODOS')
                    ->orderBy('c.id', 'ASC')
                    ->getQuery()
                    ->getResult();
        }

        return $this->createQueryBuilder('c')
                    ->andWhere('c.distrito = :val1')
                    ->orWhere('c.nombre = :val2')
                    ->setParameter('val1', $distritoId)
                    ->setParameter('val2', 'TODOS')
                    ->orderBy('c.id', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}
