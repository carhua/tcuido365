<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CasoViolencia;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\TipoMaltrato;
use App\Entity\Usuario;
use App\Entity\Accion;
use App\Entity\Estado;
use App\Entity\Institucion;
use App\Manager\CasoViolenciaManager;
use App\Manager\AccionManager;
use App\Form\AccionType;
use App\Security\Security;
use App\Traits\TraitDate;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ObjectManager;

#[Route(path: '/casos-violencia')]
class CasoViolenciaController extends BaseController
{
    use TraitDate;
    use TraitUser;

    #[Route(path: '/', defaults: ['page' => '1'], methods: ['GET'], name: 'caso_violencia_index')]
    #[Route(path: '/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'caso_violencia_index_paginated')]
    public function index(Request $request, int $page, CasoViolenciaManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'caso_violencia_index');

        $fechaInicio = $request->query->get('finicial');
        $fechaFinal = $request->query->get('ffinal');
        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $estado = $request->query->get('estado');
        $cantidad = "-";

        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);
        //  $centros = self::listCentrosByRol($rteniente, $user, $em);
        $tipos = $em->getRepository(TipoMaltrato::class)->findBy(['isActive' => true]);

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

        if (null === $request->query->get('tipoMaltrato')) {
            $request->query->set('tipoMaltrato', 0);
        }
        if ('' != $request->query->get('estado')) {
            $request->query->set('estado', $estado);
        }
        //if (null !== $oprovincia && null !== $odistrito) {    antes, obloigaba ambos
        if ((null !== $fechaInicio && null !== $fechaFinal) || (null !== $oprovincia && null !== $odistrito)) {
            $paginator = $manager->listIndex($request->query->all(), $page, $user);
            $cantidad = count($paginator);
        } else {
            $paginator = null;
        }

        return $this->render(
            'caso_violencia/index.html.twig',
            [
                'paginator' => $paginator,
                'finicio' => $fechaInicio,
                'ffinal' => $fechaFinal,
                'tipos' => $tipos,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
                'usuarios' => $usuarios,
                'cantidad' => $cantidad,
            ]
        );
    }

    #[Route(path: '/control', defaults: ['page' => '1'], methods: ['GET'], name: 'caso_violenciac_index')]
    #[Route(path: '/control/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'caso_violenciac_index_paginated')]
    public function controlCasosViolencia(Request $request, int $page, CasoViolenciaManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'caso_violencia_index');

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
        $tipos = $em->getRepository(TipoMaltrato::class)->findBy(['isActive' => true]);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', 182);
        }

        if (null !== $oprovincia) {
            $request->query->set('oprovincia', $oprovincia);
        }

        if (null !== $odistrito) {
            $request->query->set('odistrito', $odistrito);
        }

        if (null === $request->query->get('tipoMaltrato')) {
            $request->query->set('tipoMaltrato', 0);
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
            'control_casos/caso_violencia.html.twig',
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

    #[Route(path: '/grafico-casos-violencia', name: 'grafico_violencia_index', methods: ['GET'])]
    public function graficoViolencia(Request $request, CasoViolenciaManager $manager, EntityManagerInterface $em): Response
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
        $tipos = $em->getRepository(TipoMaltrato::class)->findBy(['isActive' => true]);
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

        if (null === $request->query->get('tipoMaltrato')) {
            $request->query->set('tipoMaltrato', 0);
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
            'agraficos/grafico_violencia_index.html.twig',
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

    #[Route(path: '/{id}', name: 'caso_violencia_show', methods: ['GET'])]
    public function show(CasoViolencia $casoviolencia, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::VIEW, 'caso_violencia_index');

        $cod = $casoviolencia->getCodigo();
        $distrito = $casoviolencia->getDistrito();

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
        $estadoActual = $casoviolencia->getEstadoCaso();
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

                    $casoviolencia->setEstadoCaso($estadoRecibido->getEstado());
                    $em->persist($casoviolencia);

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
        $estadoActualCaso = $casoviolencia->getEstadoCaso();
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

        return $this->render('caso_violencia/show.html.twig', [
            'caso_violencia' => $casoviolencia, 'accion' => $accion, 'institucion' => $institucion, 'estados_siguientes' => $estadosSiguientes
        ]);
    }

    #[Route(path: '/control/{id}', name: 'caso_violenciac_show', methods: ['GET'])]
    public function showControl(CasoViolencia $casoDesproteccion): Response
    {
        $this->denyAccess(Security::VIEW, 'caso_violenciac_index');

        return $this->render('control_casos/show_control_violencia.html.twig', ['caso_violencia' => $casoDesproteccion]);
    }

    #[Route(path: '/{id}', name: 'accion_violencia_new', methods: ['POST'])]
    public function accion(Request $request, CasoViolencia $casoviolencia, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::NEW, 'caso_violencia_index');

        if (in_array($casoviolencia->getEstadoCaso(), ['Cerrado', 'Archivado'])) {
            $this->addFlash('error', 'No se pueden registrar nuevas acciones en un caso cerrado o archivado.');

            return $this->redirectToRoute('caso_violencia_show', ['id' => $casoviolencia->getId()]);
        }

        try{
            $data = $request->request->all();

            $caso = $em->getRepository(CasoViolencia::class)->findOneBy(['codigo' => $data['codigo']]);
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
            dd($ex);
        }

        return $this->redirectToRoute('caso_violencia_show', ['id' => $casoviolencia->getId()]);

    }

    public static function findEstado($id, ObjectManager $em): ?Estado
    {
        return $em->getRepository(Estado::class)->find($id);
    }

    public static function findInstitucion($id, ObjectManager $em): ?Institucion
    {
        return $em->getRepository(Institucion::class)->find($id);
    }

    #[Route(path: '/{id}/reopen', name: 'caso_violencia_reopen', methods: ['POST'])]
    public function reopen(Request $request, CasoViolencia $casoviolencia, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::MASTER, 'caso_violencia_index');

        if ('Cerrado' !== $casoviolencia->getEstadoCaso()) {
            $this->addFlash('error', 'Solo se pueden reaperturar casos que se encuentran cerrados.');

            return $this->redirectToRoute('caso_violencia_show', ['id' => $casoviolencia->getId()]);
        }

        if ($this->isCsrfTokenValid('reopen'.$casoviolencia->getId(), $request->request->get('_token'))) {
            $estadoRecibido = $em->getRepository(Estado::class)->find(1); // ID 1 = Recibido
            if ($estadoRecibido) {
                // Actualizar estado del caso
                $casoviolencia->setEstadoCaso($estadoRecibido->getEstado());
                $em->persist($casoviolencia);

                // Crear la acción de reapertura
                $nuevaAccion = new Accion();
                $nuevaAccion->setCodigo($casoviolencia->getCodigo());
                $nuevaAccion->setFecha(new \DateTime());
                $nuevaAccion->setOwner($this->getUser());
                $nuevaAccion->setDescripcion('Caso reaperturado por administrador.');
                $nuevaAccion->setEstado($estadoRecibido);
                $em->persist($nuevaAccion);

                $em->flush();
                $this->addFlash('success', 'El caso ha sido reaperturado y su estado es "Recibido".');
            }
        }

        return $this->redirectToRoute('caso_violencia_show', ['id' => $casoviolencia->getId()]);
    }
    
}
