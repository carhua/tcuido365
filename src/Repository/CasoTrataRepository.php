<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\CasoTrata;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CasoTrata|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasoTrata|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasoTrata[]    findAll()
 * @method CasoTrata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasoTrataRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasoTrata::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoTrata')
            ->select(['casoTrata', 'centroPoblado'])
            ->join('casoTrata.centroPoblado', 'centroPoblado')
            ->where('YEAR(casoTrata.fechaReporte) = :anio')
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoTrata.fechaReporte) = :mes')
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

        if (null !== $params['tipoExplotacion']) {
            $queryBuilder->andwhere('casoTrata.tipoExplotacionesGeneral LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoExplotacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoTrata.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoTrata.id', 'DESC');
        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    protected function filterQueryFechas(array $params): QueryBuilder
    {
        $fi = new \DateTime($params['finicial']);
        $ff = new \DateTime($params['ffinal']);
        $provincia = $params['provincia'];
        $distrito = $params['distrito'];

        $queryBuilder = $this->createQueryBuilder('casoTrata')
            ->select(['casoTrata', 'centroPoblado'])
            ->join('casoTrata.centroPoblado', 'centroPoblado')

            ->andWhere('casoTrata.fechaReporte BETWEEN :inicio AND :final')
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

        if (null !== $params['tipoExplotacion']) {
            $queryBuilder->andwhere('casoTrata.tipoExplotacionesGeneral LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoExplotacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoTrata.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoTrata.id', 'DESC');

        Paginator::queryTexts($queryBuilder, $params, ['centroPoblado.nombre']);

        return $queryBuilder;
    }

    /*   public function filterChart($centro)
       {
           $anioActual = date('Y');

           $queryBuilder = $this->createQueryBuilder('casoTrata')
               ->select('COUNT(casoTrata.id) as cantidad')
               ->addSelect('MONTH(casoTrata.fechaReporte) as mes')
               ->where('YEAR(casoTrata.fechaReporte) = :anio')
               ->andWhere('casoTrata.estadoCaso = :ecaso')
               ->setParameter('ecaso','Notificado')
               ->setParameter('anio', $anioActual);


           if($centro != null){
               $queryBuilder->andwhere('casoTrata.centroPoblado = :idcentro')
                   ->setParameter('idcentro', $centro);
           }

           $queryBuilder->addgroupBy('mes');
           return $queryBuilder->getQuery()->getResult();

       }*/

    public function filterChart($centro, $provincia, $distrito)
    {
        $anioActual = date('Y');

        $queryBuilder = $this->createQueryBuilder('casoTrata')
            ->select('COUNT(casoTrata.id) as cantidad')
            ->addSelect('MONTH(casoTrata.fechaReporte) as mes')
            ->where('YEAR(casoTrata.fechaReporte) = :anio')
            ->andWhere('casoTrata.estadoCaso = :ecaso')
            ->setParameter('anio', $anioActual);

        if (null !== $centro) {
            $queryBuilder->andwhere('casoTrata.centroPoblado = :idcentro')
                ->setParameter('idcentro', $centro);
        }

        if ('TODOS' !== $provincia->getNombre() && null !== $provincia) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                $queryBuilder->innerJoin('casoTrata.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('casoTrata.centroPoblado', 'centroPoblado')
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
        $queryBuilder = $this->createQueryBuilder('casoTrata')
            ->select('YEAR(casoTrata.fechaReporte) as anio')
            ->addSelect('COUNT(casoTrata.id) as cantidad')
            ->where('casoTrata.estadoCaso = :ecaso')
            ->addSelect('COUNT(casoTrata.id) as cantidad');

        if (null !== $centro) {
            $queryBuilder->andwhere('casoTrata.centroPoblado = :idcentro')
                ->setParameter('idcentro', $centro->getId());
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                $queryBuilder->innerJoin('casoTrata.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->andWhere('distrito.id =:distritoId')
                    ->setParameter('distritoId', $distrito->getId());
            } else {
                $queryBuilder->innerJoin('casoTrata.centroPoblado', 'centroPoblado')
                    ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoTrata.usuarioApp = :usuario')
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

        $queryBuilder = $this->createQueryBuilder('casoTrata')
            ->select('COUNT(casoTrata.id) as cantidad')
            ->addSelect('YEAR(casoTrata.fechaReporte) as anio')
            ->addSelect('MONTH(casoTrata.fechaReporte) as mes')
            ->join('casoTrata.centroPoblado', 'centroPoblado');

        if ($anioInicio === $anioFinal) {
            $queryBuilder
                ->andWhere('casoTrata.fechaReporte BETWEEN :inicio AND :final')
                ->setParameter('inicio', $fi->format('Y-m-d'))
                ->setParameter('final', $ff->format('Y-m-d').' 23:59:59');
        } else {
            $queryBuilder
                ->andWhere('YEAR(casoTrata.fechaReporte) >= :anioInicio and YEAR(casoTrata.fechaReporte) <= :anioFinal')
                ->setParameter('anioInicio', $anioInicio)
                ->setParameter('anioFinal', $anioFinal);
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
        } elseif (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        if (null !== $params['tipoExplotacion']) {
            $queryBuilder->andwhere('casoTrata.tipoExplotacionesGeneral LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoExplotacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoTrata.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        if (null !== $usuario && '' !== $usuario) {
            $queryBuilder
                ->andwhere('casoTrata.usuarioApp = :usuario')
                ->setParameter('usuario', $usuario);
        }

        $queryBuilder
            ->addGroupBy('anio')
            ->addgroupBy('mes')
            ->orderBy('casoTrata.fechaReporte', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function filterExcel(array $params): array
    {
        $anioActual = $params['anio'];
        $mesActual = $params['mes'];
        $provincia = $params['provinciaUser'];
        $distrito = $params['distritoUser'];

        $queryBuilder = $this->createQueryBuilder('casoTrata')
            ->where('YEAR(casoTrata.fechaReporte) = :anio')
            ->setParameter('anio', $anioActual);

        if (0 !== $params['mes']) {
            $queryBuilder->andWhere('MONTH(casoTrata.fechaReporte) = :mes')
                ->setParameter('mes', $mesActual);
        }

        if (null !== $provincia && 'TODOS' !== $provincia->getNombre()) {
            if (null !== $distrito && 'TODOS' !== $distrito->getNombre()) {
                if (182 !== $params['centroPoblado'] && null !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('casoTrato.centroPoblado= :idcentro')
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

        if (null !== $params['tipoExplotacion']) {
            $queryBuilder->andwhere('casoTrata.tipoExplotacionesGeneral LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoExplotacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoTrata.estadoCaso = :ecaso')
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

        $queryBuilder = $this->createQueryBuilder('casoTrata')
            ->select(['casoTrata', 'centroPoblado'])
            ->join('casoTrata.centroPoblado', 'centroPoblado')

            ->andWhere('casoTrata.fechaReporte BETWEEN :inicio AND :final')
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

        if (null !== $params['tipoExplotacion']) {
            $queryBuilder->andwhere('casoTrata.tipoExplotacionesGeneral LIKE :tipo')
                ->setParameter('tipo', '%'.$params['tipoExplotacion'].'%');
        }

        if (null !== $params['estado']) {
            $queryBuilder->andwhere('casoTrata.estadoCaso = :ecaso')
                ->setParameter('ecaso', $params['estado']);
        }

        $queryBuilder->orderBy('casoTrata.id', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
}
