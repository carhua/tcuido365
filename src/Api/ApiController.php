<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\EstadoCivil;
use App\Entity\FormaCaptacion;
use App\Entity\Nacionalidad;
use App\Entity\Sexo;
use App\Entity\SituacionEncontrada;
use App\Entity\TipoDocumento;
use App\Entity\TipoExplotacion;
use App\Entity\TipoMaltrato;
use App\Entity\VinculoFamiliar;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends AbstractController
{
    protected function authorization(Request $request): array
    {
        if (!$request->headers->has('Authorization') || 0 !== mb_strpos($request->headers->get('Authorization'), 'Bearer ')) {
            return ['status' => false, 'message' => 'No tiene autorización'];
        }

        $token = mb_substr($request->headers->get('Authorization'), 7);
        if ($token !== $this->getParameter('app.token')) {
            return ['status' => false, 'message' => 'Token no válido'];
        }

        return ['status' => true];
    }

    public function denyAccessUnlessAuthorization(Request $request): void
    {
        $authorization = $this->authorization($request);
        if (!$authorization['status']) {
            throw new \InvalidArgumentException($authorization['message']);
        }
    }

    public function response(array $data, bool $status = true): Response
    {
        return new JsonResponse(array_merge(['status' => $status], $data));
    }

    public function listTipoMaltrato(ObjectManager $em): array
    {
        $tipoMaltratos = $em->getRepository(TipoMaltrato::class)->findBy(['isActive' => 1]);
        $dataMaltratos = [];
        foreach ($tipoMaltratos as $tipo) {
            $dataMaltratos[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $dataMaltratos;
    }

    public function listEstados(ObjectManager $em): array
    {
        $datos = $em->getRepository(EstadoCivil::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }

    public function listTipoDocumento(ObjectManager $em): array
    {
        $datos = $em->getRepository(TipoDocumento::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }

    public function listNacionalidad(ObjectManager $em): array
    {
        $datos = $em->getRepository(Nacionalidad::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }

    public function listSituaciones(ObjectManager $em): array
    {
        $datos = $em->getRepository(SituacionEncontrada::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }

    public function listVinculos(ObjectManager $em): array
    {
        $datos = $em->getRepository(VinculoFamiliar::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }

    public function listSexo(ObjectManager $em): array
    {
        $datos = $em->getRepository(Sexo::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }

    public function listCaptaciones(ObjectManager $em): array
    {
        $datos = $em->getRepository(FormaCaptacion::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }

    public function listTipoExplotaciones(ObjectManager $em): array
    {
        $datos = $em->getRepository(TipoExplotacion::class)->findBy(['isActive' => 1]);
        $data = [];
        foreach ($datos as $tipo) {
            $data[] = ['key' => $tipo->getId(), 'label' => $tipo->getNombre()];
        }

        return $data;
    }
}
