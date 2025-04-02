<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Parametro;
use App\Form\ParametroType;
use App\Repository\ParametroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(path: '/admin/parametro')]
class ParametroController extends AbstractController
{
    #[Route(path: '/', name: 'parametro_index', methods: 'GET')]
    public function index(ParametroRepository $repository)
    {
        try {
            $this->denyAccessUnlessGranted('list', 'parametro_index');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('danger', 'No tiene permiso en esta seccion');

            return $this->redirectToRoute('homepage');
        }
        $parametros = $repository->findData();
        $padres = [];
        foreach ($parametros as $parametro) {
            if (null === $parametro['padre'] && false === \in_array($parametro['nombre'], $padres, true)) {
                $padres[$parametro['id']] = $parametro['nombre'];
            }
        }

        return $this->render('parametro/list.html.twig', [
            '_padres' => $padres,
        ]);
    }

    #[Route(path: '/info', name: 'info_index', methods: 'GET')]
    public function info()
    {
        phpinfo();
    }

    #[Route(path: '/new', name: 'parametro_new', methods: 'GET|POST')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        try {
            $this->denyAccessUnlessGranted('new', 'parametro_index');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('danger', 'No tiene permiso en esta seccion');

            return $this->redirectToRoute('homepage');
        }
        $parametro = new Parametro();
        $form = $this->createForm(ParametroType::class, $parametro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($parametro);
            $em->flush();

            return $this->redirectToRoute('parametro_index');
        }

        return $this->render('parametro/new.html.twig', [
            'parametro' => $parametro,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'parametro_show', methods: 'GET')]
    public function show(Parametro $parametro): Response
    {
        try {
            $this->denyAccessUnlessGranted('view', 'parametro_index');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('danger', 'No tiene permiso en esta seccion');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('parametro/show.html.twig', ['parametro' => $parametro]);
    }

    #[Route(path: '/{id}/edit', name: 'parametro_edit', methods: 'GET|POST')]
    public function edit(Request $request, Parametro $parametro, EntityManagerInterface $em): Response
    {
        try {
            $this->denyAccessUnlessGranted('edit', 'parametro_index');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('danger', 'No tiene permiso en esta seccion');

            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(ParametroType::class, $parametro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('parametro_index', ['id' => $parametro->getId()]);
        }

        return $this->render('parametro/edit.html.twig', [
            'parametro' => $parametro,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'parametro_delete', methods: 'DELETE')]
    public function delete(Request $request, Parametro $parametro, EntityManagerInterface $em): Response
    {
        try {
            $this->denyAccessUnlessGranted('delete', 'parametro_index');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('danger', 'No tiene permiso en esta seccion');

            return $this->redirectToRoute('homepage');
        }
        if ($this->isCsrfTokenValid('delete'.$parametro->getId(), $request->request->get('_token'))) {
            $parametro->setActivo(!$parametro->getActivo());
            $em->persist($parametro);
            $em->flush();
        }

        return $this->redirectToRoute('parametro_index');
    }

    #[Route(path: '/data/ajax', name: 'parametro_data_ajax', methods: ['GET'])]
    public function select(Request $request, EntityManagerInterface $em): Response
    {
        $draw = $request->query->get('draw', null);
        $length = $request->query->get('length', 10);
        $start = $request->query->get('start', 0);
        $search = $request->query->get('search', null);
        $columns = $request->query->get('columns', null);
        $order = $request->query->get('order', null);

        $filtrados = $em->getRepository(Parametro::class)->findData($length, $start, $search, $columns, $order);
        $totales = $em->getRepository(Parametro::class)->findData(null, null, null, $columns);

        $json_data = [
            'draw' => (int) $draw,
            'recordsTotal' => (int) \count($filtrados),
            'recordsFiltered' => (int) \count($totales),
            'data' => $filtrados,   // total data array
        ];

        return new JsonResponse($json_data);
    }
}
