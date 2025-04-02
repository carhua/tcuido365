<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\TipoDocumento;
use App\Form\TipoDocumentoType;
use App\Manager\TipoDocumentoManager;
use App\Repository\TipoDocumentoRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/tipo/documento')]
class TipoDocumentoController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'tipo_documento_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'tipo_documento_index_paginated')]
    public function index(Request $request, int $page, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'tipo_documento_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'tipo_documento/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'tipo_documento_export')]
    public function export(TipoDocumentoRepository $tipoDocumentoRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'tipo_documento_index');
        try {
            $data = $tipoDocumentoRepository->findAll();
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE TIPOS DE DOCUMENTO', 'TipoDocumento', 'TipoDocumento.xlsx');

            return $this->file($fileNameTemp, 'TipoDocumento.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'tipo_documento_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'tipo_documento_index');

        $tipo = new TipoDocumento();
        $form = $this->createForm(TipoDocumentoType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_documento_index');
        }

        return $this->render(
            'tipo_documento/new.html.twig',
            [
                'TipoDocumento' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_documento_show', methods: ['GET'])]
    public function show(TipoDocumento $tipoDocumento): Response
    {
        $this->denyAccess(Security::VIEW, 'tipo_documento_index');

        return $this->render('tipo_documento/show.html.twig', ['tipo_documento' => $tipoDocumento]);
    }

    #[Route(path: '/{id}/edit', name: 'tipo_documento_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoDocumento $tipoDocumento, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'tipo_documento_index');

        $form = $this->createForm(TipoDocumentoType::class, $tipoDocumento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipoDocumento)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_documento_index', ['id' => $tipoDocumento->getId()]);
        }

        return $this->render(
            'tipo_documento/edit.html.twig',
            [
                'tipo_documento' => $tipoDocumento,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_documento_delete', methods: ['POST'])]
    public function delete(Request $request, TipoDocumento $tipoDocumento, TipoDocumentoManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'tipo_documento_index');

        if ($this->isCsrfTokenValid('delete'.$tipoDocumento->getId(), $request->request->get('_token'))) {
            $tipoDocumento->changeActive();
            if ($manager->save($tipoDocumento)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_documento_index');
    }

    #[Route(path: '/{id}/delete', name: 'tipo_documento_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        TipoDocumento $tipoDocumento,
        TipoDocumentoManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'tipo_documento_index', $tipoDocumento);

        if ($this->isCsrfTokenValid('delete_forever'.$tipoDocumento->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($tipoDocumento)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_documento_index');
    }
}
