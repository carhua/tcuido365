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
use App\Service\UbigeoFilterService;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

final class CasoViolenciaManager extends BaseManager
{
    use TraitParameter;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CasoViolenciaRepository $repository,
        private readonly UbigeoFilterService $ubigeoFilter,
    ) {
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }

    public function listIndex(array $queryValues, int $page, $user): Pagerfanta
    {
        // Configurar el usuario en el servicio de filtrado
        $this->ubigeoFilter->setUsuario($user);
        
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
                'ubigeoFilter' => $this->ubigeoFilter, // Pasar el servicio al repositorio
            ]
        );

        return $this->repository()->findLatestFechas($params);
    }

    public function listExcel(array $queryValues, $user): array
    {
        // Configurar el usuario en el servicio de filtrado
        $this->ubigeoFilter->setUsuario($user);
        
        $params =
            [
            'finicial' => (isset($queryValues['finicial']) && '' !== $queryValues['finicial']) ? $queryValues['finicial'] : null,
            'ffinal' => (isset($queryValues['ffinal']) && '' !== $queryValues['ffinal']) ? $queryValues['ffinal'] : null,
            'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
            'tipoMaltrato' => (isset($queryValues['tipoMaltrato']) && '' !== $queryValues['tipoMaltrato']) ? self::findTipoMaltratos($queryValues['tipoMaltrato'], $this->entityManager) : null,
            'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
            'provincia' => $queryValues['oprovincia'] ?? null,
            'distrito' => $queryValues['odistrito'] ?? null,
            'ubigeoFilter' => $this->ubigeoFilter, // Pasar el servicio al repositorio
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
                'centroPoblado' => (!empty($queryValues['centroPoblado'])) ? (int) $queryValues['centroPoblado'] : null,
                'tipoMaltrato' => (isset($queryValues['tipoMaltrato']) && '' !== $queryValues['tipoMaltrato']) ? self::findTipoMaltratos($queryValues['tipoMaltrato'], $this->entityManager) : null,
                'estado' => (isset($queryValues['estado']) && '' !== $queryValues['estado']) ? $queryValues['estado'] : null,
                'provincia' => !empty($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => !empty($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
                'anioInicio' => $queryValues['anioInicio'] ?? null,
                'anioFinal' => $queryValues['anioFinal'] ?? null,
                'usuario' => $queryValues['usuario'] ?? null,
                'ubigeoFilter' => $this->ubigeoFilter, // Pasar el servicio al repositorio
            ]
        );

        return $this->repository->filterChartFechas($params);
    }
}
