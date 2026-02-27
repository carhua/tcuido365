<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CasoDesproteccion;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\SituacionEncontrada;
use App\Entity\Usuario;
use App\Entity\Accion;
use App\Entity\Estado;
use App\Entity\Institucion;
use App\Manager\CasoDesproteccionManager;
use App\Security\Security;
use App\Traits\TraitDate;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/casos-desproteccion')]
class CasoDesproteccionController extends BaseController
{
    use TraitDate;
    use TraitUser;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'caso_desproteccion_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'caso_desproteccion_index_paginated')]
    public function index(Request $request, int $page, CasoDesproteccionManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'caso_desproteccion_index');

        $fechaInicio = $request->query->get('finicial');
        $fechaFinal = $request->query->get('ffinal');
        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $cantidad = "-";

        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);
        $distritos = self::listDistritosByRol($rteniente, $user, $em);
       //  $centros = self::listCentrosByRol($rteniente, $user, $em);


        if($provinciaId=='' && $provincias[0]->getId()){
            $provinciaId = $provincias[0]->getId();
            $oprovincia = $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        }else{
            $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        }

        if($distritoId=='' && $distritos[0]->getId()){
            $distritoId = $distritos[0]->getId();
            $odistrito = $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        }else{
            $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        }

        //$odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        //$oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        $tipos = $em->getRepository(SituacionEncontrada::class)->findBy(['isActive' => true]);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', 182);
        }

        if (null !== $oprovincia) {
            $request->query->set('oprovincia', $oprovincia);
        }

        if (null !== $odistrito) {
            $request->query->set('odistrito', $odistrito);
        }

        if (null === $request->query->get('situacion')) {
            $request->query->set('situacion', 0);
        }
/*
        if (null === $request->query->get('estado')) {
            $request->query->set('estado', 'Notificado');
        }
*/
        if ((null !== $fechaInicio && null !== $fechaFinal) || (null !== $oprovincia && null !== $odistrito)) {
            $paginator = $manager->listIndex($request->query->all(), $page, $user);
            $cantidad = count($paginator);
        } else {
            $paginator = null;
        }

        return $this->render(
            'caso_desproteccion/index.html.twig',
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

    #[Route(path: '/control', defaults: ['page' => '1'], methods: ['GET'], name: 'caso_desproteccionc_index')]
    #[Route(path: '/control/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'caso_desproteccionc_index_paginated')]
    public function controlCasosDespostreccion(Request $request, int $page, CasoDesproteccionManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'caso_desproteccionc_index');

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
        //  $centros = self::listCentrosByRol($rteniente, $user, $em);
        $tipos = $em->getRepository(SituacionEncontrada::class)->findBy(['isActive' => true]);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', 182);
        }

        if (null !== $oprovincia) {
            $request->query->set('oprovincia', $oprovincia);
        }

        if (null !== $odistrito) {
            $request->query->set('odistrito', $odistrito);
        }

        if (null === $request->query->get('situacion')) {
            $request->query->set('situacion', 0);
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
            'control_casos/caso_desproteccion.html.twig',
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

    #[Route(path: '/grafico-casos-desproteccion', name: 'grafico_desproteccion_index', methods: ['GET'])]
    public function graficoDesproteccion(Request $request, CasoDesproteccionManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'grafico_desproteccion_index');

        $anioDefault = (int) (new \DateTime('now'))->format('Y');
        $anioInicio = $request->query->get('anioInicio', $anioDefault);
        $anioFinal = $request->query->get('anioFinal', $anioDefault);
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
        $tipos = $em->getRepository(SituacionEncontrada::class)->findBy(['isActive' => true]);

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
/*
        if (null === $request->query->get('situacion')) {
            $request->query->set('situacion', 0);
        }

        if (null === $request->query->get('estado')) {
            $request->query->set('estado', 'Notificado');
        }
*/
        //if (null !== $oprovincia && null !== $odistrito) {
            $casoscv = $manager->graficoCasos($request->query->all(), $user);
            $dataMeses = self::dataMeses($casoscv);
            $dataAnios = self::dataAnios($casoscv);
        //} else {
        //    $dataMeses = self::dataMeses([]);
        //    $dataAnios = self::dataAnios([]);
        //}

        return $this->render(
            'agraficos/grafico_desproteccion_index.html.twig',
            [
                'anioInicio' => $anioInicio,
                'anioFinal' => $anioFinal,
                'finicio' => $fechaInicio,
                'ffinal' => $fechaFinal,
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

    #[Route(path: '/{id}', name: 'caso_desproteccion_show', methods: ['GET'])]
    public function show(CasoDesproteccion $casoDesproteccion, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::VIEW, 'caso_desproteccion_index');

        $cod = $casoDesproteccion->getCodigo();
        $distrito = $casoDesproteccion->getDistrito();

        $odistrito = null === $distrito ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'nombre' => $distrito]);
        $idProvincia = $odistrito->getProvincia()->getId();

        $query = $em->createQuery(
            'SELECT p
            FROM App\Entity\Accion p
            JOIN App\Entity\Institucion i
            WHERE p.codigo = :codigo
            ORDER BY p.fecha DESC'
        )->setParameter('codigo', $cod);

        $accion = $query->getResult();

        // Lógica para acción automática de "Recibido"
        $estadoActual = $casoDesproteccion->getEstadoCaso();
        if (in_array($estadoActual, ['Pendiente', 'Notificado'])) {
            $accionRecibidoExistente = $em->getRepository(Accion::class)->findOneBy(['codigo' => $cod, 'estado' => 1]); // Asumiendo que ID 1 es "Recibido"

            if (!$accionRecibidoExistente) {
                $estadoRecibido = $em->getRepository(Estado::class)->find(1); // ID 1 = Recibido
                if ($estadoRecibido) {
                    $nuevaAccion = new Accion();
                    $nuevaAccion->setCodigo($cod);
                    $nuevaAccion->setFecha(new \DateTime());
                    $nuevaAccion->setOwner($this->getUser());
                    $nuevaAccion->setDescripcion('Caso recibido por el operador.');
                    $nuevaAccion->setEstado($estadoRecibido);
                    $em->persist($nuevaAccion);

                    $casoDesproteccion->setEstadoCaso($estadoRecibido->getEstado());
                    $em->persist($casoDesproteccion);

                    $em->flush();
                    $this->addFlash('info', 'Se ha registrado automáticamente la acción: Caso Recibido.');

                    // Recargar las acciones para mostrar la nueva
                    $accion = $query->getResult();
                }
            }
        }

        $query = $em->createQuery(
            'SELECT p
            FROM App\Entity\Institucion p
            WHERE p.provincia_id = :provinciaId
            AND p.isActive = true
            ORDER BY p.name ASC'
        )->setParameter('provinciaId', $idProvincia);

        $institucion = $query->getResult();

        $estadosSiguientes = [];
        $estadoActualCaso = $casoDesproteccion->getEstadoCaso();
        if ('Recibido' === $estadoActualCaso) {
            // IDs para Atendido y Derivado
            $estadosSiguientes = $em->getRepository(Estado::class)->findBy(['id' => [2, 3]]);
        } elseif ('Atendido' === $estadoActualCaso) {
            // IDs para Derivado, Observado y Cerrado.
            // Asumiendo IDs: Derivado=2, Observado=4, Cerrado=5
            $estadosSiguientes = $em->getRepository(Estado::class)->findBy(['id' => [2, 4, 5]]);
        } elseif ('Derivado' === $estadoActualCaso) {
            // IDs para Atendido, Observado y Cerrado.
            // Asumiendo IDs: Atendido=3, Observado=4, Cerrado=5
            $estadosSiguientes = $em->getRepository(Estado::class)->findBy(['id' => [3, 4, 5]]);
        } elseif ('Observado' === $estadoActualCaso) {
            // IDs para Recibido y Atendido.
            // Asumiendo IDs: Recibido=1, Atendido=3
            $estadosSiguientes = $em->getRepository(Estado::class)->findBy(['id' => [1, 3]]);
        }

        return $this->render('caso_desproteccion/show.html.twig', [
            'caso_desproteccion' => $casoDesproteccion, 'accion' => $accion, 'institucion' => $institucion, 'estados_siguientes' => $estadosSiguientes
        ]);
    }

    #[Route(path: '/control/{id}', name: 'caso_desproteccionc_show', methods: ['GET'])]
    public function showControl(CasoDesproteccion $casoDesproteccion): Response
    {
        $this->denyAccess(Security::VIEW, 'caso_desproteccionc_index');

        return $this->render('control_casos/show_control_desproteccion.html.twig', ['caso_desproteccion' => $casoDesproteccion]);
    }

    #[Route(path: '/{id}', name: 'accion_desproteccionc_new', methods: ['POST'])]
    public function accion(Request $request, CasoDesproteccion $casoDesproteccion, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::NEW, 'caso_desproteccion_index');

        if (in_array($casoDesproteccion->getEstadoCaso(), ['Cerrado', 'Archivado'])) {
            $this->addFlash('error', 'No se pueden registrar nuevas acciones en un caso cerrado o archivado.');

            return $this->redirectToRoute('caso_desproteccion_show', ['id' => $casoDesproteccion->getId()]);
        }

        try{
            $data = $request->request->all();

            $caso = $em->getRepository(CasoDesproteccion::class)->findOneBy(['codigo' => $data['codigo']]);
            $estado = $em->getRepository(Estado::class)->findOneBy(['id' => $data['estado']]);

            $caso->setEstadoCaso($estado->getEstado());
            if('Derivado' === $estado->getEstado() && !empty($data['institucion'])){
                $caso->setInstitucion(self::findInstitucion($data['institucion'], $em));
            }
            $em->persist($caso);
            $em->flush();
            
            $form = new Accion();
            $form->setCodigo($data['codigo']);
            $form->setFecha(new \DateTime($data['fecha']));
            $form->setOwner($this->getUser());
            if('Derivado' === $estado->getEstado() && !empty($data['institucion'])){
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

        return $this->redirectToRoute('caso_desproteccion_show', ['id' => $casoDesproteccion->getId()]);

    }

    public static function findEstado($id, ObjectManager $em): ?Estado
    {
        return $em->getRepository(Estado::class)->find($id);
    }

    public static function findInstitucion($id, ObjectManager $em): ?Institucion
    {
        return $em->getRepository(Institucion::class)->find($id);
    }

    #[Route(path: '/{id}/reopen', name: 'caso_desproteccion_reopen', methods: ['POST'])]
    public function reopen(Request $request, CasoDesproteccion $casoDesproteccion, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::MASTER, 'caso_desproteccion_index');

        if ('Cerrado' !== $casoDesproteccion->getEstadoCaso()) {
            $this->addFlash('error', 'Solo se pueden reaperturar casos que se encuentran cerrados.');

            return $this->redirectToRoute('caso_desproteccion_show', ['id' => $casoDesproteccion->getId()]);
        }

        if ($this->isCsrfTokenValid('reopen'.$casoDesproteccion->getId(), $request->request->get('_token'))) {
            $estadoRecibido = $em->getRepository(Estado::class)->find(1); // ID 1 = Recibido
            if ($estadoRecibido) {
                // Actualizar estado del caso
                $casoDesproteccion->setEstadoCaso($estadoRecibido->getEstado());
                $em->persist($casoDesproteccion);

                // Crear la acción de reapertura
                $nuevaAccion = new Accion();
                $nuevaAccion->setCodigo($casoDesproteccion->getCodigo());
                $nuevaAccion->setFecha(new \DateTime());
                $nuevaAccion->setOwner($this->getUser());
                $nuevaAccion->setDescripcion('Caso reaperturado por administrador.');
                $nuevaAccion->setEstado($estadoRecibido);
                $em->persist($nuevaAccion);

                $em->flush();
                $this->addFlash('success', 'El caso ha sido reaperturado y su estado es "Recibido".');
            }
        }

        return $this->redirectToRoute('caso_desproteccion_show', ['id' => $casoDesproteccion->getId()]);
    }
}
