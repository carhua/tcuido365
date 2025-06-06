<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\TipoMaltrato;
use App\Form\TipoMaltratoType;
use App\Manager\TipoMaltratoManager;
use App\Repository\TipoMaltratoRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/tipo/maltrato')]
class TipoMaltratoController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'tipo_maltrato_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'tipo_maltrato_index_paginated')]
    public function index(Request $request, int $page, TipoMaltratoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'tipo_maltrato_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'tipo_maltrato/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'tipo_maltrato_export')]
    public function export(Request $request, TipoMaltratoRepository $tipoMaltratoRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'tipo_maltrato_index');
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
                $data = $tipoMaltratoRepository->findAll();
            } else {
                $qb = $tipoMaltratoRepository->createQueryBuilder('e');
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
            
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE TIPOS DE MALTRATO', 'TipoMaltrato', 'TipoMaltrato.xlsx');

            return $this->file($fileNameTemp, 'TipoMaltrato.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'tipo_maltrato_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TipoMaltratoManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'tipo_maltrato_index');

        $tipo = new TipoMaltrato();
        $form = $this->createForm(TipoMaltratoType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_maltrato_index');
        }

        return $this->render(
            'tipo_maltrato/new.html.twig',
            [
                'tipoMaltrato' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_maltrato_show', methods: ['GET'])]
    public function show(TipoMaltrato $tipoMaltrato): Response
    {
        $this->denyAccess(Security::VIEW, 'tipo_maltrato_index');

        return $this->render('tipo_maltrato/show.html.twig', ['tipo_maltrato' => $tipoMaltrato]);
    }

    #[Route(path: '/{id}/edit', name: 'tipo_maltrato_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoMaltrato $tipoMaltrato, TipoMaltratoManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'tipo_maltrato_index');

        $form = $this->createForm(TipoMaltratoType::class, $tipoMaltrato);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($tipoMaltrato)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('tipo_maltrato_index', ['id' => $tipoMaltrato->getId()]);
        }

        return $this->render(
            'tipo_maltrato/edit.html.twig',
            [
                'tipo_maltrato' => $tipoMaltrato,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'tipo_maltrato_delete', methods: ['POST'])]
    public function delete(Request $request, TipoMaltrato $tipoMaltrato, TipoMaltratoManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'tipo_maltrato_index');

        if ($this->isCsrfTokenValid('delete'.$tipoMaltrato->getId(), $request->request->get('_token'))) {
            $tipoMaltrato->changeActive();
            if ($manager->save($tipoMaltrato)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_maltrato_index');
    }

    #[Route(path: '/{id}/delete', name: 'tipo_maltrato_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        TipoMaltrato $tipoMaltrato,
        TipoMaltratoManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'tipo_maltrato_index', $tipoMaltrato);

        if ($this->isCsrfTokenValid('delete_forever'.$tipoMaltrato->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($tipoMaltrato)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('tipo_maltrato_index');
    }
}
