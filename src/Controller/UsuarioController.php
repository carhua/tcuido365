<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\ChanguePasswordType;
use App\Form\Import\ImportFileDto;
use App\Form\Import\ImportFileType;
use App\Form\UsuarioType;
use App\Manager\Usuario\ImportDataUsuarioManager;
use App\Manager\UsuarioManager;
use App\Repository\UsuarioRepository;
use App\Security\Security;
use App\Service\Import\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/usuario')]
class UsuarioController extends BaseController
{
    use TraitExcelFechas;

    public function __construct(
        private readonly EntityManagerInterface $em,
        Security $security
    ) {
        parent::__construct($security);
    }

    #[Route(path: '/', name: 'usuario_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'usuario_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, UsuarioManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'usuario_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'usuario/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'usuario_export', methods: ['GET'])]
    public function export(Request $request, UsuarioRepository $usuarioRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'usuario_index');
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
                $data = $usuarioRepository->findAll();
            } else {
                $qb = $usuarioRepository->createQueryBuilder('e');
                if (isset($filters['isActive'])) {
                    $qb->andWhere('e.isActive = :isActive')
                    ->setParameter('isActive', $filters['isActive']);
                }
                if (isset($filters['nombre']) && $filters['nombre'] !== '') {
                    $qb->andWhere('e.fullName LIKE :nombre')
                    ->setParameter('nombre', '%' . $filters['nombre'] . '%');
                }
                $data = $qb->getQuery()->getResult();

            }
            
            $fileNameTemp = self::usuarioExport($data);

            return $this->file($fileNameTemp, 'Usuarios.xlsx');
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/import', name: 'import', methods: ['GET', 'POST'])]
    public function importUsario(Request $request, FileUploader $fileUploader, ImportDataUsuarioManager $importDataUsuarioManager): Response
    {
        $file = new ImportFileDto();
        $form = $this->createForm(ImportFileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileDto = $fileUploader->up($file->file(), '/usuario');
            $importDataUsuarioManager->saveFile($fileDto);
            $this->addFlash('success', 'Registros importados');

            return $this->redirectToRoute('usuario_import_process');
        }

        return $this->render('usuario/import.html.twig', [
            'formImport' => $form->createView(),
        ]);
    }

    #[Route(path: '/import/process', name: 'usuario_import_process', methods: ['GET', 'POST'])]
    public function importProcess(Request $request, ImportDataUsuarioManager $importDataUsuarioManager): Response
    {
        $onlyErrors = (bool) $request->request->get('errors');
        $options = $request->request->all('options');
        $results = [];
        if (!empty($options)) {
            $guardar = (bool) $request->request->get('guardar');
            $results = $importDataUsuarioManager->execute($options, $guardar);

            $this->addFlash('success', 'Se importado con exito los datos');
            if ($guardar) {
                return $this->redirectToRoute('usuario_index');
            }
        }

        return $this->render('usuario/import_process.html.twig', [
            'data' => $importDataUsuarioManager->dataImportada(),
            'headerOptions' => $importDataUsuarioManager->headerOptions(),
            'selectedOptions' => $options,
            'result' => $results,
            'onlyErrors' => $onlyErrors,
            ]);
    }

    #[Route(path: '/new', name: 'usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $passwordEncoder, UsuarioManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'usuario_index');

        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (null !== $plainPassword && '' !== $plainPassword) {
                $usuario->setPassword($passwordEncoder->hashPassword($usuario, $plainPassword));
            }

            $this->handleOperadorProteccion($usuario);
            $usuario->setOwner($this->getUser());
            if ($manager->save($usuario)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('usuario_index');
        }

        return $this->render(
            'usuario/new.html.twig',
            [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/profile', name: 'usuario_profile', methods: 'GET|POST')]
    public function profile(Request $request, UserPasswordHasherInterface $passwordEncoder, UsuarioManager $manager): Response
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

        return $this->render('usuario/profile.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'usuario_show', methods: ['GET'])]
    public function show(Usuario $usuario): Response
    {
        $this->denyAccess(Security::VIEW, 'usuario_index');

        return $this->render('usuario/show.html.twig', ['usuario' => $usuario]);
    }

    #[Route(path: '/{id}/edit', name: 'usuario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Usuario $usuario, UserPasswordHasherInterface $passwordEncoder, UsuarioManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'usuario_index');
        $passwordOriginal = $usuario->getPassword();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (null !== $plainPassword && '' !== $plainPassword) {
                $usuario->setPassword($passwordEncoder->hashPassword($usuario, $plainPassword));
            } else {
                $usuario->setPassword($passwordOriginal);
            }

            $this->handleOperadorProteccion($usuario);
            if ($manager->save($usuario)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('usuario_index', ['id' => $usuario->getId()]);
        }

        return $this->render(
            'usuario/edit.html.twig',
            [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]
        );
    }

    private function handleOperadorProteccion(Usuario $usuario): void
    {
        $esOperador = false;
        foreach ($usuario->getUsuarioRoles() as $rol) {
            if ('ROLE_OPERADORPROTECCION' === $rol->getRol()) {
                $esOperador = true;
                break;
            }
        }

        if ($esOperador) {
            $centroPobladoTodos = $this->em->getRepository(\App\Entity\CentroPoblado::class)->findOneBy(['nombre' => 'TODOS']);
            if ($centroPobladoTodos) {
                $usuario->setCentroPoblado($centroPobladoTodos);
            }
        }
    }
    #[Route(path: '/{id}', name: 'usuario_delete', methods: ['POST'])]
    public function delete(Request $request, Usuario $usuario, UsuarioManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'usuario_index');

        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $usuario->changeActive();
            if ($manager->save($usuario)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_index');
    }

    #[Route(path: '/{id}/delete', name: 'usuario_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Usuario $usuario,
        UsuarioManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'usuario_index', $usuario);

        if ($this->isCsrfTokenValid('delete_forever'.$usuario->getId(), $request->request->get('_token'))) {
            if ($manager->remove($usuario)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('usuario_index');
    }
}
