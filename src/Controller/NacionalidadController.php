<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Nacionalidad;
use App\Form\NacionalidadType;
use App\Manager\NacionalidadManager;
use App\Repository\NacionalidadRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/nacionalidad')]
class NacionalidadController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'nacionalidad_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'nacionalidad_index_paginated')]
    public function index(Request $request, int $page, NacionalidadManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'nacionalidad_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'nacionalidad/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'nacionalidad_export')]
    public function export(NacionalidadRepository $nacionalidadRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'nacionalidad_index');
        try {
            $data = $nacionalidadRepository->findAll();
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE NACIONALIDADES', 'Nacionalidad', 'Nacionalidad.xlsx');

            return $this->file($fileNameTemp, 'Nacionalidad.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'nacionalidad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NacionalidadManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'nacionalidad_index');

        $tipo = new Nacionalidad();
        $form = $this->createForm(NacionalidadType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('nacionalidad_index');
        }

        return $this->render(
            'nacionalidad/new.html.twig',
            [
                'Nacionalidad' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'nacionalidad_show', methods: ['GET'])]
    public function show(Nacionalidad $nacionalidad): Response
    {
        $this->denyAccess(Security::VIEW, 'nacionalidad_index');

        return $this->render('nacionalidad/show.html.twig', ['nacionalidad' => $nacionalidad]);
    }

    #[Route(path: '/{id}/edit', name: 'nacionalidad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nacionalidad $nacionalidad, NacionalidadManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'nacionalidad_index');

        $form = $this->createForm(NacionalidadType::class, $nacionalidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($nacionalidad)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('nacionalidad_index', ['id' => $nacionalidad->getId()]);
        }

        return $this->render(
            'nacionalidad/edit.html.twig',
            [
                'nacionalidad' => $nacionalidad,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'nacionalidad_delete', methods: ['POST'])]
    public function delete(Request $request, Nacionalidad $nacionalidad, NacionalidadManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'nacionalidad_index');

        if ($this->isCsrfTokenValid('delete'.$nacionalidad->getId(), $request->request->get('_token'))) {
            $nacionalidad->changeActive();
            if ($manager->save($nacionalidad)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('nacionalidad_index');
    }

    #[Route(path: '/{id}/delete', name: 'nacionalidad_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Nacionalidad $nacionalidad,
        NacionalidadManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'nacionalidad_index', $nacionalidad);

        if ($this->isCsrfTokenValid('delete_forever'.$nacionalidad->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($nacionalidad)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('nacionalidad_index');
    }
}
