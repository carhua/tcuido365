<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Entity\Persona;
use App\Repository\BaseRepository;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

final class PersonaManager extends BaseManager
{
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Persona::class);
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

    public function listHistorialViolencia(array $queryValues, ?int $page, $user)
    {
        $params = Paginator::params($queryValues, $page);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ]
        );

        $queryBuilder = $this->repository()->filterQueryViolencia($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function listHistorialViolenciaExcel(array $queryValues, $user)
    {
        $params = Paginator::params($queryValues, 1);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ]
        );
        $queryBuilder = $this->repository()->filterQueryViolencia($params);

        return $queryBuilder->getQuery()->getResult();
    }

    public function listHistorialDesproteccion(array $queryValues, $page, $user)
    {
        $params = Paginator::params($queryValues, $page);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
                ]
        );

        $queryBuilder = $this->repository()->filterQueryDesproteccion($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function listHistorialDesproteccionExcel(array $queryValues, $user)
    {
        $params = Paginator::params($queryValues, 1);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ]
        );
        $queryBuilder = $this->repository()->filterQueryDesproteccion($params);

        return $queryBuilder->getQuery()->getResult();
    }

    public function listHistorialTipoViolencia(array $queryValues, $page, $tipo, $user): Pagerfanta
    {
        $params = Paginator::params($queryValues, $page);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => $queryValues['oprovincia'] ?? null,
                'distrito' => $queryValues['odistrito'] ?? null,
            ]
        );
        $queryBuilder = $this->repository()->filterQueryTipoViolencia($params, $tipo);

        return Paginator::create($queryBuilder, $params);
    }

    public function listHistorialTipoViolenciaExcel(array $queryValues, $tipo, $user)
    {
        $params = Paginator::params($queryValues, 1);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
                ]
        );
        $queryBuilder = $this->repository()->filterQueryTipoViolencia($params, $tipo);

        return $queryBuilder->getQuery()->getResult();
    }

    public function listHistorialTrata(array $queryValues, $page, $user)
    {
        $params = Paginator::params($queryValues, $page);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ]
        );

        $queryBuilder = $this->repository()->filterQueryTrata($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function listHistorialTrataExcel(array $queryValues, $user)
    {
        $params = Paginator::params($queryValues, 1);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ]
        );

        $queryBuilder = $this->repository()->filterQueryTrata($params);

        return $queryBuilder->getQuery()->getResult();
    }

    public function listHistorialDesaparecido(array $queryValues, $page, $user)
    {
        $params = Paginator::params($queryValues, $page);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ]
        );

        $queryBuilder = $this->repository()->filterQueryDesaparecido($params);

        return Paginator::create($queryBuilder, $params);
    }

    public function listHistorialDesaparecidoExcel(array $queryValues, $user)
    {
        $params = Paginator::params($queryValues, 1);
        $params = array_merge(
            $params,
            [
                'centroPoblado' => (isset($queryValues['centroPoblado']) && '' !== $queryValues['centroPoblado']) ? (int) $queryValues['centroPoblado'] : null,
                'provinciaUser' => $user->getProvincia(),
                'distritoUser' => $user->getDistrito(),
                'provincia' => isset($queryValues['oprovincia']) ? $queryValues['oprovincia'] : null,
                'distrito' => isset($queryValues['odistrito']) ? $queryValues['odistrito'] : null,
            ]
        );

        $queryBuilder = $this->repository()->filterQueryDesaparecido($params);

        return $queryBuilder->getQuery()->getResult();
    }
}
