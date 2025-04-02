<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\TipoExplotacion;
use App\Form\TipoExplotacionType;
use App\Manager\TipoExplotacionManager;
use App\Repository\TipoExplotacionRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/tipo/explotacion')]
class TipoExplotacionController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'tipo_explotacion_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'tipo_explotacion_index_paginated')]
    public function index(Request $request, int $page, TipoExplotacionManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'tipo_explotacion_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'tipo_explotacion/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'tipo_explotacion_export')]
    public function export(TipoExplotacionRepository $tipoExplotacionRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'tipo_explotacion_index');
        try {
            $data = $tipoExplotacionRepository->findAll();
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE TIPOS DE EXPLOTACIONES', 'TipoExplotacion', 'TipoExplotacion.xlsx');

            return $this->file($fileNameTemp, 'TipoExplotacion.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'tipo_explotacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TipoExplotacionManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'tipo_explotacion_index');

        $tipo = new TipoExplotacion();
        $form = $this->createForm(TipoExplotacionType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_explotacion_index');
        }

        return $this->render(
            'tipo_explotacion/new.html.twig',
            [
                'TipoExplotacion' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_explotacion_show', methods: ['GET'])]
    public function show(TipoExplotacion $tipoExplotacion): Response
    {
        $this->denyAccess(Security::VIEW, 'tipo_explotacion_index');

        return $this->render('tipo_explotacion/show.html.twig', ['tipo_explotacion' => $tipoExplotacion]);
    }

    #[Route(path: '/{id}/edit', name: 'tipo_explotacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoExplotacion $tipoExplotacion, TipoExplotacionManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'tipo_explotacion_index');

        $form = $this->createForm(TipoExplotacionType::class, $tipoExplotacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipoExplotacion)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_explotacion_index', ['id' => $tipoExplotacion->getId()]);
        }

        return $this->render(
            'tipo_explotacion/edit.html.twig',
            [
                'tipo_explotacion' => $tipoExplotacion,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_explotacion_delete', methods: ['POST'])]
    public function delete(Request $request, TipoExplotacion $tipoExplotacion, TipoExplotacionManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'tipo_explotacion_index');

        if ($this->isCsrfTokenValid('delete'.$tipoExplotacion->getId(), $request->request->get('_token'))) {
            $tipoExplotacion->changeActive();
            if ($manager->save($tipoExplotacion)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_explotacion_index');
    }

    #[Route(path: '/{id}/delete', name: 'tipo_explotacion_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        TipoExplotacion $tipoExplotacion,
        TipoExplotacionManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'tipo_explotacion_index', $tipoExplotacion);

        if ($this->isCsrfTokenValid('delete_forever'.$tipoExplotacion->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($tipoExplotacion)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_explotacion_index');
    }
}
