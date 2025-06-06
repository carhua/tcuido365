<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\VinculoFamiliar;
use App\Form\VinculoFamiliarType;
use App\Manager\VinculoFamiliarManager;
use App\Repository\VinculoFamiliarRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/vinculo/familiar')]
class VinculoFamiliarController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'vinculo_familiar_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'vinculo_familiar_index_paginated')]
    public function index(Request $request, int $page, VinculoFamiliarManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'vinculo_familiar_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'vinculo_familiar/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'vinculo_familiar_export')]
    public function export(Request $request, VinculoFamiliarRepository $vinculoFamiliarRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'vinculo_familiar_index');
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
                $data = $vinculoFamiliarRepository->findAll();
            } else {
                $qb = $vinculoFamiliarRepository->createQueryBuilder('e');
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
            
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE VINCULOS FAMILIARES', 'VinculoFamiliar', 'VinculoFamiliar.xlsx');

            return $this->file($fileNameTemp, 'VinculoFamiliar.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'vinculo_familiar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VinculoFamiliarManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'vinculo_familiar_index');

        $tipo = new VinculoFamiliar();
        $form = $this->createForm(VinculoFamiliarType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('vinculo_familiar_index');
        }

        return $this->render(
            'vinculo_familiar/new.html.twig',
            [
                'VinculoFamiliar' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'vinculo_familiar_show', methods: ['GET'])]
    public function show(VinculoFamiliar $vinculoFamiliar): Response
    {
        $this->denyAccess(Security::VIEW, 'vinculo_familiar_index');

        return $this->render('vinculo_familiar/show.html.twig', ['vinculo_familiar' => $vinculoFamiliar]);
    }

    #[Route(path: '/{id}/edit', name: 'vinculo_familiar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VinculoFamiliar $vinculoFamiliar, VinculoFamiliarManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'vinculo_familiar_index');

        $form = $this->createForm(VinculoFamiliarType::class, $vinculoFamiliar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($vinculoFamiliar)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('vinculo_familiar_index', ['id' => $vinculoFamiliar->getId()]);
        }

        return $this->render(
            'vinculo_familiar/edit.html.twig',
            [
                'vinculo_familiar' => $vinculoFamiliar,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'vinculo_familiar_delete', methods: ['POST'])]
    public function delete(Request $request, VinculoFamiliar $vinculoFamiliar, VinculoFamiliarManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'vinculo_familiar_index');

        if ($this->isCsrfTokenValid('delete'.$vinculoFamiliar->getId(), $request->request->get('_token'))) {
            $vinculoFamiliar->changeActive();
            if ($manager->save($vinculoFamiliar)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('vinculo_familiar_index');
    }

    #[Route(path: '/{id}/delete', name: 'vinculo_familiar_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        VinculoFamiliar $vinculoFamiliar,
        VinculoFamiliarManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'vinculo_familiar_index', $vinculoFamiliar);

        if ($this->isCsrfTokenValid('delete_forever'.$vinculoFamiliar->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($vinculoFamiliar)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('vinculo_familiar_index');
    }
}
