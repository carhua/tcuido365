<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CasoTrata;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\TipoExplotacion;
use App\Entity\Usuario;
use App\Entity\Accion;
use App\Entity\Estado;
use App\Entity\Institucion;
use App\Manager\CasoTrataManager;
use App\Security\Security;
use App\Traits\TraitDate;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/casos-trata')]
class CasoTrataController extends BaseController
{
    use TraitDate;
    use TraitUser;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'caso_trata_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'caso_trata_index_paginated')]
    public function index(Request $request, int $page, CasoTrataManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'caso_trata_index');

        $fechaInicio = $request->query->get('finicial');
        $fechaFinal = $request->query->get('ffinal');
        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);
        $cantidad = "-";

        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);
        $tipos = $em->getRepository(TipoExplotacion::class)->findBy(['isActive' => true]);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', 182);
        }

        if (null !== $oprovincia) {
            $request->query->set('oprovincia', $oprovincia);
        }

        if (null !== $odistrito) {
            $request->query->set('odistrito', $odistrito);
        }

        if (null === $request->query->get('tipoExplotacion')) {
            $request->query->set('tipoExplotacion', 0);
        }
        if (null === $request->query->get('estado')) {
            $request->query->set('estado', 'Notificado');
        }

        if (null !== $oprovincia && null !== $odistrito) {
            $paginator = $manager->listIndex($request->query->all(), $page, $user);
            $cantidad = count($paginator);
        } else {
            $paginator = null;
        }

        return $this->render(
            'caso_trata/index.html.twig',
            [
                'paginator' => $paginator,
                'finicio' => $fechaInicio,
                'ffinal' => $fechaFinal,
                'tipos' => $tipos,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
                'cantidad' => $cantidad,
            ]
        );
    }

    #[Route(path: '/control', defaults: ['page' => '1'], methods: ['GET'], name: 'caso_tratac_index')]
    #[Route(path: '/control/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'caso_tratac_index_paginated')]
    public function controlCasostrata(Request $request, int $page, CasoTrataManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'caso_tratac_index');

        $fechaInicio = $request->query->get('finicial');
        $fechaFinal = $request->query->get('ffinal');
        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);
        $tipos = $em->getRepository(TipoExplotacion::class)->findBy(['isActive' => true]);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', 182);
        }

        if (null !== $oprovincia) {
            $request->query->set('oprovincia', $oprovincia);
        }

        if (null !== $odistrito) {
            $request->query->set('odistrito', $odistrito);
        }

        if (null === $request->query->get('tipoExplotacion')) {
            $request->query->set('tipoExplotacion', 0);
        }
        if (null === $request->query->get('estado')) {
            $request->query->set('estado', 'Pendiente');
        }

        if (null !== $oprovincia && null !== $odistrito) {
            $paginator = $manager->listIndex($request->query->all(), $page, $user);
        } else {
            $paginator = null;
        }

        return $this->render(
            'control_casos/caso_trata.html.twig',
            [
                'paginator' => $paginator,
                'finicio' => $fechaInicio,
                'ffinal' => $fechaFinal,
                'tipos' => $tipos,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
            ]
        );
    }

    #[Route(path: '/grafico-casos-trata', name: 'grafico_trata_index', methods: ['GET'])]
    public function graficoViolencia(Request $request, CasoTrataManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'grafico_violencia_index');

        $anioDefault = (int) (new \DateTime('now'))->format('Y');
        $fechaInicio = $request->query->get('finicial');
        $fechaFinal = $request->query->get('ffinal');
        $anioInicio = $request->query->get('anioInicio', $anioDefault);
        $anioFinal = $request->query->get('anioFinal', $anioDefault);
        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);
        $tipos = $em->getRepository(TipoExplotacion::class)->findBy(['isActive' => true]);

        $usuarios = [];
        if ($this->isSuperAdmin()) {
            $usuarios = $em->getRepository(Usuario::class)->allNombres();
        }

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', 182);
        }

        if (null !== $oprovincia) {
            $request->query->set('oprovincia', $oprovincia);
        }

        if (null !== $odistrito) {
            $request->query->set('odistrito', $odistrito);
        }

        if (null === $request->query->get('tipoExplotacion')) {
            $request->query->set('tipoExplotacion', 0);
        }
        if (null === $request->query->get('estado')) {
            $request->query->set('estado', 'Notificado');
        }

        if (null !== $oprovincia && null !== $odistrito) {
            $casoscv = $manager->graficoCasos($request->query->all());
            $dataMeses = self::dataMeses($casoscv);
            $dataAnios = self::dataAnios($casoscv);
        } else {
            $dataMeses = self::dataMeses([]);
            $dataAnios = self::dataAnios([]);
        }

        return $this->render(
            'agraficos/grafico_trata_index.html.twig',
            [
                'finicio' => $fechaInicio,
                'ffinal' => $fechaFinal,
                'anioInicio' => $anioInicio,
                'anioFinal' => $anioFinal,
                'tipos' => $tipos,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
                'anioActual' => date('Y'),
                'usuarios' => $usuarios,
                'dataAnios' => $dataAnios,
                'dataMeses' => $dataMeses,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'caso_trata_show', methods: ['GET'])]
    public function show(CasoTrata $casoTrata, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::VIEW, 'caso_trata_index');

        $cod = $casoTrata->getCodigo();

        $query = $em->createQuery(
            'SELECT p
            FROM App\Entity\Accion p
            JOIN App\Entity\Institucion i
            WHERE p.codigo = :codigo
            ORDER BY p.fecha DESC'
        )->setParameter('codigo', $cod);

        $accion = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
            FROM App\Entity\Institucion p'
        );

        $institucion = $query->getResult();

        return $this->render('caso_trata/show.html.twig', ['caso_trata' => $casoTrata, 'accion' => $accion, 'institucion' => $institucion]);
    }

    #[Route(path: '/{id}', name: 'accion_trata_new', methods: ['POST'])]
    public function accion(Request $request, CasoTrata $casotrata, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::NEW, 'caso_violenciac_index');

        try{
            $data = $request->request->all();

            $caso = $em->getRepository(CasoTrata::class)->findOneBy(['codigo' => $data['codigo']]);
            $estado = $em->getRepository(Estado::class)->findOneBy(['id' => $data['estado']]);

            $caso->setEstadoCaso($estado->getEstado());
            if($data['institucion']){
                $caso->setInstitucion(self::findInstitucion($data['institucion'], $em));
            }
            $em->persist($caso);
            $em->flush();
            
            $form = new Accion();
            $form->setCodigo($data['codigo']);
            $form->setFecha(new \DateTime($data['fecha']));
            $form->setOwner($this->getUser());
            if($data['institucion']){
                //$form->setInstitucionId($data['institucion']);
                $form->setInstitucion(self::findInstitucion($data['institucion'], $em));
            }        
            $form->setDescripcion($data['descripcion']);
            $form->setEstado(self::findEstado($data['estado'], $em));

            //dd($form);
            $em->persist($form);
            $em->flush();

            $this->addFlash('success', 'Registro creado!!!');
            //dd('ok');

        }catch (\Exception $ex) {
            $this->addFlash('error', 'Ha ocurrido un error.');
        }

        return $this->redirectToRoute('caso_trata_show', ['id' => $casotrata->getId()]);

    }

    #[Route(path: '/control/{id}', name: 'caso_tratac_show', methods: ['GET'])]
    public function showControl(CasoTrata $casoTrata): Response
    {
        $this->denyAccess(Security::VIEW, 'caso_tratac_index');

        return $this->render('control_casos/show_control_trata.html.twig', ['caso_trata' => $casoTrata]);
    }

    public static function findEstado($id, ObjectManager $em): ?Estado
    {
        return $em->getRepository(Estado::class)->find($id);
    }

    public static function findInstitucion($id, ObjectManager $em): ?Institucion
    {
        return $em->getRepository(Institucion::class)->find($id);
    }
}
