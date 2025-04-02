<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Provincia;
use App\Form\ProvinciaType;
use App\Manager\ProvinciaManager;
use App\Repository\ProvinciaRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/provincia')]
class ProvinciaController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'provincia_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'provincia_index_paginated')]
    public function index(Request $request, int $page, ProvinciaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'provincia_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'provincia/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'provincia_export')]
    public function export(ProvinciaRepository $provinciaRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'provincia_index');
        try {
            $data = $provinciaRepository->findAll();
            $fileNameTemp = self::provinciasExp($data);

            return $this->file($fileNameTemp, 'Provincias.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'provincia_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProvinciaManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'provincia_index');

        $tipo = new Provincia();
        $form = $this->createForm(ProvinciaType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('provincia_index');
        }

        return $this->render(
            'provincia/new.html.twig',
            [
                'provincia' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'provincia_show', methods: ['GET'])]
    public function show(Provincia $provincia): Response
    {
        $this->denyAccess(Security::VIEW, 'provincia_index');

        return $this->render('provincia/show.html.twig', ['provincia' => $provincia]);
    }

    #[Route(path: '/{id}/edit', name: 'provincia_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Provincia $provincia, ProvinciaManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'provincia_index');

        $form = $this->createForm(ProvinciaType::class, $provincia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($provincia)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('provincia_index', ['id' => $provincia->getId()]);
        }

        return $this->render(
            'provincia/edit.html.twig',
            [
                'provincia' => $provincia,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'provincia_delete', methods: ['POST'])]
    public function delete(Request $request, Provincia $provincia, ProvinciaManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'provincia_index');

        if ($this->isCsrfTokenValid('delete'.$provincia->getId(), $request->request->get('_token'))) {
            $provincia->changeActive();
            if ($manager->save($provincia)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('provincia_index');
    }

    #[Route(path: '/{id}/delete', name: 'provincia_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Provincia $provincia,
        ProvinciaManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'provincia_index', $provincia);

        if ($this->isCsrfTokenValid('delete_forever'.$provincia->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($provincia)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('provincia_index');
    }
}
