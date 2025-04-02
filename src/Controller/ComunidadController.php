<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Comunidad;
use App\Form\ComunidadType;
use App\Manager\ComunidadManager;
use App\Security\Security;
use App\Utils\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/comunidad')]
class ComunidadController extends BaseController
{
    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'comunidad_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'comunidad_index_paginated')]
    public function index(Request $request, int $page, ComunidadManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'comunidad_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'comunidad/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'comunidad_export')]
    public function export(Request $request, ComunidadManager $manager): Response
    {
        $this->denyAccess(Security::EXPORT, 'comunidad_index');

        $headers = [
            'nombre' => 'Nombre',
            'activo' => 'Activo',
        ];

        $params = Paginator::params($request->query->all());
        $objetos = $manager->repository()->filter($params, false);
        $data = [];

        /** @var Comunidad $objeto */
        foreach ($objetos as $objeto) {
            $item = [];
            $item['nombre'] = $objeto->getNombre();
            $item['activo'] = $objeto->isActive();
            $data[] = $item;
            unset($item);
        }

        return $manager->export($data, $headers, 'Reporte', 'Comunidad');
    }

    #[Route(path: '/new', name: 'comunidad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ComunidadManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'comunidad_index');

        $tipo = new Comunidad();
        $form = $this->createForm(ComunidadType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('comunidad_index');
        }

        return $this->render(
            'comunidad/new.html.twig',
            [
                'Comunidad' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'comunidad_show', methods: ['GET'])]
    public function show(Comunidad $comunidad): Response
    {
        $this->denyAccess(Security::VIEW, 'comunidad_index');

        return $this->render('comunidad/show.html.twig', ['comunidad' => $comunidad]);
    }

    #[Route(path: '/{id}/edit', name: 'comunidad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comunidad $comunidad, ComunidadManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'usuario_index');

        $form = $this->createForm(ComunidadType::class, $comunidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($comunidad)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('comunidad_index', ['id' => $comunidad->getId()]);
        }

        return $this->render(
            'comunidad/edit.html.twig',
            [
                'comunidad' => $comunidad,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'comunidad_delete', methods: ['POST'])]
    public function delete(Request $request, Comunidad $comunidad, ComunidadManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'comunidad_index');

        if ($this->isCsrfTokenValid('delete'.$comunidad->getId(),
            $request->request->get('_token'))) {
            $comunidad->changeActive();
            if ($manager->save($comunidad)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('comunidad_index');
    }

    #[Route(path: '/{id}/delete', name: 'comunidad_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Comunidad $comunidad,
        ComunidadManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'comunidad_index', $comunidad);

        if ($this->isCsrfTokenValid('delete_forever'.$comunidad->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($comunidad)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('comunidad_index');
    }
}
