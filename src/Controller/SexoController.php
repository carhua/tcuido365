<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Sexo;
use App\Form\SexoType;
use App\Manager\SexoManager;
use App\Repository\SexoRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/sexo')]
class SexoController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'sexo_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'sexo_index_paginated')]
    public function index(Request $request, int $page, SexoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'sexo_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'sexo/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'sexo_export')]
    public function export(Request $request, SexoRepository $sexoRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'sexo_index');
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
                $data = $sexoRepository->findAll();
            } else {
                $qb = $sexoRepository->createQueryBuilder('e');
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
            
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE SEXOS', 'Sexo', 'Sexo.xlsx');

            return $this->file($fileNameTemp, 'Sexo.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'sexo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SexoManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'sexo_index');

        $tipo = new Sexo();
        $form = $this->createForm(SexoType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('sexo_index');
        }

        return $this->render(
            'sexo/new.html.twig',
            [
                'Sexo' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sexo_show', methods: ['GET'])]
    public function show(Sexo $Sexo): Response
    {
        $this->denyAccess(Security::VIEW, 'sexo_index');

        return $this->render('sexo/show.html.twig', ['sexo' => $Sexo]);
    }

    #[Route(path: '/{id}/edit', name: 'sexo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sexo $sexo, SexoManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'sexo_index');

        $form = $this->createForm(SexoType::class, $sexo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($sexo)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('sexo_index', ['id' => $sexo->getId()]);
        }

        return $this->render(
            'sexo/edit.html.twig',
            [
                'sexo' => $sexo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sexo_delete', methods: ['POST'])]
    public function delete(Request $request, Sexo $sexo, SexoManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'sexo_index');

        if ($this->isCsrfTokenValid('delete'.$sexo->getId(), $request->request->get('_token'))) {
            $sexo->changeActive();
            if ($manager->save($sexo)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('sexo_index');
    }

    #[Route(path: '/{id}/delete', name: 'sexo_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Sexo $sexo,
        SexoManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'sexo_index', $sexo);

        if ($this->isCsrfTokenValid('delete_forever'.$sexo->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($sexo)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('sexo_index');
    }
}
