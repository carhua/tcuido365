<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Institucion;
use App\Form\InstitucionType;
use App\Manager\InstitucionManager;
use App\Repository\InstitucionRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/institucion')]
class InstitucionController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'institucion_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'institucion_index_paginated')]
    public function index(Request $request, int $page, InstitucionManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'institucion_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'institucion/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'institucion_export')]
    public function export(InstitucionRepository $institucionRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'institucion_index');
        try {
            $data = $institucionRepository->findAll();
            $fileNameTemp = self::institucionesExp($data);

            return $this->file($fileNameTemp, 'Instituciones.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'institucion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InstitucionManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'institucion_index');

        $tipo = new Institucion();
        $form = $this->createForm(InstitucionType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('institucion_index');
        }

        return $this->render(
            'institucion/new.html.twig',
            [
                'institucion' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'institucion_show', methods: ['GET'])]
    public function show(Institucion $institucion): Response
    {
        $this->denyAccess(Security::VIEW, 'institucion_index');

        return $this->render('institucion/show.html.twig', ['institucion' => $institucion]);
    }

    #[Route(path: '/{id}/edit', name: 'institucion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Institucion $institucion, InstitucionManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'institucion_index');

        $form = $this->createForm(InstitucionType::class, $institucion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($institucion)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('institucion_index', ['id' => $institucion->getId()]);
        }

        return $this->render(
            'institucion/edit.html.twig',
            [
                'institucion' => $institucion,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'institucion_delete', methods: ['POST'])]
    public function delete(Request $request, Institucion $institucion, InstitucionManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'institucion_index');

        if ($this->isCsrfTokenValid('delete'.$institucion->getId(), $request->request->get('_token'))) {
            $institucion->changeActive();
            if ($manager->save($institucion)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('institucion_index');
    }

    #[Route(path: '/{id}/delete', name: 'institucion_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Institucion $institucion,
        InstitucionManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'institucion_index', $institucion);

        if ($this->isCsrfTokenValid('delete_forever'.$institucion->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($institucion)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('institucion_index');
    }
}
