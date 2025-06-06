<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\FormaCaptacion;
use App\Form\FormaCaptacionType;
use App\Manager\FormaCaptacionManager;
use App\Repository\FormaCaptacionRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/forma/captacion')]
class FormaCaptacionController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'forma_captacion_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'forma_captacion_index_paginated')]
    public function index(Request $request, int $page, FormaCaptacionManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'forma_captacion_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'forma_captacion/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'forma_captacion_export')]
    public function export(Request $request, FormaCaptacionRepository $formaCaptacionRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'forma_captacion_index');
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
                $data = $formaCaptacionRepository->findAll();
            } else {
                $qb = $formaCaptacionRepository->createQueryBuilder('e');
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
            
            $fileNameTemp = self::generarExcel($data, 'REPORTE DE FORMAS DE CAPACITACIONES', 'FormaCapacitacion', 'FormaCapacitacion.xlsx');

            return $this->file($fileNameTemp, 'FormaCapacitacion.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'forma_captacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormaCaptacionManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'forma_captacion_index');

        $tipo = new FormaCaptacion();
        $form = $this->createForm(FormaCaptacionType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('forma_captacion_index');
        }

        return $this->render(
            'forma_captacion/new.html.twig',
            [
                'FormaCaptacion' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'forma_captacion_show', methods: ['GET'])]
    public function show(FormaCaptacion $formaCaptacion): Response
    {
        $this->denyAccess(Security::VIEW, 'forma_captacion_index');

        return $this->render('forma_captacion/show.html.twig', ['forma_captacion' => $formaCaptacion]);
    }

    #[Route(path: '/{id}/edit', name: 'forma_captacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FormaCaptacion $formaCaptacion, FormaCaptacionManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'forma_captacion_index');

        $form = $this->createForm(FormaCaptacionType::class, $formaCaptacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($formaCaptacion)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('forma_captacion_index', ['id' => $formaCaptacion->getId()]);
        }

        return $this->render(
            'forma_captacion/edit.html.twig',
            [
                'forma_captacion' => $formaCaptacion,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'forma_captacion_delete', methods: ['POST'])]
    public function delete(Request $request, FormaCaptacion $formaCaptacion, FormaCaptacionManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'forma_captacion_index');

        if ($this->isCsrfTokenValid('delete'.$formaCaptacion->getId(), $request->request->get('_token'))) {
            $formaCaptacion->changeActive();
            if ($manager->save($formaCaptacion)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('forma_captacion_index');
    }

    #[Route(path: '/{id}/delete', name: 'forma_captacion_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        FormaCaptacion $formaCaptacion,
        FormaCaptacionManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'forma_captacion_index', $formaCaptacion);

        if ($this->isCsrfTokenValid('delete_forever'.$formaCaptacion->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($formaCaptacion)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('forma_captacion_index');
    }
}
