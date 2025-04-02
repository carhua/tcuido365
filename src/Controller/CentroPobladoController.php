<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CentroPoblado;
use App\Form\CentroPobladoType;
use App\Manager\CentroPobladoManager;
use App\Repository\CentroPobladoRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/centro/poblado')]
class CentroPobladoController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'centro_poblado_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'centro_poblado_index_paginated')]
    public function index(Request $request, int $page, CentroPobladoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'centro_poblado_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'centro_poblado/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'centro_poblado_export')]
    public function export(CentroPobladoRepository $centroPobladoRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'centro_poblado_index');
        try {
            $data = $centroPobladoRepository->findAll();
            $fileNameTemp = self::centroPobladoExp($data);

            return $this->file($fileNameTemp, 'CentroPoblado.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'centro_poblado_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CentroPobladoManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'centro_poblado_index');

        $tipo = new CentroPoblado();
        $form = $this->createForm(CentroPobladoType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('centro_poblado_index');
        }

        return $this->render(
            'centro_poblado/new.html.twig',
            [
                'CentroPoblado' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'centro_poblado_show', methods: ['GET'])]
    public function show(CentroPoblado $centroPoblado): Response
    {
        $this->denyAccess(Security::VIEW, 'centro_poblado_index');

        return $this->render('centro_poblado/show.html.twig', ['centro_poblado' => $centroPoblado]);
    }

    #[Route(path: '/{id}/edit', name: 'centro_poblado_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CentroPoblado $centroPoblado, CentroPobladoManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'centro_poblado_index');

        $form = $this->createForm(CentroPobladoType::class, $centroPoblado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($centroPoblado)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('centro_poblado_index', ['id' => $centroPoblado->getId()]);
        }

        return $this->render(
            'centro_poblado/edit.html.twig',
            [
                'centro_poblado' => $centroPoblado,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'centro_poblado_delete', methods: ['POST'])]
    public function delete(Request $request, CentroPoblado $centroPoblado, CentroPobladoManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'centro_poblado_index');

        if ($this->isCsrfTokenValid('delete'.$centroPoblado->getId(), $request->request->get('_token'))) {
            $centroPoblado->changeActive();
            if ($manager->save($centroPoblado)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('centro_poblado_index');
    }

    #[Route(path: '/{id}/delete', name: 'centro_poblado_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        CentroPoblado $centroPoblado,
        CentroPobladoManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'centro_poblado_index', $centroPoblado);

        if ($this->isCsrfTokenValid('delete_forever'.$centroPoblado->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($centroPoblado)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('centro_poblado_index');
    }

    #[Route(path: '/ajax/centros', name: 'centros_ajax', methods: ['GET', 'POST'])]
    public function findCentros(Request $request, EntityManagerInterface $em)
    {
        $distrito_id = $request->get('distrito_id');
        $user = $this->getUser();
        $centroPobladoUser = $user->getCentroPoblado();
        if ('TODOS' !== $centroPobladoUser->getNombre()) {
            $centros = $em->getRepository(CentroPoblado::class)->findBy(['id' => $centroPobladoUser->getId()]);
        } else {
            $centros = $em->getRepository(CentroPoblado::class)->findByDistrito($distrito_id);
        }

        $responseArray = [];
        foreach ($centros as $centro) {
            $responseArray[] = [
                'id' => $centro->getId(),
                'name' => $centro->getNombre(),
            ];
        }

        return new JsonResponse($responseArray);
    }
}
