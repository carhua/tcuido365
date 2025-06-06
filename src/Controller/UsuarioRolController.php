<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\UsuarioRol;
use App\Form\UsuarioRolType;
use App\Manager\UsuarioRolManager;
use App\Repository\UsuarioRolRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/usuario_rol')]
class UsuarioRolController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'usuario_rol_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'usuario_rol_index_paginated')]
    public function index(Request $request, int $page, UsuarioRolManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'usuario_rol_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'usuario_rol/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'usuario_rol_export')]
    public function export(Request $request, UsuarioRolRepository $usuarioRolRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'usuario_rol_index');
        try {
            $b = $request->query->get('b');
            $ac = $request->query->get('ac');

            $filters = [];
            if ($ac !== null && $ac !== '') {
                $filters['isActive'] = (bool) $ac;
            }
            if ($b !== null && $b !== '') {
                $filters['nombre'] = $b;
            }
            
            if (empty($filters) || (isset($filters['nombre']) && $filters['nombre'] === '')) {
                $data = $usuarioRolRepository->findAll();
            } else {
                $qb = $usuarioRolRepository->createQueryBuilder('e');
                if (isset($filters['isActive'])) {
                    $qb->andWhere('e.isActive = :isActive')
                    ->setParameter('isActive', $filters['isActive']);
                }
                if (isset($filters['nombre']) && $filters['nombre'] !== '') {
                    $qb->andWhere('e.nombre LIKE :nombre')
                    ->setParameter('nombre', '%' . $filters['nombre'] . '%');
                }
                $data = $qb->getQuery()->getResult();
            }
            
            $fileNameTemp = self::usuarioRolExport($data);

            return $this->file($fileNameTemp, 'UsuarioRol.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'usuario_rol_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UsuarioRolManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'usuario_rol_index');

        $rol = new UsuarioRol();
        $form = $this->createForm(UsuarioRolType::class, $rol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rol->setOwner($this->getUser());
            if ($manager->save($rol)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('usuario_rol_index');
        }

        return $this->render(
            'usuario_rol/new.html.twig',
            [
                'usuario_rol' => $rol,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'usuario_rol_show', methods: ['GET'])]
    public function show(UsuarioRol $rol): Response
    {
        $this->denyAccess(Security::VIEW, 'usuario_rol_index');

        return $this->render('usuario_rol/show.html.twig', ['UsuarioRol' => $rol]);
    }

    #[Route(path: '/{id}/edit', name: 'usuario_rol_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsuarioRol $rol, UsuarioRolManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'usuario_rol_index');

        $form = $this->createForm(UsuarioRolType::class, $rol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($rol)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('usuario_rol_index', ['id' => $rol->getId()]);
        }

        return $this->render(
            'usuario_rol/edit.html.twig',
            [
                'usuario_rol' => $rol,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'usuario_rol_delete', methods: ['POST'])]
    public function delete(Request $request, UsuarioRol $rol, UsuarioRolManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'usuario_rol_index');

        if ($this->isCsrfTokenValid('delete'.$rol->getId(), $request->request->get('_token'))) {
            $rol->changeActive();
            if ($manager->save($rol)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_rol_index');
    }

    #[Route(path: '/{id}/delete', name: 'usuario_rol_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        UsuarioRol $rol,
        UsuarioRolManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'usuario_rol_index', $rol);

        if ($this->isCsrfTokenValid('delete_forever'.$rol->getId(), $request->request->get('_token'))) {
            if ($manager->remove($rol)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_rol_index');
    }
}
