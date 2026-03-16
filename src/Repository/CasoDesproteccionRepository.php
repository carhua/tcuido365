<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CasoDesproteccion;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Service\UbigeoFilterService;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CasoDesproteccion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasoDesproteccion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasoDesproteccion[]    findAll()
 * @method CasoDesproteccion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasoDesproteccionRepository extends BaseRepository
{
    private UbigeoFilterService $ubigeoFilterService;

    public function __construct(ManagerRegistry $registry, UbigeoFilterService $ubigeoFilterService)
    {
        parent::__construct($registry, CasoDesproteccion::class);
        $this->ubigeoFilterService = $ubigeoFilterService;
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoDesproteccion')
            ->select(['casoDesproteccion', 'centroPoblado'])
            ->join('casoDesproteccion.centroPoblado', 'centroPoblado')
           // ->join('casoDesproteccion.situacionEncontrada', 'situacion')
            // ->Where('MONTH(casoDesproteccion.fechaReporte) = :mes')
            ->Where('YEAR(casoDesproteccion.fechaReporte) = :anio')
           // ->setParameter('mes', $mesActual)
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoDesproteccion.fechaReporte) = :mes')
                ->setParameter('mes', $mesActual);
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                if (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                        ->setParameter('idcentro', $params['centroPoblado']);
                } else {
                    $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                        ->andWhere('distrito.id =:distritoId')
                        ->setParameter('distritoId', $distrito->getId());
                }
            } else {
                $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }

        if (null !== $params['situacion']) {
            $queryBuilder->andwhere('casoDesproteccion.situacionesEncontradas LIKE :tipo')
                ->setParameter('tipo', '%'.$params['situacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesproteccion.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoDesproteccion.id', 'DESC');
        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    protected function filterQueryFechas(array $params): QueryBuilder
    {
        $fi = new \DateTime($params['finicial']);
        $ff = new \DateTime($params['ffinal']);
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];

        $queryBuilder = $this->createQueryBuilder('casoDesproteccion')
            ->select(['casoDesproteccion', 'centroPoblado'])
            ->join('casoDesproteccion.centroPoblado', 'centroPoblado')

            ->andWhere('casoDesproteccion.fechaReporte BETWEEN :inicio AND :final')
            ->setParameter('inicio', $fi->format('Y-m-d'))
            ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                if (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                        ->setParameter('idcentro', $params['centroPoblado']);
                } else {
                    $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                        ->andWhere('distrito.id =:distritoId')
                        ->setParameter('distritoId', $distrito->getId());
                }
            } else {
                $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        } elseif (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        if (null !== $params['situacion']) {
            $queryBuilder->andwhere('casoDesproteccion.situacionesEncontradas LIKE :tipo')
                ->setParameter('tipo', '%'.$params['situacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesproteccion.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoDesproteccion.id', 'DESC');

        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    public function filterChart($centro, $provincia, $distrito)
    {
        $anioActual = date('Y');

        $queryBuilder = $this->createQueryBuilder('casoDesproteccion')
            ->select('COUNT(casoDesproteccion.id) as cantidad')
            ->addSelect('MONTH(casoDesproteccion.fechaReporte) as mes')
            ->where('YEAR(casoDesproteccion.fechaReporte) = :anio')
            ->setParameter('anio', $anioActual);

        if (null !== $centro) {
            $queryBuilder->andwhere('casoDesproteccion.centroPoblado = :idcentro')
                ->setParameter('idcentro', $centro);
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                $queryBuilder->innerJoin('casoDesproteccion.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('casoDesproteccion.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }

        $queryBuilder->addgroupBy('mes');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterChartPorAnios(?Provincia $provincia, ?Distrito $distrito, ?CentroPoblado $centro, ?string $usuario)
    {
        $queryBuilder = $this->createQueryBuilder('casoDesproteccion')
            ->select('YEAR(casoDesproteccion.fechaReporte) as anio')
            ->addSelect('COUNT(casoDesproteccion.id) as cantidad')
            ->innerJoin('casoDesproteccion.centroPoblado', 'cp');

        // Aplicar filtro automático por ubigeo del usuario
        $this->ubigeoFilterService->aplicarFiltroQueryBuilder($queryBuilder, 'cp');

        // Aplicar filtros manuales adicionales si se proporcionan
        if (null !== $centro) {
            $queryBuilder->andwhere('cp.id = :idcentro')
                ->setParameter('idcentro', $centro->getId());
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                $queryBuilder->innerJoin('cp.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('cp.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoDesproteccion.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }

        $queryBuilder->addgroupBy('anio');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterChartFechas(array $params)
    {
        $anioInicio = (int) ($params['anioInicio'] ?? 0);
        $anioFinal = (int) ($params['anioFinal'] ?? 0);
        $fi = null;
        $ff = null;
        if (!empty($params['finicial']) && !empty($params['ffinal'])) {
            $fi = new \DateTime($params['finicial']);
            $ff = new \DateTime($params['ffinal']);
        }
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];
        $usuario = $params['usuario'] ?? null;
        
        // Verificar si se pasó el servicio de filtrado
        $ubigeoFilter = $params['ubigeoFilter'] ?? null;

        $queryBuilder = $this->createQueryBuilder('casoDesproteccion')
            ->select('COUNT(casoDesproteccion.id) as cantidad')
            ->addSelect('YEAR(casoDesproteccion.fechaReporte) as anio')
            ->addSelect('MONTH(casoDesproteccion.fechaReporte) as mes')
            ->join('casoDesproteccion.centroPoblado', 'centroPoblado');

        if ($anioInicio > 0 && $anioFinal > 0) {
            $queryBuilder
                ->andWhere('YEAR(casoDesproteccion.fechaReporte) >= :anioInicio and YEAR(casoDesproteccion.fechaReporte) <= :anioFinal')
                ->setParameter('anioInicio', $anioInicio)
                ->setParameter('anioFinal', $anioFinal);
        }

        if (null !== $fi && null !== $ff) {
            $queryBuilder
                ->andWhere('casoDesproteccion.fechaReporte BETWEEN :inicio AND :final')
                ->setParameter('inicio', $fi->format('Y-m-d'))
                ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');
        }
        
        // Aplicar filtro automático por ubigeo del usuario si se proporcionó el servicio
        if ($ubigeoFilter) {
            $ubigeoFilter->aplicarFiltroQueryBuilder($queryBuilder, 'centroPoblado');
        }
        
        // Aplicar filtros adicionales seleccionados por el usuario
        if (!empty($params['centroPoblado']) && $params['centroPoblado'] !== 182) {
            $queryBuilder
                ->andWhere('centroPoblado.id = :centroPobladoId')
                ->setParameter('centroPobladoId', $params['centroPoblado']);
        } elseif (!empty($params['distrito'])) {
            $queryBuilder
                ->innerJoin('centroPoblado.distrito', 'distrito')
                ->andWhere('distrito.id = :distritoId')
                ->setParameter('distritoId', is_object($distrito) ? $distrito->getId() : $distrito);
        } elseif (!empty($params['provincia'])) {
            $queryBuilder
                ->innerJoin('centroPoblado.distrito', 'distrito')
                ->innerJoin('distrito.provincia', 'provincia')
                ->andWhere('provincia.id = :provinciaId')
                ->setParameter('provinciaId', is_object($provincia) ? $provincia->getId() : $provincia);
        }

        if (null !== $params['situacion']) {
            $queryBuilder->andwhere('casoDesproteccion.situacionesEncontradas LIKE :tipo')
                ->setParameter('tipo', '%'.$params['situacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesproteccion.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoDesproteccion.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }

        $queryBuilder
            ->addGroupBy('anio')
            ->addgroupBy('mes')
            ->orderBy('casoDesproteccion.fechaReporte', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterExcel(array $params): array
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoDesproteccion')
            // ->Where('MONTH(casoDesproteccion.fechaReporte) = :mes')
            ->Where('YEAR(casoDesproteccion.fechaReporte) = :anio')
            // ->setParameter('mes', $mesActual)
            // ->andWhere('casoDesproteccion.estadoCaso = :ecaso')
            // ->setParameter('ecaso','Notificado')
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoDesproteccion.fechaReporte) = :mes')
                ->setParameter('mes', $mesActual);
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                if (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('casoDesproteccion.centroPoblado= :idcentro')
                        ->setParameter('idcentro', $params['centroPoblado']);
                } else {
                    $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                        ->andWhere('distrito.id =:distritoId')
                        ->setParameter('distritoId', $distrito->getId());
                }
            } else {
                $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }

        /*   if (0 !== $params['situacion']) {
               $queryBuilder->andwhere('casoDesproteccion.situacionEncontrada = :idtipo')
                   ->setParameter('idtipo', $params['situacion']);
           }*/

        if (null !== $params['situacion']) {
            $queryBuilder->andwhere('casoDesproteccion.situacionesEncontradas LIKE :tipo')
                ->setParameter('tipo', '%'.$params['situacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesproteccion.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterExcelFechas(array $params): array
    {
        $fi = new \DateTime($params['finicial']);
        $ff = new \DateTime($params['ffinal']);
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];

        $queryBuilder = $this->createQueryBuilder('casoDesproteccion')
            ->select(['casoDesproteccion', 'centroPoblado'])
            ->join('casoDesproteccion.centroPoblado', 'centroPoblado')

            ->andWhere('casoDesproteccion.fechaReporte BETWEEN :inicio AND :final')
            ->setParameter('inicio', $fi->format('Y-m-d'))
            ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                if (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                        ->setParameter('idcentro', $params['centroPoblado']);
                } else {
                    $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                        ->andWhere('distrito.id =:distritoId')
                        ->setParameter('distritoId', $distrito->getId());
                }
            } else {
                $queryBuilder->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        } elseif (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        if (null !== $params['situacion']) {
            $queryBuilder->andwhere('casoDesproteccion.situacionesEncontradas LIKE :tipo')
                ->setParameter('tipo', '%'.$params['situacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesproteccion.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoDesproteccion.id', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
}
