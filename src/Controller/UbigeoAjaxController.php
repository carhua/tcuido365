<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Security\Security;
use App\Service\UbigeoFilterService;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/ubigeo-ajax')]
class UbigeoAjaxController extends BaseController
{
    use TraitUser;

    private UbigeoFilterService $ubigeoFilter;

    public function __construct(Security $security, UbigeoFilterService $ubigeoFilter)
    {
        parent::__construct($security);
        $this->ubigeoFilter = $ubigeoFilter;
    }

    #[Route(path: '/distritos/{provinciaId}', name: 'ubigeo_distritos_ajax')]
    public function getDistritosByProvincia(int $provinciaId, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $this->ubigeoFilter->setUsuario($user);

        $distritosDisponibles = $this->ubigeoFilter->getDistritosDisponibles();
        
        $results = [];
        foreach ($distritosDisponibles as $distrito) {
            if ($distrito->getProvincia()->getId() == $provinciaId) {
                $results[] = [
                    'id' => $distrito->getId(),
                    'name' => $distrito->getNombre()
                ];
            }
        }

        return new JsonResponse($results);
    }

    #[Route(path: '/centros/{distritoId}', name: 'ubigeo_centros_ajax')]
    public function getCentrosByDistrito(int $distritoId, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $this->ubigeoFilter->setUsuario($user);

        $centrosDisponibles = $this->ubigeoFilter->getCentrosPobladosDisponibles();
        
        $results = [];
        foreach ($centrosDisponibles as $centro) {
            if ($centro->getDistrito()->getId() == $distritoId) {
                $results[] = [
                    'id' => $centro->getId(),
                    'name' => $centro->getNombre()
                ];
            }
        }

        return new JsonResponse($results);
    }
}
