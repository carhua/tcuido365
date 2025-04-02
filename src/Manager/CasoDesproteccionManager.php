<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Api\TraitParameter;
use App\Entity\CasoDesproteccion;
use App\Repository\BaseRepository;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

final class CasoDesproteccionManager extends BaseManager
{
    use TraitParameter;
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(CasoDesproteccion::class);
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }

    public function listIndex(array $queryValues, int $page, $user): Pagerfanta
    {
        $params = Paginator::params($queryValues, $page);
        $params = array_merge(
            $params,
            [
                'finicial' => (isset($queryValues['finicial']) && '' !== $queryValues['finicial']) ? $queryValues['finicial'] : null,
                'ffinal' => (isset($queryValues['ffinal']) && '' !== $queryValues['ffinal']) ? $queryValues['ffinal'] : null,
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'situacion' => (isset($queryValues['situacion']) && '' !== $queryValues['situacion']) ? self::findSituacionEncontrada($queryValues['situacion'], $this->entityManager) : null,
                'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
                ]
        );

        return $this->repository()->findLatestFechas($params);
    }

    public function listExcel(array $queryValues, $user)
    {
        $params =
            [
            'finicial' => (isset($queryValues['finicial']) && '' !== $queryValues['finicial']) ? $queryValues['finicial'] : null,
            'ffinal' => (isset($queryValues['ffinal']) && '' !== $queryValues['ffinal']) ? $queryValues['ffinal'] : null,
            'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
            'situacion' => (isset($queryValues['situacion']) && '' !== $queryValues['situacion']) ? self::findSituacionEncontrada($queryValues['situacion'], $this->entityManager) : null,
            'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
            'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
            'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ];

        return $this->repository()->filterExcelFechas($params);
    }

    public function graficoCasos(array $queryValues)
    {
        $params = [];

        $params = array_merge(
            $params,
            [
                'finicial' => (isset($queryValues['finicial']) && '' !== $queryValues['finicial']) ? $queryValues['finicial'] : null,
                'ffinal' => (isset($queryValues['ffinal']) && '' !== $queryValues['ffinal']) ? $queryValues['ffinal'] : null,
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'situacion' => (isset($queryValues['situacion']) && '' !== $queryValues['situacion']) ? self::findSituacionEncontrada($queryValues['situacion'], $this->entityManager) : null,
                'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
                'provincia' => $queryValues['oprovincia'] ?? null,
                'distrito' => $queryValues['odistrito'] ?? null,
                'anioInicio' => $queryValues['anioInicio'] ?? null,
                'anioFinal' => $queryValues['anioFinal'] ?? null,
                'usuario' => $queryValues['usuario'] ?? null,
            ]
        );

        return $this->repository()->filterChartFechas($params);
    }
}
