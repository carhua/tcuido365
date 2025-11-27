<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\Blog;
use App\Entity\Distrito;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogApiController extends ApiController
{
    use TraitUser;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Obtiene un listado de noticias de tipo 'Normal'.
     * Espera un cuerpo JSON con información del usuario para filtrar las noticias por su ubicación.
     * @return Response JSON con el listado de noticias.
     */
    #[Route(path: '/noticias/listado', name: 'api_noticia_data', methods: ['POST'])]
    public function dataNoticiaNormal(Request $request, RequestStack $requestStack): Response
    {
        $content = json_decode($request->getContent(), true);
        $usuario = $content['usuario'];
        $centroPobladoid = $usuario['centro_poblado_id'];
        $distritoId = $usuario['distrito_id'];
        $distritoUser = $this->entityManager->getRepository(Distrito::class)->findOneBy(['id' => $distritoId]);
        $provinciaId = $distritoUser->getProvincia()->getId();

        $dominio = $requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        $path = $dominio.'/media';

        // obtenemos las ultimas 10 noticias con acceso general para todas las noticias
        /** @var Blog[] $blogs */
        $blogs = $this->entityManager->getRepository(Blog::class)
                ->findByNoticiasDistrito($provinciaId, $distritoId, $centroPobladoid, 'Normal');

        $data = [];
        foreach ($blogs as $blog) {
            $item = [];
            $item['titulo'] = $blog->getTitulo();
            $item['tipo'] = $blog->getTipo();
            $item['descripcion'] = $blog->getDescripcion();
            $item['fecha'] = $blog->getCreatedAt()->format('d/m/Y');
            $item['image'] = null !== $blog->getAdjunto() ? $path.'/'.$blog->getAdjunto()->path() : null;

            $data[] = $item;
        }

        return $this->response(['data' => $data]);
    }

    /**
     * Obtiene un listado de noticias de tipo 'Alerta'.
     * Espera un cuerpo JSON con información del usuario para filtrar las noticias por su ubicación.
     * @return Response JSON con el listado de noticias de alerta.
     */
    #[Route(path: '/noticias/alertas', name: 'api_noticia_data_alertas', methods: ['POST'])]
    public function dataNoticiaEmergente(Request $request, RequestStack $requestStack): Response
    {
        $content = json_decode($request->getContent(), true);
        $usuario = $content['usuario'];
        $centroPobladoid = $usuario['centro_poblado_id'];
        $distritoId = $usuario['distrito_id'];
        $distritoUser = $this->entityManager->getRepository(Distrito::class)->findOneBy(['id' => $distritoId]);
        $provinciaId = $distritoUser->getProvincia()->getId();

        $dominio = $requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        $path = $dominio.'/media';

        // obtenemos las ultimas 10 noticias con acceso general para todas las noticias
        /** @var Blog[] $blogs */
        $blogs = $this->entityManager->getRepository(Blog::class)
            ->findByNoticiasDistrito($provinciaId, $distritoId, $centroPobladoid, 'Alerta');

        $data = [];
        foreach ($blogs as $blog) {
            $item = [];
            $item['titulo'] = $blog->getTitulo();
            $item['tipo'] = $blog->getTipo();
            $item['descripcion'] = $blog->getDescripcion();
            $item['fecha'] = $blog->getCreatedAt()->format('d/m/Y');
            $item['image'] = null !== $blog->getAdjunto() ? $path.'/'.$blog->getAdjunto()->path() : null;

            $data[] = $item;
        }

        return $this->response(['data' => $data]);
    }
}
