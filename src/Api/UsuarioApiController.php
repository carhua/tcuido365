<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\Usuario;
use App\Entity\CasoDesaparecido;
use App\Entity\CasoDesproteccion;
use App\Entity\CasoTrata;
use App\Entity\CasoViolencia;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UsuarioApiController extends ApiController
{
    use TraitUser;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Obtiene un listado de todos los usuarios de la aplicación.
     * @Route("/usuario/list", name="api_usuario_data", methods={"GET"})
     * @deprecated La ruta usa la anotación @Route en lugar del atributo #[Route].
     * @return Response JSON con los datos de los usuarios.
     */
    public function data(): Response
    {
        /** @var Usuario[] $usuarios */
        $usuarios = $this->entityManager->getRepository(Usuario::class)->findAll();
        $data = [];
        foreach ($usuarios as $usuario) {
            $item = [];
            $item['name'] = $usuario->getFullName();
            $item['password'] = $usuario->getPassword();
            $item['username'] = $usuario->getUsername();
            $item['centroPoblado'] = $usuario->getCentroPoblado() ? $usuario->getCentroPoblado()->getNombre() : null;
            $item['centro_poblado_id'] = $usuario->getCentroPoblado() ? $usuario->getCentroPoblado()->getId() : null;
            $item['distrito_id'] = $usuario->getDistrito() ? $usuario->getDistrito()->getId() : null;
            $item['distrito'] = $usuario->getDistrito() ? $usuario->getDistrito()->getNombre() : null;

            $data[] = $item;
        }

        return $this->response(['data' => $data]);
    }

    /**
     * Busca un usuario por su 'username' y valida sus roles.
     * @Route("/usuario/find", name="api_usuario_find_data", methods={"POST"})
     * @deprecated La ruta usa la anotación @Route en lugar del atributo #[Route].
     * @param Request $request La solicitud HTTP con el 'username' en formato JSON.
     * @return Response JSON con los datos del usuario si se encuentra y es válido.
     */
    public function userFind(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $username = $content['username'];

        $usuario = $this->entityManager->getRepository(Usuario::class)->findOneBy(['username' => $username]);
        /** @var Usuario $usuario */
        $rt = $usuario && self::validarRoles($usuario->getRoles());
        if (null !== $usuario && $rt) {
            $item['name'] = $usuario->getFullName();
            $item['password'] = $usuario->getPassword();
            $item['username'] = $usuario->getUsername();
            $item['centroPoblado'] = $usuario->getCentroPoblado() ? $usuario->getCentroPoblado()->getNombre() : null;
            $item['centro_poblado_id'] = $usuario->getCentroPoblado() ? $usuario->getCentroPoblado()->getId() : null;
            $item['distrito_id'] = $usuario->getDistrito() ? $usuario->getDistrito()->getId() : null;
            $item['distrito'] = $usuario->getDistrito() ? $usuario->getDistrito()->getNombre() : null;
            $data = $item;
            $state = true;
        } else {
            $data = [];
            $state = false;
        }

        return $this->response(['data' => $data], $state);
    }

    /**
     * Encuentra todos los casos reportados por un usuario específico.
     * @Route("/data/find", name="api_data_find_data", methods={"POST"})
     * @deprecated La ruta usa la anotación @Route en lugar del atributo #[Route].
     * @param Request $request La solicitud HTTP con el 'username' en formato JSON.
     * @return Response JSON con un listado de todos los casos asociados al usuario.
     */
    public function dataFind(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $username = $content['username'];
        $data = [];

        $datosD = $this->entityManager->getRepository(CasoDesaparecido::class)->findBy(['usuarioApp' => $username]);
        foreach ($datosD as $tipo) {
            $data[] = ['tipo' => 'D', 'id' => $tipo->getId(), 'descripcion' => $tipo->getDescripcionReporte(), 'fecha' => $tipo->getFechaReporte()];
        }

        $datosP = $this->entityManager->getRepository(CasoDesproteccion::class)->findBy(['usuarioApp' => $username]);
        foreach ($datosP as $tipo) {
            $data[] = ['tipo' => 'P', 'id' => $tipo->getId(), 'descripcion' => $tipo->getDescripcionReporte(), 'fecha' => $tipo->getFechaReporte()];
        }

        $datosT = $this->entityManager->getRepository(CasoTrata::class)->findBy(['usuarioApp' => $username]);
        foreach ($datosT as $tipo) {
            $data[] = ['tipo' => 'T', 'id' => $tipo->getId(), 'descripcion' => $tipo->getDescripcionReporte(), 'fecha' => $tipo->getFechaReporte()];
        }

        $datosV = $this->entityManager->getRepository(CasoViolencia::class)->findBy(['usuarioApp' => $username]);
        foreach ($datosV as $tipo) {
            $data[] = ['tipo' => 'V', 'id' => $tipo->getId(), 'descripcion' => $tipo->getDescripcionReporte(), 'fecha' => $tipo->getFechaReporte()];
        }

        $state = true;

        array_multisort(array_column($data, 'fecha'), SORT_DESC, $data);

        return $this->response(['data' => $data], $state);
    }
}
