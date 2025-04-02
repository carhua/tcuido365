<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\EstadoCivil;
use App\Form\EstadoCivilType;
use App\Manager\EstadoCivilManager;
use App\Repository\EstadoCivilRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/estado/civil')]
class EstadoCivilController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'estado_civil_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'estado_civil_index_paginated')]
    public function index(Request $request, int $page, EstadoCivilManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'estado_civil_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'estado_civil/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'estado_civil_export')]
    public function export(EstadoCivilRepository $estadoCivilRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'estado_civil_index');
        try {
            $data = $estadoCivilRepository->findAll();
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE ESTADOS CIVILES', 'EstadoCivil', 'EstadoCivil.xlsx');

            return $this->file($fileNameTemp, 'EstadoCivil.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'estado_civil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EstadoCivilManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'estado_civil_index');

        $tipo = new EstadoCivil();
        $form = $this->createForm(EstadoCivilType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('estado_civil_index');
        }

        return $this->render(
            'estado_civil/new.html.twig',
            [
                'EstadoCivil' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'estado_civil_show', methods: ['GET'])]
    public function show(EstadoCivil $estadoCivil): Response
    {
        $this->denyAccess(Security::VIEW, 'estado_civil_index');

        return $this->render('estado_civil/show.html.twig', ['estado_civil' => $estadoCivil]);
    }

    #[Route(path: '/{id}/edit', name: 'estado_civil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EstadoCivil $estadoCivil, EstadoCivilManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'estado_civil_index');

        $form = $this->createForm(EstadoCivilType::class, $estadoCivil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($estadoCivil)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('estado_civil_index', ['id' => $estadoCivil->getId()]);
        }

        return $this->render(
            'estado_civil/edit.html.twig',
            [
                'estado_civil' => $estadoCivil,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'estado_civil_delete', methods: ['POST'])]
    public function delete(Request $request, EstadoCivil $estadoCivil, EstadoCivilManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'estado_civil_index');

        if ($this->isCsrfTokenValid('delete'.$estadoCivil->getId(), $request->request->get('_token'))) {
            $estadoCivil->changeActive();
            if ($manager->save($estadoCivil)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('estado_civil_index');
    }

    #[Route(path: '/{id}/delete', name: 'estado_civil_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        EstadoCivil $estadoCivil,
        EstadoCivilManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'estado_civil_index', $estadoCivil);

        if ($this->isCsrfTokenValid('delete_forever'.$estadoCivil->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($estadoCivil)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('estado_civil_index');
    }
}
