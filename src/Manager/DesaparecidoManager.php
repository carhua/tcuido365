<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Entity\Desaparecido;
use App\Repository\BaseRepository;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

final class DesaparecidoManager extends BaseManager
{
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Desaparecido::class);
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

    public function listExcel(array $queryValues)
    {
        $params =
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
            ];

        return $this->repository()->filterExcel($params);
    }

    public function listPersona(array $queryValues)
    {
        $params =
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
            ];

        return $this->repository()->filterQueryArray($params);
    }
}
