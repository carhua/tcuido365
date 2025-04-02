<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Caserio;
use App\Form\CaserioType;
use App\Manager\CaserioManager;
use App\Security\Security;
use App\Utils\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/caserio')]
class CaserioController extends BaseController
{
    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'caserio_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'caserio_index_paginated')]
    public function index(Request $request, int $page, CaserioManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'caserio_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'caserio/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'caserio_export')]
    public function export(Request $request, CaserioManager $manager): Response
    {
        $this->denyAccess(Security::EXPORT, 'caserio_index');

        $headers = [
            'nombre' => 'Nombre',
            'activo' => 'Activo',
        ];

        $params = Paginator::params($request->query->all());
        $objetos = $manager->repository()->filter($params, false);
        $data = [];

        /** @var Caserio $objeto */
        foreach ($objetos as $objeto) {
            $item = [];
            $item['nombre'] = $objeto->getNombre();
            $item['activo'] = $objeto->isActive();
            $data[] = $item;
            unset($item);
        }

        return $manager->export($data, $headers, 'Reporte', 'Caserio');
    }

    #[Route(path: '/new', name: 'caserio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CaserioManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'caserio_index');

        $tipo = new Caserio();
        $form = $this->createForm(CaserioType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('caserio_index');
        }

        return $this->render(
            'caserio/new.html.twig',
            [
                'Caserio' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'caserio_show', methods: ['GET'])]
    public function show(Caserio $caserio): Response
    {
        $this->denyAccess(Security::VIEW, 'caserio_index');

        return $this->render('caserio/show.html.twig', ['caserio' => $caserio]);
    }

    #[Route(path: '/{id}/edit', name: 'caserio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Caserio $caserio, CaserioManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'usuario_index');

        $form = $this->createForm(CaserioType::class, $caserio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($caserio)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('caserio_index', ['id' => $caserio->getId()]);
        }

        return $this->render(
            'caserio/edit.html.twig',
            [
                'caserio' => $caserio,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'caserio_delete', methods: ['POST'])]
    public function delete(Request $request, Caserio $caserio, CaserioManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'caserio_index');

        if ($this->isCsrfTokenValid('delete'.$caserio->getId(), $request->request->get('_token'))) {
            $caserio->changeActive();
            if ($manager->save($caserio)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('caserio_index');
    }

    #[Route(path: '/{id}/delete', name: 'caserio_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Caserio $caserio,
        CaserioManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'caserio_index', $caserio);

        if ($this->isCsrfTokenValid('delete_forever'.$caserio->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($caserio)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('caserio_index');
    }
}
