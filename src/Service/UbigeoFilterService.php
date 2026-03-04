<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service;

use App\Entity\Usuario;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class UbigeoFilterService
{
    private EntityManagerInterface $entityManager;
    private Security $security;
    private ?Usuario $usuario = null;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    private function getUsuario(): ?Usuario
    {
        if ($this->usuario) {
            return $this->usuario;
        }

        $user = $this->security->getUser();
        if ($user instanceof Usuario) {
            return $user;
        }

        return null;
    }

    /**
     * Aplica filtros de ubigeo a un QueryBuilder según los permisos del usuario
     */
    public function applyFilters(QueryBuilder $queryBuilder, string $alias = 'caso'): QueryBuilder
    {
        $usuario = $this->getUsuario();
        if (!$usuario) {
            return $queryBuilder;
        }

        // Si es super admin, no aplicar filtros
        if ($this->isSuperAdmin()) {
            return $queryBuilder;
        }

        // Obtener la jerarquía de ubigeo del usuario
        $region = $usuario->getRegion();
        $provincia = $usuario->getProvincia();
        $distrito = $usuario->getDistrito();
        $centroPoblado = $usuario->getCentroPoblado();

        // Aplicar filtros según el nivel más específico disponible
        if ($centroPoblado && $centroPoblado->getId() !== 182) { // 182 parece ser el valor para "TODOS"
            $queryBuilder
                ->andWhere("{$alias}.centroPoblado = :centroPoblado")
                ->setParameter('centroPoblado', $centroPoblado);
        } elseif ($distrito && $distrito->getNombre() !== 'TODOS') {
            $queryBuilder
                ->innerJoin("{$alias}.centroPoblado", 'centroPoblado')
                ->innerJoin('centroPoblado.distrito', 'distrito')
                ->andWhere('distrito.id = :distrito')
                ->setParameter('distrito', $distrito);
        } elseif ($provincia && $provincia->getNombre() !== 'TODOS') {
            $queryBuilder
                ->innerJoin("{$alias}.centroPoblado", 'centroPoblado')
                ->innerJoin('centroPoblado.distrito', 'distrito')
                ->innerJoin('distrito.provincia', 'provincia')
                ->andWhere('provincia.id = :provincia')
                ->setParameter('provincia', $provincia);
        } elseif ($region) {
            $queryBuilder
                ->innerJoin("{$alias}.centroPoblado", 'centroPoblado')
                ->innerJoin('centroPoblado.distrito', 'distrito')
                ->innerJoin('distrito.provincia', 'provincia')
                ->innerJoin('provincia.region', 'region')
                ->andWhere('region.id = :region')
                ->setParameter('region', $region);
        }

        return $queryBuilder;
    }

    /**
     * Alias en español para applyFilters
     * Aplica filtros de ubigeo a un QueryBuilder según los permisos del usuario
     */
    public function aplicarFiltroQueryBuilder(QueryBuilder $queryBuilder, string $alias = 'cp'): QueryBuilder
    {
        $usuario = $this->getUsuario();
        if (!$usuario) {
            return $queryBuilder;
        }

        // Si es super admin, no aplicar filtros
        if ($this->isSuperAdmin()) {
            return $queryBuilder;
        }

        // Obtener la jerarquía de ubigeo del usuario
        $provincia = $usuario->getProvincia();
        $distrito = $usuario->getDistrito();
        $centroPoblado = $usuario->getCentroPoblado();

        // Aplicar filtros según el nivel más específico disponible
        if ($centroPoblado && $centroPoblado->getId() !== 182) {
            $queryBuilder
                ->andWhere("{$alias}.id = :centroPoblado")
                ->setParameter('centroPoblado', $centroPoblado->getId());
        } elseif ($distrito && $distrito->getNombre() !== 'TODOS') {
            $queryBuilder
                ->andWhere("{$alias}.distrito = :distrito")
                ->setParameter('distrito', $distrito->getId());
        } elseif ($provincia && $provincia->getNombre() !== 'TODOS') {
            $queryBuilder
                ->innerJoin("{$alias}.distrito", 'd')
                ->andWhere('d.provincia = :provincia')
                ->setParameter('provincia', $provincia->getId());
        }

        return $queryBuilder;
    }

    /**
     * Obtiene las provincias disponibles para el usuario
     */
    public function getProvinciasDisponibles(): array
    {
        $usuario = $this->getUsuario();
        if (!$usuario) {
            return [];
        }

        // Primero verificar si el usuario tiene una provincia específica configurada
        $provincia = $usuario->getProvincia();
        if ($provincia && $provincia->getNombre() !== 'TODOS') {
            return [$provincia];
        }

        // Solo si es super admin y no tiene provincia específica, mostrar todas
        if ($this->isSuperAdmin()) {
            return $this->entityManager
                ->getRepository(Provincia::class)
                ->findBy(['isActive' => true]);
        }

        return [];
    }

    /**
     * Obtiene los distritos disponibles para el usuario
     */
    public function getDistritosDisponibles(): array
    {
        $usuario = $this->getUsuario();
        if (!$usuario) {
            return [];
        }

        // Primero verificar si el usuario tiene un distrito específico configurado
        $distrito = $usuario->getDistrito();
        if ($distrito && $distrito->getNombre() !== 'TODOS') {
            return [$distrito];
        }

        // Si tiene provincia configurada, obtener distritos de esa provincia
        $provincia = $usuario->getProvincia();
        if ($provincia && $provincia->getNombre() !== 'TODOS') {
            return $this->entityManager
                ->getRepository(Distrito::class)
                ->findBy(['isActive' => true, 'provincia' => $provincia]);
        }

        // Solo si es super admin y no tiene restricciones, mostrar todos
        if ($this->isSuperAdmin()) {
            return $this->entityManager
                ->getRepository(Distrito::class)
                ->findBy(['isActive' => true]);
        }

        return [];
    }

    /**
     * Obtiene los centros poblados disponibles para el usuario
     */
    public function getCentrosPobladosDisponibles(): array
    {
        $usuario = $this->getUsuario();
        if (!$usuario) {
            return [];
        }

        // Primero verificar si el usuario tiene un centro poblado específico configurado
        $centroPoblado = $usuario->getCentroPoblado();
        if ($centroPoblado && $centroPoblado->getId() !== 182) {
            return [$centroPoblado];
        }

        // Si tiene distrito configurado, obtener centros de ese distrito
        $distrito = $usuario->getDistrito();
        if ($distrito && $distrito->getNombre() !== 'TODOS') {
            return $this->entityManager
                ->getRepository(CentroPoblado::class)
                ->findBy(['isActive' => true, 'distrito' => $distrito]);
        }

        // Si tiene provincia configurada, obtener centros de esa provincia
        $provincia = $usuario->getProvincia();
        if ($provincia && $provincia->getNombre() !== 'TODOS') {
            return $this->entityManager
                ->getRepository(CentroPoblado::class)
                ->findByProvinciaDistrito($provincia, null);
        }

        // Solo si es super admin y no tiene restricciones, mostrar todos
        if ($this->isSuperAdmin()) {
            return $this->entityManager
                ->getRepository(CentroPoblado::class)
                ->findBy(['isActive' => true]);
        }

        return [];
    }

    /**
     * Verifica si el usuario puede ver un caso específico según su ubigeo
     */
    public function puedeVerCaso($caso): bool
    {
        $usuario = $this->getUsuario();
        if (!$usuario || !$caso) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $casoCentroPoblado = $caso->getCentroPoblado();
        if (!$casoCentroPoblado) {
            return false;
        }

        // Obtener ubigeo del caso
        $casoDistrito = $casoCentroPoblado->getDistrito();
        $casoProvincia = $casoDistrito ? $casoDistrito->getProvincia() : null;
        $casoRegion = $casoProvincia ? $casoProvincia->getRegion() : null;

        // Verificar si el caso está en la jurisdicción del usuario
        $usuarioCentroPoblado = $usuario->getCentroPoblado();
        $usuarioDistrito = $usuario->getDistrito();
        $usuarioProvincia = $usuario->getProvincia();
        $usuarioRegion = $usuario->getRegion();

        // Nivel más específico: centro poblado
        if ($usuarioCentroPoblado && $usuarioCentroPoblado->getId() !== 182) {
            return $casoCentroPoblado->getId() === $usuarioCentroPoblado->getId();
        }

        // Nivel distrito
        if ($usuarioDistrito && $usuarioDistrito->getNombre() !== 'TODOS') {
            return $casoDistrito && $casoDistrito->getId() === $usuarioDistrito->getId();
        }

        // Nivel provincia
        if ($usuarioProvincia && $usuarioProvincia->getNombre() !== 'TODOS') {
            return $casoProvincia && $casoProvincia->getId() === $usuarioProvincia->getId();
        }

        // Nivel región
        if ($usuarioRegion) {
            return $casoRegion && $casoRegion->getId() === $usuarioRegion->getId();
        }

        return false;
    }

    private function isSuperAdmin(): bool
    {
        $usuario = $this->getUsuario();
        if (!$usuario) {
            return false;
        }

        foreach ($usuario->getRoles() as $role) {
            if ($role === 'ROLE_SUPER_ADMIN') {
                return true;
            }
        }

        return false;
    }

    private function isAutoridadComun(): bool
    {
        $usuario = $this->getUsuario();
        if (!$usuario) {
            return false;
        }

        foreach ($usuario->getRoles() as $role) {
            if ($role === 'ROLE_AUTORIDADCOMUN') {
                return true;
            }
        }

        return false;
    }
}
