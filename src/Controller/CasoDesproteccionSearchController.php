<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CasoDesproteccion;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\SituacionEncontrada;
use App\Security\Security;
use App\Service\UbigeoFilterService;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/busqueda-desproteccion')]
class CasoDesproteccionSearchController extends BaseController
{
    use TraitUser;

    private UbigeoFilterService $ubigeoFilter;

    public function __construct(Security $security, UbigeoFilterService $ubigeoFilter)
    {
        parent::__construct($security);
        $this->ubigeoFilter = $ubigeoFilter;
    }

    #[Route(path: '/', name: 'caso_desproteccion_search')]
    public function search(EntityManagerInterface $em): Response
    {
        $this->denyAccess(\App\Security\Security::LIST, 'caso_desproteccion_search');

        $user = $this->getUser();
        
        // Configurar el servicio con el usuario actual
        $this->ubigeoFilter->setUsuario($user);
        
        // Obtener las opciones disponibles según el usuario
        $provincias = $this->ubigeoFilter->getProvinciasDisponibles();
        $distritos = $this->ubigeoFilter->getDistritosDisponibles();
        $centrosPoblados = $this->ubigeoFilter->getCentrosPobladosDisponibles();
        $situaciones = $em->getRepository(SituacionEncontrada::class)->findBy(['isActive' => true]);

        return $this->render('caso_desproteccion/search.html.twig', [
            'provincias' => $provincias,
            'distritos' => $distritos,
            'centrosPoblados' => $centrosPoblados,
            'situaciones' => $situaciones,
        ]);
    }

    #[Route(path: '/ajax', name: 'caso_desproteccion_search_ajax', methods: ['POST'])]
    public function searchAjax(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $this->ubigeoFilter->setUsuario($user);

        $fechaInicio = $request->request->get('fechaInicio');
        $fechaFinal = $request->request->get('fechaFinal');
        $provinciaId = $request->request->get('provincia');
        $distritoId = $request->request->get('distrito');
        $centroId = $request->request->get('centroPoblado');
        $situacionId = $request->request->get('situacionEncontrada');
        $estado = $request->request->get('estado');

        $qb = $em->createQueryBuilder();
        $qb->select('c', 'cp', 'se')
            ->from(CasoDesproteccion::class, 'c')
            ->leftJoin('c.centroPoblado', 'cp')
            ->leftJoin('c.situacionEncontrada', 'se')
            ->orderBy('c.fechaReporte', 'DESC');

        // Aplicar filtros
        if ($fechaInicio) {
            $qb->andWhere('c.fechaReporte >= :fechaInicio')
               ->setParameter('fechaInicio', new \DateTime($fechaInicio));
        }

        if ($fechaFinal) {
            $qb->andWhere('c.fechaReporte <= :fechaFinal')
               ->setParameter('fechaFinal', new \DateTime($fechaFinal . ' 23:59:59'));
        }

        if ($centroId && $centroId !== '') {
            $qb->andWhere('cp.id = :centroId')
               ->setParameter('centroId', $centroId);
        } elseif ($distritoId && $distritoId !== '') {
            $qb->andWhere('cp.distrito = :distritoId')
               ->setParameter('distritoId', $distritoId);
        } elseif ($provinciaId && $provinciaId !== '') {
            $qb->innerJoin('cp.distrito', 'd')
               ->innerJoin('d.provincia', 'p')
               ->andWhere('p.id = :provinciaId')
               ->setParameter('provinciaId', $provinciaId);
        } else {
            // Aplicar filtro automático por ubigeo del usuario
            $this->applyUbigeoFilter($qb, $user);
        }

        if ($situacionId && $situacionId !== '') {
            $qb->andWhere('se.id = :situacionId')
               ->setParameter('situacionId', $situacionId);
        }

        if ($estado && $estado !== '') {
            $qb->andWhere('c.estadoCaso = :estado')
               ->setParameter('estado', $estado);
        }

        $casos = $qb->getQuery()->getResult();

        $results = [];
        foreach ($casos as $caso) {
            $results[] = [
                'id' => $caso->getId(),
                'codigo' => $caso->getCodigo(),
                'fechaReporte' => $caso->getFechaReporte()->format('d/m/Y H:i'),
                'centroPoblado' => $caso->getCentroPoblado() ? $caso->getCentroPoblado()->getNombre() : 'N/A',
                'lugarCaso' => $caso->getLugarCaso(),
                'situacionEncontrada' => $caso->getSituacionEncontrada() ? $caso->getSituacionEncontrada()->getNombre() : 'N/A',
                'estadoCaso' => $caso->getEstadoCaso(),
                'descripcion' => substr($caso->getDescripcionReporte(), 0, 100) . '...',
            ];
        }

        return new JsonResponse([
            'success' => true,
            'data' => $results,
            'total' => count($results)
        ]);
    }

    private function applyUbigeoFilter($qb, $user): void
    {
        // Si es super admin, no aplicar filtros
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return;
        }

        $centroPoblado = $user->getCentroPoblado();
        $distrito = $user->getDistrito();
        $provincia = $user->getProvincia();

        if ($centroPoblado && $centroPoblado->getId() !== 182) {
            $qb->andWhere('cp.id = :centroPoblado')
               ->setParameter('centroPoblado', $centroPoblado->getId());
        } elseif ($distrito && $distrito->getNombre() !== 'TODOS') {
            $qb->andWhere('cp.distrito = :distrito')
               ->setParameter('distrito', $distrito->getId());
        } elseif ($provincia && $provincia->getNombre() !== 'TODOS') {
            $qb->innerJoin('cp.distrito', 'd')
               ->innerJoin('d.provincia', 'p')
               ->andWhere('p.id = :provincia')
               ->setParameter('provincia', $provincia->getId());
        }
    }
}
