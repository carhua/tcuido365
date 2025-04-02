<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\SituacionEncontrada;
use App\Form\SituacionEncontradaType;
use App\Manager\SituacionEncontradaManager;
use App\Repository\SituacionEncontradaRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/situacion/encontrada')]
class SituacionEncontradaController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'situacion_encontrada_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'situacion_encontrada_index_paginated')]
    public function index(Request $request, int $page, SituacionEncontradaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'situacion_encontrada_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'situacion_encontrada/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'situacion_encontrada_export')]
    public function export(SituacionEncontradaRepository $situacionEncontradaRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'situacion_encontrada_index');
        try {
            $data = $situacionEncontradaRepository->findAll();
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE SITUACIONES ENCONTRADAS', 'SituacionEncontrada', 'SituacionEncontrada.xlsx');

            return $this->file($fileNameTemp, 'SituacionEncontrada.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'situacion_encontrada_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SituacionEncontradaManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'situacion_encontrada_index');

        $tipo = new SituacionEncontrada();
        $form = $this->createForm(SituacionEncontradaType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('situacion_encontrada_index');
        }

        return $this->render(
            'situacion_encontrada/new.html.twig',
            [
                'SituacionEncontrada' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'situacion_encontrada_show', methods: ['GET'])]
    public function show(SituacionEncontrada $situacionEncontrada): Response
    {
        $this->denyAccess(Security::VIEW, 'situacion_encontrada_index');

        return $this->render('situacion_encontrada/show.html.twig',
            ['situacion_encontrada' => $situacionEncontrada]);
    }

    #[Route(path: '/{id}/edit', name: 'situacion_encontrada_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SituacionEncontrada $situacionEncontrada, SituacionEncontradaManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'usuario_index');

        $form = $this->createForm(SituacionEncontradaType::class, $situacionEncontrada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($situacionEncontrada)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('situacion_encontrada_index', ['id' => $situacionEncontrada->getId()]);
        }

        return $this->render(
            'situacion_encontrada/edit.html.twig',
            [
                'situacion_encontrada' => $situacionEncontrada,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'situacion_encontrada_delete', methods: ['POST'])]
    public function delete(Request $request, SituacionEncontrada $situacionEncontrada, SituacionEncontradaManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'situacion_encontrada_index');

        if ($this->isCsrfTokenValid('delete'.$situacionEncontrada->getId(),
            $request->request->get('_token'))) {
            $situacionEncontrada->changeActive();
            if ($manager->save($situacionEncontrada)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('situacion_encontrada_index');
    }

    #[Route(path: '/{id}/delete', name: 'situacion_encontrada_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        SituacionEncontrada $situacionEncontrada,
        SituacionEncontradaManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'situacion_encontrada_index', $situacionEncontrada);

        if ($this->isCsrfTokenValid('delete_forever'.$situacionEncontrada->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($situacionEncontrada)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('situacion_encontrada_index');
    }
}
