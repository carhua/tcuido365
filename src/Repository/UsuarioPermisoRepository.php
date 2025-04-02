<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\UsuarioPermiso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsuarioPermiso|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioPermiso|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioPermiso[]    findAll()
 * @method UsuarioPermiso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioPermisoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioPermiso::class);
    }

    public function findPermisosByUsuarioIdAndRuta(int $usuarioId, string $ruta = null): array
    {
        $queryBuilder = $this->createQueryBuilder('permiso')
            ->select('menu.ruta as route')
            ->addSelect('permiso.listar as list')
            ->addSelect('permiso.mostrar as view')
            ->addSelect('permiso.crear as new')
            ->addSelect('permiso.editar as edit')
            ->addSelect('permiso.eliminar as delete')
            ->addSelect('permiso.imprimir as print')
            ->addSelect('permiso.exportar as export')
            ->addSelect('permiso.importar as import')
            ->addSelect('permiso.maestro as master')
            ->join('permiso.menu', 'menu')
          //  ->join('menu.config', 'config')
            ->leftJoin('menu.padre', 'padre')
            ->leftJoin('permiso.roles', 'roles')
            ->leftJoin('roles.usuarios', 'usuarios')
            ->where('menu.isActive = TRUE')
            ->andWhere('usuarios.id = :usuario_id')
            ->setParameter('usuario_id', $usuarioId);

        if (null !== $ruta) {
            $queryBuilder
                ->andWhere('menu.route = :route')
                ->setParameter('route', $ruta);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findMenus(int $usuario_id)
    {
        return $this->createQueryBuilder('permiso')
            ->select('menu.nombre as nombre')
            ->addSelect('padre.nombre as padre_nombre')
            ->addSelect('menu.ruta as ruta')
            ->addSelect('permiso.listar as listar')
            ->addSelect('permiso.mostrar as mostrar')
            ->addSelect('permiso.crear as crear')
            ->addSelect('permiso.editar as editar')
            ->addSelect('permiso.eliminar as eliminar')
            ->addSelect('permiso.exportar as exportar')
            ->addSelect('permiso.maestro as maestro')
            ->addSelect('permiso.imprimir as imprimir')
            ->join('permiso.menu', 'menu')
            ->leftJoin('menu.padre', 'padre')
            ->leftJoin('permiso.roles', 'roles')
            ->leftJoin('roles.usuarios', 'usuarios')
            ->where('menu.isActive = TRUE')
            ->andWhere('usuarios.id = :usuario_id')
            ->setParameter('usuario_id', $usuario_id)
            ->getQuery()->getArrayResult();
    }
}
