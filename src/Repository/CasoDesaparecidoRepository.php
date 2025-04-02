<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CasoDesaparecido;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CasoDesaparecido|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasoDesaparecido|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasoDesaparecido[]    findAll()
 * @method CasoDesaparecido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasoDesaparecidoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasoDesaparecido::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
            ->select(['casoDesaparecido', 'centroPoblado'])
            ->join('casoDesaparecido.centroPoblado', 'centroPoblado')
            ->where('YEAR(casoDesaparecido.fechaReporte) = :anio')
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoDesaparecido.fechaReporte) = :mes')
                ->setParameter('mes', $mesActual);
        }

        if ('TODOS' !== $provincia->getNombre() && null !== $provincia) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                if (182 !== $params['centroPoblado']) {
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

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesaparecido.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoDesaparecido.id', 'DESC');
        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    protected function filterQueryFechas(array $params): QueryBuilder
    {
        $fi = new \DateTime($params['finicial']);
        $ff = new \DateTime($params['ffinal']);
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];

        $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
            ->select(['casoDesaparecido', 'centroPoblado'])
            ->join('casoDesaparecido.centroPoblado', 'centroPoblado')
            ->andWhere('casoDesaparecido.fechaReporte BETWEEN :inicio AND :final')
            ->setParameter('inicio', $fi->format('Y-m-d'))
            ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');

        if ('TODOS' !== $provincia->getNombre()) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                if (182 !== $params['centroPoblado']) {
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
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesaparecido.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoDesaparecido.id', 'DESC');

        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    /*    public function filterChart($centro)
        {
            $anioActual = date('Y');

            $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
                ->select('COUNT(casoDesaparecido.id) as cantidad')
                ->addSelect('MONTH(casoDesaparecido.fechaReporte) as mes')
                ->where('YEAR(casoDesaparecido.fechaReporte) = :anio')
                ->andWhere('casoDesaparecido.estadoCaso = :ecaso')
                ->setParameter('ecaso','Notificado')
                ->setParameter('anio', $anioActual);


            if($centro != null){
                $queryBuilder->andwhere('casoDesaparecido.centroPoblado = :idcentro')
                    ->setParameter('idcentro', $centro);
            }

            $queryBuilder->addgroupBy('mes');
            return $queryBuilder->getQuery()->getResult();

        }*/

    public function filterChart($centro, $provincia, $distrito)
    {
        $anioActual = date('Y');

        $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
            ->select('COUNT(casoDesaparecido.id) as cantidad')
            ->addSelect('MONTH(casoDesaparecido.fechaReporte) as mes')
            ->where('YEAR(casoDesaparecido.fechaReporte) = :anio')
            ->andWhere('casoDesaparecido.estadoCaso = :ecaso')
            ->setParameter('ecaso', 'Notificado')
            ->setParameter('anio', $anioActual);

        if (null !== $centro) {
            $queryBuilder->andwhere('casoDesaparecido.centroPoblado = :idcentro')
                ->setParameter('idcentro', $centro);
        }

        if ('TODOS' !== $provincia->getNombre() && null !== $provincia) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                $queryBuilder->innerJoin('casoDesaparecido.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('casoDesaparecido.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }
        $queryBuilder->addgroupBy('mes');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterChartPorAnios(?Provincia $provincia, ?Distrito $distrito, ?CentroPoblado $centro, ?string $usuario): array
    {
        $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
            ->select('YEAR(casoDesaparecido.fechaReporte) as anio')
            ->addSelect('COUNT(casoDesaparecido.id) as cantidad')
            ->where('casoDesaparecido.estadoCaso = :ecaso')
            ->setParameter('ecaso', 'Notificado');

        if (null !== $centro) {
            $queryBuilder->andwhere('casoDesaparecido.centroPoblado = :idcentro')
                ->setParameter('idcentro', $centro->getId());
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                $queryBuilder->innerJoin('casoDesaparecido.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('casoDesaparecido.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoDesaparecido.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }
        $queryBuilder->addgroupBy('anio');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterChartFechas(array $params)
    {
        $anioInicio = (int) $params['anioInicio'];
        $anioFinal = (int) $params['anioFinal'];
        $fi = new \DateTime($params['finicial']);
        $ff = new \DateTime($params['ffinal']);
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];
        $usuario = $params['usuario'] ?? null;

        $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
            ->select('COUNT(casoDesaparecido.id) as cantidad')
            ->addSelect('YEAR(casoDesaparecido.fechaReporte) as anio')
            ->addSelect('MONTH(casoDesaparecido.fechaReporte) as mes')
            ->join('casoDesaparecido.centroPoblado', 'centroPoblado');

        if ($anioInicio === $anioFinal) {
            $queryBuilder
                ->andWhere('casoDesaparecido.fechaReporte BETWEEN :inicio AND :final')
                ->setParameter('inicio', $fi->format('Y-m-d'))
                ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');
        } else {
            $queryBuilder
                ->andWhere('YEAR(casoDesaparecido.fechaReporte) >= :anioInicio and YEAR(casoDesaparecido.fechaReporte) <= :anioFinal')
                ->setParameter('anioInicio', $anioInicio)
                ->setParameter('anioFinal', $anioFinal);
        }

        if ('TODOS' !== $provincia->getNombre()) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                if (182 !== $params['centroPoblado']) {
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
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesaparecido.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoDesaparecido.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }

        $queryBuilder
            ->addGroupBy('anio')
            ->addgroupBy('mes')
            ->orderBy('casoDesaparecido.fechaReporte', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterExcel(array $params): array
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
            ->where('YEAR(casoDesaparecido.fechaReporte) = :anio')
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoDesaparecido.fechaReporte) = :mes')
                ->setParameter('mes', $mesActual);
        }

        if ('TODOS' !== $provincia->getNombre() && null !== $provincia) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                if (182 !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('casoDesaparecido.centroPoblado = :idcentro')
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

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesaparecido.estadoCaso = :ecaso')
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

        $queryBuilder = $this->createQueryBuilder('casoDesaparecido')
            ->select(['casoDesaparecido', 'centroPoblado'])
            ->join('casoDesaparecido.centroPoblado', 'centroPoblado')
            ->andWhere('casoDesaparecido.fechaReporte BETWEEN :inicio AND :final')
            ->setParameter('inicio', $fi->format('Y-m-d'))
            ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');

        if ('TODOS' !== $provincia->getNombre()) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                if (182 !== $params['centroPoblado']) {
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
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoDesaparecido.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoDesaparecido.id', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
}
