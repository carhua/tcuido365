<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CasoViolencia;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\TipoMaltrato;
use App\Security\Security;
use App\Service\UbigeoFilterService;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/busqueda-violencia')]
class CasoViolenciaSearchController extends BaseController
{
    use TraitUser;

    private UbigeoFilterService $ubigeoFilter;

    public function __construct(Security $security, UbigeoFilterService $ubigeoFilter)
    {
        parent::__construct($security);
        $this->ubigeoFilter = $ubigeoFilter;
    }

    #[Route(path: '/', name: 'caso_violencia_search')]
    public function search(EntityManagerInterface $em): Response
    {
        $this->denyAccess(\App\Security\Security::LIST, 'caso_violencia_search');

        $user = $this->getUser();
        
        $this->ubigeoFilter->setUsuario($user);
        
        $provincias = $this->ubigeoFilter->getProvinciasDisponibles();
        $distritos = $this->ubigeoFilter->getDistritosDisponibles();
        $centrosPoblados = $this->ubigeoFilter->getCentrosPobladosDisponibles();
        $tiposMaltrato = $em->getRepository(TipoMaltrato::class)->findBy(['isActive' => true]);

        return $this->render('caso_violencia/search.html.twig', [
            'provincias' => $provincias,
            'distritos' => $distritos,
            'centrosPoblados' => $centrosPoblados,
            'tiposMaltrato' => $tiposMaltrato,
        ]);
    }

    #[Route(path: '/ajax', name: 'caso_violencia_search_ajax', methods: ['POST'])]
    public function searchAjax(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $this->ubigeoFilter->setUsuario($user);

        $fechaInicio = $request->request->get('fechaInicio');
        $fechaFinal = $request->request->get('fechaFinal');
        $provinciaId = $request->request->get('provincia');
        $distritoId = $request->request->get('distrito');
        $centroId = $request->request->get('centroPoblado');
        $tipoMaltratoId = $request->request->get('tipoMaltrato');
        $estado = $request->request->get('estado');

        $qb = $em->createQueryBuilder();
        $qb->select('c', 'cp', 'tm')
            ->from(CasoViolencia::class, 'c')
            ->leftJoin('c.centroPoblado', 'cp')
            ->leftJoin('c.tipoMaltrato', 'tm')
            ->orderBy('c.fechaReporte', 'DESC');

        if ($fechaInicio) {
            $qb->andWhere('c.fechaReporte >= :fechaInicio')
               ->setParameter('fechaInicio', new \DateTime($fechaInicio));
        }

        if ($fechaFinal) {
            $qb->andWhere('c.fechaReporte <= :fechaFinal')
               ->setParameter('fechaFinal', new \DateTime($fechaFinal));
        }

        if ($centroId) {
            $qb->andWhere('c.centroPoblado = :centro')
               ->setParameter('centro', $centroId);
        } elseif ($distritoId) {
            $qb->andWhere('cp.distrito = :distrito')
               ->setParameter('distrito', $distritoId);
        } elseif ($provinciaId) {
            $qb->innerJoin('cp.distrito', 'd')
               ->andWhere('d.provincia = :provincia')
               ->setParameter('provincia', $provinciaId);
        }

        if ($tipoMaltratoId) {
            $qb->andWhere('c.tipoMaltrato = :tipoMaltrato')
               ->setParameter('tipoMaltrato', $tipoMaltratoId);
        }

        if ($estado) {
            $qb->andWhere('c.estadoCaso = :estado')
               ->setParameter('estado', $estado);
        }

        $this->ubigeoFilter->aplicarFiltroQueryBuilder($qb, 'cp');

        $casos = $qb->getQuery()->getResult();

        $results = [];
        foreach ($casos as $caso) {
            $results[] = [
                'id' => $caso->getId(),
                'codigo' => $caso->getCodigoApp() ?: $caso->getCodigo(),
                'fechaReporte' => $caso->getFechaReporte()->format('d/m/Y'),
                'centroPoblado' => $caso->getCentroPoblado() ? $caso->getCentroPoblado()->getNombre() : '',
                'distrito' => $caso->getDistrito(),
                'tipoMaltrato' => $caso->getTipoMaltrato() ? $caso->getTipoMaltrato()->getNombre() : '',
                'descripcion' => $caso->getDescripcionReporte() ? substr($caso->getDescripcionReporte(), 0, 100) : '',
                'estadoCaso' => $caso->getEstadoCaso(),
            ];
        }

        return new JsonResponse([
            'data' => $results,
            'total' => count($results)
        ]);
    }
}
