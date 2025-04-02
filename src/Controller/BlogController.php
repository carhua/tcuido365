<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Manager\BlogManager;
use App\Repository\BlogRepository;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/blog')]
class BlogController extends BaseController
{
    use TraitExcelFechas;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'blog_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'blog_index_paginated')]
    public function index(Request $request, int $page, BlogManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'blog_index');

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'blog/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', methods: ['GET'], name: 'blog_export')]
    public function export(BlogRepository $blogRepository): Response
    {
        $this->denyAccess(Security::EXPORT, 'blog_index');
        try {
            $data = $blogRepository->findAll();
            $fileNameTemp = self::blogExport($data);

            return $this->file($fileNameTemp, 'Noticias.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 500);
        }
    }

    #[Route(path: '/new', name: 'blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BlogManager $manager): Response
    {
        $this->denyAccess(Security::NEW, 'blog_index');

        $tipo = new blog();
        $form = $this->createForm(BlogType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo->setOwner($this->getUser());
            if ($manager->save($tipo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('blog_index');
        }

        return $this->render(
            'blog/new.html.twig',
            [
                'blog' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'blog_show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        $this->denyAccess(Security::VIEW, 'blog_index');

        return $this->render('blog/show.html.twig', ['blog' => $blog]);
    }

    #[Route(path: '/{id}/edit', name: 'blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, blog $blog, blogManager $manager): Response
    {
        $this->denyAccess(Security::EDIT, 'blog_index');

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($blog)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('blog_index', ['id' => $blog->getId()]);
        }

        return $this->render(
            'blog/edit.html.twig',
            [
                'blog' => $blog,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, BlogManager $manager): Response
    {
        $this->denyAccess(Security::DELETE, 'blog_index');

        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $blog->changeActive();
            if ($manager->save($blog)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('blog_index');
    }

    #[Route(path: '/{id}/delete', name: 'blog_delete_forever', methods: ['POST'])]
    public function deleteForever(
        Request $request,
        Blog $blog,
        BlogManager $manager
    ): Response {
        $this->denyAccess(Security::MASTER, 'blog_index', $blog);

        if ($this->isCsrfTokenValid('delete_forever'.$blog->getId(),
            $request->request->get('_token'))) {
            if ($manager->remove($blog)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('blog_index');
    }
}
