<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Api\TraitParameter;
use App\Repository\BaseRepository;
use App\Repository\CasoViolenciaRepository;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

final class CasoViolenciaManager extends BaseManager
{
    use TraitParameter;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CasoViolenciaRepository $repository,
    ) {
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
              //  'mes' => (isset($queryValues['mes']) && '' !== $queryValues['mes']) ? (int) $queryValues['mes'] : null,
              //  'anio' => (isset($queryValues['anio']) && '' !== $queryValues['anio']) ? $queryValues['anio'] : null,
                'finicial' => (isset($queryValues['finicial']) && '' !== $queryValues['finicial']) ? $queryValues['finicial'] : null,
                'ffinal' => (isset($queryValues['ffinal']) && '' !== $queryValues['ffinal']) ? $queryValues['ffinal'] : null,
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'tipoMaltrato' => (isset($queryValues['tipoMaltrato']) && '' !== $queryValues['tipoMaltrato']) ? self::findTipoMaltratos($queryValues['tipoMaltrato'], $this->entityManager) : null,
                'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
                'provincia' => $queryValues['oprovincia'] ?? null,
                'distrito' => $queryValues['odistrito'] ?? null,
                'usuario' => $queryValues['usuario'] ?? null,
            ]
        );

        return $this->repository()->findLatestFechas($params);
    }

    public function listExcel(array $queryValues, $user): array
    {
        $params =
            [
            'finicial' => (isset($queryValues['finicial']) && '' !== $queryValues['finicial']) ? $queryValues['finicial'] : null,
            'ffinal' => (isset($queryValues['ffinal']) && '' !== $queryValues['ffinal']) ? $queryValues['ffinal'] : null,
            'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
            'tipoMaltrato' => (isset($queryValues['tipoMaltrato']) && '' !== $queryValues['tipoMaltrato']) ? self::findTipoMaltratos($queryValues['tipoMaltrato'], $this->entityManager) : null,
            'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
            'provincia' => $queryValues['oprovincia'] ?? null,
            'distrito' => $queryValues['odistrito'] ?? null,
            ];

        return $this->repository->filterExcelFechas($params);
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
                'tipoMaltrato' => (isset($queryValues['tipoMaltrato']) && '' !== $queryValues['tipoMaltrato']) ? self::findTipoMaltratos($queryValues['tipoMaltrato'], $this->entityManager) : null,
                'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
                'provincia' => $queryValues['oprovincia'] ?? null,
                'distrito' => $queryValues['odistrito'] ?? null,
                'anioInicio' => $queryValues['anioInicio'] ?? null,
                'anioFinal' => $queryValues['anioFinal'] ?? null,
                'usuario' => $queryValues['usuario'] ?? null,
            ]
        );

        return $this->repository->filterChartFechas($params);
    }
}
