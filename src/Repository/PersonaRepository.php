<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Persona;
use App\Utils\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Persona|null find($id, $lockMode = null, $lockVersion = null)
 * @method Persona|null findOneBy(array $criteria, array $orderBy = null)
 * @method Persona[]    findAll()
 * @method Persona[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonaRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Persona::class);
    }

    protected function filterQuery(array $params): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('persona')
            ->select(['persona', 'centroPoblado'])
            ->join('persona.centroPoblado', 'centroPoblado');

        if (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        $queryBuilder->groupBy('persona.numeroDocumento', 'persona.nombres', 'persona.apellidos');

        Paginator::queryTexts($queryBuilder, $params, ['persona.nombres', 'persona.apellidos', 'persona.edad', 'persona.sexo']);

        return $queryBuilder;
    }

    public function filterQueryViolencia(array $params): QueryBuilder
    {
        if (null !== $params['provincia'] && null !== $params['distrito']) {
            $provincia = $params['provincia'];
            $distrito = $params['distrito'];
        } else {
            $provincia = $params['provinciaUser'];
            $distrito = $params['distritoUser'];
        }

        $queryBuilder = $this->createQueryBuilder('persona')
            ->select(['persona', 'centroPoblado'])
            ->join('persona.centroPoblado', 'centroPoblado')
            ->where('persona.casoViolencia = :cviolencia')
            ->setParameter('cviolencia', 1)
            ->orderBy('persona.casoViolenciaTotal', 'DESC')
            ->addOrderBy('persona.nombres', 'ASC');

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
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['persona.nombres', 'persona.edad', 'persona.sexo']);

        return $queryBuilder;
    }

    public function filterQueryDesproteccion(array $params): QueryBuilder
    {
        if (null !== $params['provincia'] && null !== $params['distrito']) {
            $provincia = $params['provincia'];
            $distrito = $params['distrito'];
        } else {
            $provincia = $params['provinciaUser'];
            $distrito = $params['distritoUser'];
        }

        $queryBuilder = $this->createQueryBuilder('persona')
            ->select(['persona', 'centroPoblado'])
            ->join('persona.centroPoblado', 'centroPoblado')
            ->where('persona.casoDesproteccion = :cdes')
            ->setParameter('cdes', 1)
            ->orderBy('persona.casoDesproteccionTotal', 'DESC')
            ->addOrderBy('persona.nombres', 'ASC');

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
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }
        Paginator::queryTexts($queryBuilder, $params, ['persona.nombres', 'persona.edad', 'persona.sexo']);

        return $queryBuilder;
    }

    public function filterQueryTipoViolencia(array $params, $tipo): QueryBuilder
    {
        if (null !== $params['provincia'] && null !== $params['distrito']) {
            $provincia = $params['provincia'];
            $distrito = $params['distrito'];
        } else {
            $provincia = $params['provinciaUser'];
            $distrito = $params['distritoUser'];
        }

        $queryBuilder = $this->createQueryBuilder('persona')
            ->select(['persona', 'centroPoblado', 'distrito'])
            ->addSelect('persona.id as id')
            ->addSelect('distrito.nombre as nombreDistrito')
            ->addSelect('centroPoblado.nombre as nombreCentro')
            ->addSelect('persona.tipoDocumento as tipoDocumento')
            ->addSelect('persona.numeroDocumento as numeroDocumento')
            ->addSelect('persona.nombres as nombres')
            ->addSelect('persona.apellidos as apellidos')
            ->addSelect('persona.edad as edad')
            ->addSelect('persona.sexo as sexo');
        if (1 === (int) $tipo) {
            $queryBuilder->addSelect("(SELECT COUNT(cva.casoViolencia) FROM App\Entity\CasoViolenciaAgresor cva JOIN cva.agresor agre JOIN cva.casoViolencia cv WHERE agre.persona = persona.id AND cv.tipoMaltratos LIKE :tp1 AND cv.estadoCaso='Notificado') as tipoPsicologico ")
                ->addSelect("(SELECT COUNT(cva2.casoViolencia) FROM App\Entity\CasoViolenciaAgresor cva2 JOIN cva2.agresor agre2 JOIN cva2.casoViolencia cv2 WHERE agre2.persona = persona.id AND cv2.tipoMaltratos LIKE :tp2 AND cv2.estadoCaso='Notificado') as tipoFisico ")
                ->addSelect("(SELECT COUNT(cva3.casoViolencia) FROM App\Entity\CasoViolenciaAgresor cva3 JOIN cva3.agresor agre3 JOIN cva3.casoViolencia cv3 WHERE agre3.persona = persona.id AND cv3.tipoMaltratos LIKE :tp3 AND cv3.estadoCaso='Notificado') as tipoSexual ")
                ->addSelect("(SELECT COUNT(cva4.casoViolencia) FROM App\Entity\CasoViolenciaAgresor cva4 JOIN cva4.agresor agre4 JOIN cva4.casoViolencia cv4 WHERE agre4.persona = persona.id AND cv4.tipoMaltratos LIKE :tp4 AND cv4.estadoCaso='Notificado') as tipoEconomico ")
                ->setParameter('tp1', '%Psicologico%')
                ->setParameter('tp2', '%Fisico%')
                ->setParameter('tp3', '%Sexual%')
                ->setParameter('tp4', '%Economico%');
        } else {
            $queryBuilder->addSelect("(SELECT COUNT(cva.casoViolencia) FROM App\Entity\CasoViolenciaAgraviado cva JOIN cva.agraviado agre JOIN cva.casoViolencia cv WHERE agre.persona = persona.id AND cv.tipoMaltratos LIKE :tp1  AND cv.estadoCaso='Notificado') as tipoPsicologico ")
                ->addSelect("(SELECT COUNT(cva2.casoViolencia) FROM App\Entity\CasoViolenciaAgraviado cva2 JOIN cva2.agraviado agre2 JOIN cva2.casoViolencia cv2 WHERE agre2.persona = persona.id AND cv2.tipoMaltratos LIKE :tp2  AND cv2.estadoCaso='Notificado') as tipoFisico ")
                ->addSelect("(SELECT COUNT(cva3.casoViolencia) FROM App\Entity\CasoViolenciaAgraviado cva3 JOIN cva3.agraviado agre3 JOIN cva3.casoViolencia cv3 WHERE agre3.persona = persona.id AND cv3.tipoMaltratos LIKE :tp3  AND cv3.estadoCaso='Notificado') as tipoSexual ")
                ->addSelect("(SELECT COUNT(cva4.casoViolencia) FROM App\Entity\CasoViolenciaAgraviado cva4 JOIN cva4.agraviado agre4 JOIN cva4.casoViolencia cv4 WHERE agre4.persona = persona.id AND cv4.tipoMaltratos LIKE :tp4  AND cv4.estadoCaso='Notificado') as tipoEconomico ")
                ->setParameter('tp1', '%Psicologico%')
                ->setParameter('tp2', '%Fisico%')
                ->setParameter('tp3', '%Sexual%')
                ->setParameter('tp4', '%Economico%');
        }

        $queryBuilder->join('persona.centroPoblado', 'centroPoblado')
        ->join('centroPoblado.distrito', 'distrito')
        ->add('orderBy', 'tipoPsicologico DESC, tipoFisico DESC, tipoSexual DESC, tipoEconomico DESC');

        if ('TODOS' !== $provincia->getNombre() && null !== $provincia) {
            if ('TODOS' !== $distrito->getNombre() && null !== $distrito) {
                if (182 !== $params['centroPoblado']) {
                    $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                        ->setParameter('idcentro', $params['centroPoblado']);
                } else {
                    $queryBuilder // ->innerJoin('centroPoblado.distrito', 'distrito')
                        ->andWhere('distrito.id =:distritoId')
                        ->setParameter('distritoId', $distrito->getId());
                }
            } else {
                $queryBuilder // ->innerJoin('centroPoblado.distrito', 'distrito')
                    ->innerJoin('distrito.provincia', 'provincia')
                    ->andWhere('provincia.id =:provinciaId')
                    ->setParameter('provinciaId', $provincia->getId());
            }
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['persona.nombres', 'persona.apellidos', 'persona.edad', 'persona.sexo']);

        return $queryBuilder;
    }

    public function filterQueryTrata(array $params): QueryBuilder
    {
        if (null !== $params['provincia'] && null !== $params['distrito']) {
            $provincia = $params['provincia'];
            $distrito = $params['distrito'];
        } else {
            $provincia = $params['provinciaUser'];
            $distrito = $params['distritoUser'];
        }

        $queryBuilder = $this->createQueryBuilder('persona')
            ->select(['persona', 'centroPoblado'])
            ->join('persona.centroPoblado', 'centroPoblado')
            ->where('persona.casoTrata = :cdes')
            ->setParameter('cdes', 1)
            ->orderBy('persona.casoTrataTotal', 'DESC')
            ->addOrderBy('persona.nombres', 'ASC');

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
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['persona.nombres', 'persona.edad', 'persona.sexo']);

        return $queryBuilder;
    }

    public function filterQueryDesaparecido(array $params): QueryBuilder
    {
        if (null !== $params['provincia'] && null !== $params['distrito']) {
            $provincia = $params['provincia'];
            $distrito = $params['distrito'];
        } else {
            $provincia = $params['provinciaUser'];
            $distrito = $params['distritoUser'];
        }

        $queryBuilder = $this->createQueryBuilder('persona')
            ->select(['persona', 'centroPoblado', 'desaparecidos', 'denuncianteDesaparecidos'])
            ->join('persona.centroPoblado', 'centroPoblado')
            ->leftJoin('persona.denuncianteDesaparecidos', 'denuncianteDesaparecidos')
            ->leftJoin('persona.desaparecidos', 'desaparecidos')
            ->where('persona.casoDesaparecido = :cdes')
            ->setParameter('cdes', 1)
            ->orderBy('persona.casoDesaparecidoTotal', 'DESC')
            ->addOrderBy('persona.nombres', 'ASC');

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
        } elseif (182 !== $params['centroPoblado']) {
            $queryBuilder->andwhere('centroPoblado.id = :idcentro')
                ->setParameter('idcentro', $params['centroPoblado']);
        }

        Paginator::queryTexts($queryBuilder, $params, ['persona.nombres', 'persona.edad', 'persona.sexo']);

        return $queryBuilder;
    }
}
