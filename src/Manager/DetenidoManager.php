<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Entity\Detenido;
use App\Repository\BaseRepository;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

final class DetenidoManager extends BaseManager
{
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Detenido::class);
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }

    public function list(array $queryValues, int $page): Pagerfanta
    {
        $params = Paginator::params($queryValues, $page);

        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
            ]
        );

        return $this->repository()->findLatest($params);
    }
}
