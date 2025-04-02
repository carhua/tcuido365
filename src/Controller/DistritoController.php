<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Distrito;
use App\Entity\Usuario;
use App\Form\DistritoType;
use App\Manager\DistritoManager;
use App\Repository\DistritoRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/distrito')]
class DistritoController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'distrito_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'distrito_index_paginated')]
    public function index(Request $request, int $page, DistritoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'distrito_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'distrito/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'distrito_export')]
    public function export(DistritoRepository $distritoRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'distrito_index');
        try {
            $data = $distritoRepository->findAll();
            $fileNameTemp = self::distritosExp($data);

            return $this->file($fileNameTemp, 'Distritos.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'distrito_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DistritoManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'distrito_index');

        $tipo = new Distrito();
        $form = $this->createForm(DistritoType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('distrito_index');
        }

        return $this->render(
            'distrito/new.html.twig',
            [
                'distrito' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'distrito_show', methods: ['GET'])]
    public function show(Distrito $distrito): Response
    {
        $this->denyAccess(Security::VIEW, 'distrito_index');

        return $this->render('distrito/show.html.twig', ['distrito' => $distrito]);
    }

    #[Route(path: '/{id}/edit', name: 'distrito_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Distrito $distrito, DistritoManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'distrito_index');

        $form = $this->createForm(DistritoType::class, $distrito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($distrito)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('distrito_index', ['id' => $distrito->getId()]);
        }

        return $this->render(
            'distrito/edit.html.twig',
            [
                'distrito' => $distrito,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'distrito_delete', methods: ['POST'])]
    public function delete(Request $request, Distrito $distrito, DistritoManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'distrito_index');

        if ($this->isCsrfTokenValid('delete'.$distrito->getId(), $request->request->get('_token'))) {
            $distrito->changeActive();
            if ($manager->save($distrito)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('distrito_index');
    }

    #[Route(path: '/{id}/delete', name: 'distrito_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Distrito $distrito,
        DistritoManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'distrito_index', $distrito);

        if ($this->isCsrfTokenValid('delete_forever'.$distrito->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($distrito)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('distrito_index');
    }

    #[Route(path: '/ajax/distritos', name: 'distrito_ajax', methods: ['GET', 'POST'])]
    public function findDistritos(Request $request, EntityManagerInterface $em): Response
    {
        $provincia_id = $request->get('provincia_id');

        /** @var Usuario $user */
        $user = $this->getUser();
        $distritoUser = $user->getDistrito();
        if ('TODOS' !== $distritoUser->getNombre()) {
            $distritos = $em->getRepository(Distrito::class)->findBy(['id' => $distritoUser->getId()]);
        } else {
            $distritos = $em->getRepository(Distrito::class)->findByProvincia($provincia_id);
        }

        $responseArray = [];
        foreach ($distritos as $distrito) {
            $responseArray[] = [
                'id' => $distrito->getId(),
                'name' => $distrito->getNombre(),
            ];
        }

        return new JsonResponse($responseArray);
    }
}
