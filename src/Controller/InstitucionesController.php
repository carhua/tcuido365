<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Instituciones;
use App\Form\Import\ImportFileDto;
use App\Form\Import\ImportFileType;
use App\Form\InstitucionesType;
use App\Manager\InstitucionesManager;
use App\Repository\InstitucionesRepository;
use App\Security\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/instituciones')]
class InstitucionesController extends BaseController
{
    #[Route(path: '/', name: 'instituciones_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'instituciones_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, InstitucionesManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'instituciones_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'instituciones/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/new', name: 'usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $passwordEncoder, InstitucionesManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'instituciones_index');

        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->setPassword($passwordEncoder->hashPassword($usuario, $usuario->getPassword()));
            $usuario->setOwner($this->getUser());
            if ($manager->save($usuario)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('instituciones_index');
        }

        return $this->render(
            'instituciones/new.html.twig',
            [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/profile', name: 'usuario_profile', methods: 'GET|POST')]
    public function profile(Request $request, UserPasswordHasherInterface $passwordEncoder, InstitucionesManager $manager): Response
    {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();
        $passwordOriginal = $usuario->getPassword();
        $form = $this->createForm(ChanguePasswordType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $usuario->getPassword() && '' !== $usuario->getPassword()) {
                $usuario->setPassword($passwordEncoder->hashPassword($usuario, $usuario->getPassword()));
            } else {
                $usuario->setPassword($passwordOriginal);
            }
            if ($manager->saveUser($usuario)) {
                $this->addFlash('success', 'Contraseña Actualizada correctamente!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('usuario_profile');
        }

        return $this->render('instituciones/profile.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'usuario_show', methods: ['GET'])]
    public function show(Usuario $usuario): Response
    {
        $this->denyAccess(Security::VIEW, 'instituciones_index');

        return $this->render('instituciones/show.html.twig', ['usuario' => $usuario]);
    }

    #[Route(path: '/{id}/edit', name: 'usuario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Usuario $usuario, UserPasswordHasherInterface $passwordEncoder, InstitucionesManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'instituciones_index');
        $passwordOriginal = $usuario->getPassword();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $usuario->getPassword() && '' !== $usuario->getPassword()) {
                $usuario->setPassword($passwordEncoder->hashPassword($usuario, $usuario->getPassword()));
            } else {
                $usuario->setPassword($passwordOriginal);
            }
            if ($manager->save($usuario)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('instituciones_index', ['id' => $usuario->getId()]);
        }

        return $this->render(
            'instituciones/edit.html.twig',
            [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'usuario_delete', methods: ['POST'])]
    public function delete(Request $request, Usuario $usuario, InstitucionesManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'instituciones_index');

        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $usuario->changeActive();
            if ($manager->save($usuario)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('instituciones_index');
    }

    #[Route(path: '/{id}/delete', name: 'usuario_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Usuario $usuario,
        InstitucionesManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'instituciones_index', $usuario);

        if ($this->isCsrfTokenValid('delete_forever'.$usuario->getId(), $request->request->get('_token'))) {
            if ($manager->remove($usuario)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('instituciones_index');
    }
}
