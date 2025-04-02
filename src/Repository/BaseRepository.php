<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Base;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function findLatest(array $params): Pagerfanta
    {
        $queryBuilder = $this->filterQuery($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function findLatestFechas(array $params): Pagerfanta
    {
        $queryBuilder = $this->filterQueryFechas($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function filter(array $params, bool $inArray = true): array
    {
        $queryBuilder = $this->filterQuery($params);

        if ($inArray) {
            return $queryBuilder->getQuery()->getArrayResult();
        }

        return $queryBuilder->getQuery()->getResult();
    }

    abstract protected function filterQuery(array $params): QueryBuilder;

    public function save(Base $entity): bool
    {
        $this->_em->persist($entity);
        $this->_em->flush();

        return true;
    }

    public function saveUser(UserInterface $entity): bool
    {
        $this->_em->persist($entity);
        $this->_em->flush();

        return true;
    }

    public function remove(Base $entity): bool
    {
        $this->_em->remove($entity);
        $this->_em->flush();

        return true;
    }
}
