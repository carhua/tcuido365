<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Region;
use App\Form\RegionType;
use App\Manager\RegionManager;
use App\Repository\RegionRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/region')]
class RegionController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'region_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'region_index_paginated')]
    public function index(Request $request, int $page, RegionManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'region_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'region/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'region_export')]
    public function export(RegionRepository $regionRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'region_index');
        try {
            $data = $regionRepository->findAll();
            $fileNameTemp = self::regionesExp($data);

            return $this->file($fileNameTemp, 'Regiones.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'region_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RegionManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'region_index');

        $tipo = new Region();
        $form = $this->createForm(RegionType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('region_index');
        }

        return $this->render(
            'region/new.html.twig',
            [
                'region' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'region_show', methods: ['GET'])]
    public function show(Region $region): Response
    {
        $this->denyAccess(Security::VIEW, 'region_index');

        return $this->render('region/show.html.twig', ['region' => $region]);
    }

    #[Route(path: '/{id}/edit', name: 'region_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, region $region, regionManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'region_index');

        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($region)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('region_index', ['id' => $region->getId()]);
        }

        return $this->render(
            'region/edit.html.twig',
            [
                'region' => $region,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'region_delete', methods: ['POST'])]
    public function delete(Request $request, Region $region, RegionManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'region_index');

        if ($this->isCsrfTokenValid('delete'.$region->getId(), $request->request->get('_token'))) {
            $region->changeActive();
            if ($manager->save($region)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('region_index');
    }

    #[Route(path: '/{id}/delete', name: 'region_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Region $region,
        RegionManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'region_index', $region);

        if ($this->isCsrfTokenValid('delete_forever'.$region->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($region)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('region_index');
    }
}
