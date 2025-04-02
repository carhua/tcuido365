<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Cache\MenuCache;
use App\Entity\Menu;
use App\Form\MenuType;
use App\Manager\MenuManager;
use App\Security\Security;
use App\Utils\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/menu')]
class MenuController extends BaseController
{
    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'menu_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'menu_index_paginated')]
    public function index(Request $request, int $page, MenuManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'menu_index');

        $paginator = $manager->list($request->query->all(), $page);
        //        $paginator = $manager->listPadres($request->query->all(), $page);

        return $this->render(
            'menu/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'menu_export')]
    public function export(Request $request, MenuManager $manager): Response
    {
        $this->denyAccess(Security::EXPORT, 'menu_index');

        $headers = [
            'padre' => 'Padre',
            'nombre' => 'Nombre',
            'ruta' => 'Ruta',
            'icono' => 'Icono',
            'orden' => 'Orden',
            'activo' => 'Activo',
        ];

        $params = Paginator::params($request->query->all());
        $objetos = $manager->repository()->filter($params, false);
        $data = [];

        /** @var Menu $objeto */
        foreach ($objetos as $objeto) {
            $item = [];
            $item['padre'] = null !== $objeto->getPadre() ? $objeto->getPadre()->getNombre() : '';
            $item['nombre'] = $objeto->getNombre();
            $item['ruta'] = $objeto->getRuta();
            $item['icono'] = $objeto->getIcono();
            $item['orden'] = $objeto->getOrden();
            $item['activo'] = $objeto->isActive();
            $data[] = $item;
            unset($item);
        }

        return $manager->export($data, $headers, 'Reporte', 'menu');
    }

    #[Route(path: '/new', name: 'menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess(Security::NEW, 'menu_index');

        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setOwner($this->getUser());
            if ($manager->save($menu)) {
                $cache->update();
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('menu_index');
        }

        return $this->render(
            'menu/new.html.twig',
            [
                'menu' => $menu,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        $this->denyAccess(Security::VIEW, 'menu_index');

        return $this->render('menu/show.html.twig', ['menu' => $menu]);
    }

    #[Route(path: '/{id}/edit', name: 'menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess(Security::EDIT, 'menu_index');

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($menu)) {
                $cache->update();
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('menu_index', ['id' => $menu->getId()]);
        }

        return $this->render(
            'menu/edit.html.twig',
            [
                'menu' => $menu,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess(Security::DELETE, 'menu_index');

        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $menu->changeActive();
            if ($manager->save($menu)) {
                $cache->update();
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index');
    }

    #[Route(path: '/{id}/delete', name: 'menu_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Menu $menu,
        MenuManager $manager,
        MenuCache $cache
    ): Response {
        $this->denyAccess(Security::MASTER, 'menu_index', $menu);

        if ($this->isCsrfTokenValid('delete_forever'.$menu->getId(), $request->request->get('_token'))) {
            if ($manager->remove($menu)) {
                $cache->update();
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index');
    }
}
