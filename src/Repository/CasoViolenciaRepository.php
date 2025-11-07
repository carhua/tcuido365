<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CasoViolencia;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CasoViolencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasoViolencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasoViolencia[]    findAll()
 * @method CasoViolencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasoViolenciaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasoViolencia::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoViolencia')
            ->select(['casoViolencia', 'centroPoblado'])
            ->join('casoViolencia.centroPoblado', 'centroPoblado')
            //  ->join('casoViolencia.tipoMaltrato', 'tipoMaltrato')
            //  ->Where('MONTH(casoViolencia.fechaReporte) = :mes')
            ->where('YEAR(casoViolencia.fechaReporte) = :anio')
            //  ->setParameter('mes', $mesActual)
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoViolencia.fechaReporte) = :mes')
                ->setParameter('mes', $mesActual);
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
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

        if (null !== $params['tipoMaltrato']) {
            $queryBuilder->andwhere('casoViolencia.tipoMaltratos LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoMaltrato'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoViolencia.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoViolencia.id', 'DESC');

        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    protected function filterQueryFechas(array $params): QueryBuilder
    {
        $fi = new \DateTime($params['finicial']);
        $ff = new \DateTime($params['ffinal']);
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];
        $usuario = $params['usuario'];

        $queryBuilder = $this->createQueryBuilder('casoViolencia')
            ->select(['casoViolencia', 'centroPoblado'])
            ->join('casoViolencia.centroPoblado', 'centroPoblado')

            ->andWhere('casoViolencia.fechaReporte BETWEEN :inicio AND :final')
            ->setParameter('inicio', $fi->format('Y-m-d'))
            ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');

        // if($provincia != null && $distrito != null){
        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
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
        //  }

        if (null !== $params['tipoMaltrato']) {
            $queryBuilder->andwhere('casoViolencia.tipoMaltratos LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoMaltrato'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoViolencia.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoViolencia.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }

        $queryBuilder->orderBy('casoViolencia.id', 'DESC');

        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    public function filterChart($centro, $provincia, $distrito)
    {
        $anioActual = date('Y');

        $queryBuilder = $this->createQueryBuilder('casoViolencia')
            ->select('COUNT(casoViolencia.id) as cantidad')
            ->addSelect('MONTH(casoViolencia.fechaReporte) as mes')
            ->where('YEAR(casoViolencia.fechaReporte) = :anio')
            ->andWhere('casoViolencia.estadoCaso = :ecaso')
            ->setParameter('ecaso', 'Notificado')
            ->setParameter('anio', $anioActual);

        if (null !== $centro) {
            $queryBuilder->andwhere('casoViolencia.centroPoblado = :idcentro')
                ->setParameter('idcentro', $centro);
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                $queryBuilder->innerJoin('casoViolencia.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('casoViolencia.centroPoblado', 'centroPoblado')
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
        $queryBuilder = $this->createQueryBuilder('casoViolencia')
            ->select('YEAR(casoViolencia.fechaReporte) as anio')
            ->addSelect('COUNT(casoViolencia.id) as cantidad')
            ->where('casoViolencia.estadoCaso = :ecaso')
            ->setParameter('ecaso', 'Notificado');

        if (null !== $centro) {
            $queryBuilder->andwhere('casoViolencia.centroPoblado = :idcentro')
                ->setParameter('idcentro', $centro->getId());
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                $queryBuilder->innerJoin('casoViolencia.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('casoViolencia.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoViolencia.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }

        $queryBuilder->addgroupBy('anio');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterChartFechas(array $params)
    {
        $fi = new \DateTime($params['finicial']);
        $ff = new \DateTime($params['ffinal']);
        $anioInicio = (int) $params['anioInicio'];
        $anioFinal = (int) $params['anioFinal'];
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];
        $usuario = $params['usuario'] ?? null;

        $queryBuilder = $this->createQueryBuilder('casoViolencia')
            ->select('COUNT(casoViolencia.id) as cantidad')
            ->addSelect('YEAR(casoViolencia.fechaReporte) as anio')
            ->addSelect('MONTH(casoViolencia.fechaReporte) as mes')
            ->join('casoViolencia.centroPoblado', 'centroPoblado');

        if ($anioInicio === $anioFinal) {
            $queryBuilder
                ->andWhere('casoViolencia.fechaReporte BETWEEN :inicio AND :final')
                ->setParameter('inicio', $fi->format('Y-m-d'))
                ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');
        } else {
            $queryBuilder
                ->andWhere('YEAR(casoViolencia.fechaReporte) >= :anioInicio and YEAR(casoViolencia.fechaReporte) <= :anioFinal')
                ->setParameter('anioInicio', $anioInicio)
                ->setParameter('anioFinal', $anioFinal);
        }
        
        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
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

        if (null !== $params['tipoMaltrato']) {
            $queryBuilder->andwhere('casoViolencia.tipoMaltratos LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoMaltrato'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoViolencia.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoViolencia.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }

        $queryBuilder
            ->addGroupBy('anio')
            ->addgroupBy('mes')
            ->orderBy('casoViolencia.fechaReporte', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterExcel(array $params): array
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoViolencia')
            ->where('YEAR(casoViolencia.fechaReporte) = :anio')
            //   ->andWhere('casoViolencia.estadoCaso = :ecaso')
            //   ->setParameter('ecaso','Notificado')
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoViolencia.fechaReporte) = :mes')
                ->setParameter('mes', $mesActual);
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                if (182 !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('casoViolencia.centroPoblado = :idcentro')
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

        if (null !== $params['tipoMaltrato']) {
            $queryBuilder->andwhere('casoViolencia.tipoMaltratos LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoMaltrato'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoViolencia.estadoCaso = :ecaso')
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

        $queryBuilder = $this->createQueryBuilder('casoViolencia')
            ->select(['casoViolencia', 'centroPoblado'])
            ->join('casoViolencia.centroPoblado', 'centroPoblado')

            ->andWhere('casoViolencia.fechaReporte BETWEEN :inicio AND :final')
            ->setParameter('inicio', $fi->format('Y-m-d'))
            ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
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

        if (null !== $params['tipoMaltrato']) {
            $queryBuilder->andwhere('casoViolencia.tipoMaltratos LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoMaltrato'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoViolencia.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }
        $queryBuilder->orderBy('casoViolencia.id', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
}
