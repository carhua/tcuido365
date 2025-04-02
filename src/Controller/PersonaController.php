<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Agraviado;
use App\Entity\Agresor;
use App\Entity\CentroPoblado;
use App\Entity\Denunciante;
use App\Entity\DenuncianteDesaparecido;
use App\Entity\Desaparecido;
use App\Entity\Detenido;
use App\Entity\Distrito;
use App\Entity\MenorEdad;
use App\Entity\Persona;
use App\Entity\Provincia;
use App\Entity\Tutor;
use App\Entity\Usuario;
use App\Entity\Victima;
use App\Manager\AgraviadoManager;
use App\Manager\AgresorManager;
use App\Manager\DenuncianteDesaparecidoManager;
use App\Manager\DenuncianteManager;
use App\Manager\DesaparecidoManager;
use App\Manager\DetenidoManager;
use App\Manager\MenorEdadManager;
use App\Manager\PersonaManager;
use App\Manager\TutorManager;
use App\Manager\VictimaManager;
use App\Security\Security;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonaController extends BaseController
{
    use TraitUser;

    #[Route(path: '/denunciante', defaults: ['page' => '1'], methods: ['GET'], name: 'denunciante_index')]
    #[Route(path: '/denunciante/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'denunciante_index_paginated')]
    public function listDenunciante(Request $request, int $page, DenuncianteManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'denunciante_index');

        $user = $this->getUser();
        $rt = self::validarRoles($user->getRoles());
        $centros = self::listCentrosByRol($rt, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rt) {
            $request->query->set('centroPoblado', 182);
        }

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'persona/denunciante.html.twig',
            [
                'paginator' => $paginator,
                'centros' => $centros,
            ]
        );
    }

    #[Route(path: '/agraviado', defaults: ['page' => '1'], methods: ['GET'], name: 'agraviado_index')]
    #[Route(path: '/agraviado/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'agraviado_index_paginated')]
    public function listAgraviado(Request $request, int $page, AgraviadoManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'agraviado_index');

        $user = $this->getUser();
        $rt = self::validarRoles($user->getRoles());
        $centros = self::listCentrosByRol($rt, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rt) {
            $request->query->set('centroPoblado', 182);
        }

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'persona/agraviado.html.twig',
            [
                'paginator' => $paginator,
                'centros' => $centros,
            ]
        );
    }

    #[Route(path: '/agresor', defaults: ['page' => '1'], methods: ['GET'], name: 'agresor_index')]
    #[Route(path: '/agresor/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'agresor_index_paginated')]
    public function listAgresor(Request $request, int $page, AgresorManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'agresor_index');
        $user = $this->getUser();
        $rt = self::validarRoles($user->getRoles());
        $centros = self::listCentrosByRol($rt, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rt) {
            $request->query->set('centroPoblado', 182);
        }

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'persona/agresor.html.twig',
            [
                'paginator' => $paginator,
                'centros' => $centros,
            ]
        );
    }

    #[Route(path: '/menoredad', defaults: ['page' => '1'], methods: ['GET'], name: 'menoredad_index')]
    #[Route(path: '/menoredad/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'menoredad_index_paginated')]
    public function listMenorEdad(Request $request, int $page, MenorEdadManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'menoredad_index');

        $user = $this->getUser();
        $rt = self::validarRoles($user->getRoles());
        $centros = self::listCentrosByRol($rt, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rt) {
            $request->query->set('centroPoblado', 182);
        }

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'persona/menor_edad.html.twig',
            [
                'paginator' => $paginator,
                'centros' => $centros,
            ]
        );
    }

    #[Route(path: '/tutor', defaults: ['page' => '1'], methods: ['GET'], name: 'tutor_index')]
    #[Route(path: '/tutor/page/{page<[1-9]\d*>}', methods: ['GET'], name: 'tutor_index_paginated')]
    public function listTutor(Request $request, int $page, TutorManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'agresor_index');

        if (null === $request->query->get('centroPoblado')) {
            $request->query->set('centroPoblado', 182);
        }

        $centros = $em->getRepository(CentroPoblado::class)->findBy(['isActive' => true]);

        $paginator = $manager->list($request->query->all(), $page);

        return $this->render(
            'persona/tutor.html.twig',
            [
                'paginator' => $paginator,
                'centros' => $centros,
            ]
        );
    }

    #[Route(path: '/historial-agraviado', methods: ['GET'], name: 'historial_agraviado_index')]
    public function listHistorialAgraviado(Request $request, AgraviadoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_violencia_index');
        $numero = $request->query->get('numeroDocumento');
        $agraviados = $manager->repository()->findBy(['numeroDocumento' => $numero]);

        $historialData = [];
        $persona = null;
        /** @var Agraviado[] $agraviados */
        foreach ($agraviados as $item) {
            $persona = $item->getPersona();
            if (\count($item->getCasoViolenciaAgraviados()) > 0) {
                foreach ($item->getCasoViolenciaAgraviados() as $casoViolenciaAgraviado) {
                    $historialData[] = $casoViolenciaAgraviado->getCasoViolencia();
                }
            }
        }

        return $this->render(
            'historial/show_historial_violencia.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_violencia_index',
            ]
        );
    }

    #[Route(path: '/historial-agresor', methods: ['GET'], name: 'historial_agresor_index')]
    public function listHistorialAgresor(Request $request, AgresorManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_violencia_index');
        $numero = $request->query->get('numeroDocumento');
        $personaid = $request->query->get('personaid');
        if (null !== $numero || '' !== $numero) {
            $agresores = $manager->repository()->findBy(['numeroDocumento' => $numero]);
        } else {
            $agresores = $manager->repository()->findBy(['persona' => $personaid]);
        }

        $historialData = [];
        $persona = null;
        /** @var Agresor[] $agresores */
        foreach ($agresores as $item) {
            $persona = $item->getPersona();
            if (\count($item->getCasoViolenciaAgresors()) > 0) {
                foreach ($item->getCasoViolenciaAgresors() as $casoViolenciaAgresor) {
                    $historialData[] = $casoViolenciaAgresor->getCasoViolencia();
                }
            }
        }

        return $this->render(
            'historial/show_historial_violencia.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_violencia_index',
            ]
        );
    }

    #[Route(path: '/historial-denunciante', methods: ['GET'], name: 'historial_denunciante_index')]
    public function listHistorialDenunciante(Request $request, DenuncianteManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_violencia_index');
        $numero = $request->query->get('numeroDocumento');
        $personaid = $request->query->get('personaid');
        if (null !== $numero || '' !== $numero) {
            $denunciantes = $manager->repository()->findBy(['numeroDocumento' => $numero]);
        } else {
            $denunciantes = $manager->repository()->findBy(['persona' => $personaid]);
        }

        $historialData = [];
        $persona = null;
        /** @var Denunciante[] $denunciantes */
        foreach ($denunciantes as $item) {
            $persona = $item->getPersona();
            if (\count($item->getCasoViolenciaDenunciantes()) > 0) {
                foreach ($item->getCasoViolenciaDenunciantes() as $casoViolenciaDenunciante) {
                    $historialData[] = $casoViolenciaDenunciante->getCasoViolencia();
                }
            }
        }

        return $this->render(
            'historial/show_historial_violencia.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_violencia_index',
            ]
        );
    }

    #[Route(path: '/historial-menoredad', methods: ['GET'], name: 'historial_menoredad_index')]
    public function listHistorialMenorEdad(Request $request, MenorEdadManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desproteccion_index');
        $numero = $request->query->get('numeroDocumento');
        $menors = $manager->repository()->findBy(['numeroDocumento' => $numero]);

        $historialData = [];
        $persona = null;
        /** @var MenorEdad[] $menors */
        foreach ($menors as $item) {
            $persona = $item->getPersona();
            if (\count($item->getCasoDesproteccioMenorEdads()) > 0) {
                foreach ($item->getCasoDesproteccioMenorEdads() as $caso) {
                    $historialData[] = $caso->getCasoDesproteccion();
                }
            }
        }

        return $this->render(
            'historial/show_historial_desproteccion.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_desproteccion_index',
            ]
        );
    }

    #[Route(path: '/historial-tutor', methods: ['GET'], name: 'historial_tutor_index')]
    public function listHistorialTutor(Request $request, TutorManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desproteccion_index');
        $numero = $request->query->get('numeroDocumento');
        $tutors = $manager->repository()->findBy(['numeroDocumento' => $numero]);

        $historialData = [];
        $persona = null;
        /** @var Tutor[] $tutors */
        foreach ($tutors as $item) {
            $persona = $item->getPersona();
            if (\count($item->getCasoDesproteccionTutors()) > 0) {
                foreach ($item->getCasoDesproteccionTutors() as $caso) {
                    $historialData[] = $caso->getCasoDesproteccion();
                }
            }
        }

        return $this->render(
            'historial/show_historial_desproteccion.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_desproteccion_index',
            ]
        );
    }

    #[Route(path: '/historialviolencia', name: 'historial_violencia_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/historialviolencia/page/{page<[1-9]\d*>}', name: 'historial_violencia_index_paginated', methods: ['GET'])]
    public function listHistorialViolencia(Request $request, int $page, PersonaManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'historial_violencia_index');

        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', $user->getCentroPoblado()->getId());
        }

        $paginator = $manager->listHistorialViolencia($request->query->all(), $page, $user);

        return $this->render(
            'historial/historial_casos_violencia.html.twig',
            [
                'paginator' => $paginator,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
            ]
        );
    }

    #[Route(path: '/historialdesproteccion', name: 'historial_desproteccion_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/historialdesproteccion/page/{page<[1-9]\d*>}', name: 'historial_desproteccion_index_paginated', methods: ['GET'])]
    public function listHistorialDesproteccion(Request $request, int $page, PersonaManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desproteccion_index');

        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        /** @var Usuario $user */
        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', $user->getCentroPoblado()?->getId());
        }

        $paginator = $manager->listHistorialDesproteccion($request->query->all(), $page, $user);

        return $this->render(
            'historial/historial_caso_desproteccion.html.twig',
            [
                'paginator' => $paginator,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
            ]
        );
    }

    #[Route(path: '/historial-total-violencia', methods: ['GET'], name: 'historial_totalviolencia_index')]
    public function listTotalViolencia(Request $request, PersonaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_violencia_index');
        $numero = $request->query->get('numeroDocumento');
        $personaid = $request->query->get('personaid');

        if (null !== $numero || '' !== $numero) {
            $persona = $manager->repository()->findOneBy(['numeroDocumento' => $numero]);
        } else {
            $persona = $manager->repository()->findOneBy(['persona' => $personaid]);
        }

        $historialData = [];
        /** @var Persona $persona */
        foreach ($persona->getAgresors() as $item) {
            if (\count($item->getCasoViolenciaAgresors()) > 0) {
                foreach ($item->getCasoViolenciaAgresors() as $casoViolenciaAgresor) {
                    $historialData[] = $casoViolenciaAgresor->getCasoViolencia();
                }
            }
        }

        foreach ($persona->getAgraviados() as $item) {
            if (\count($item->getCasoViolenciaAgraviados()) > 0) {
                foreach ($item->getCasoViolenciaAgraviados() as $caso) {
                    $historialData[] = $caso->getCasoViolencia();
                }
            }
        }

        foreach ($persona->getDenunciantes() as $item) {
            if (\count($item->getCasoViolenciaDenunciantes()) > 0) {
                foreach ($item->getCasoViolenciaDenunciantes() as $caso) {
                    $historialData[] = $caso->getCasoViolencia();
                }
            }
        }

        return $this->render(
            'historial/show_historial_violencia.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_violencia_index',
            ]
        );
    }

    #[Route(path: '/historial-total-desproteccion', methods: ['GET'], name: 'historial_totaldesproteccion_index')]
    public function listTotalDesproteccion(Request $request, PersonaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desproteccion_index');
        $numero = $request->query->get('numeroDocumento');
        $personaid = $request->query->get('personaid');
        if (null !== $numero || '' !== $numero) {
            $persona = $manager->repository()->findOneBy(['numeroDocumento' => $numero]);
        } else {
            $persona = $manager->repository()->findOneBy(['persona' => $personaid]);
        }

        $historialData = [];
        /** @var Persona $persona */
        foreach ($persona->getMenorEdads() as $item) {
            if (\count($item->getCasoDesproteccioMenorEdads()) > 0) {
                foreach ($item->getCasoDesproteccioMenorEdads() as $caso) {
                    $historialData[] = $caso->getCasoDesproteccion();
                }
            }
        }

        foreach ($persona->getTutors() as $item) {
            if (\count($item->getCasoDesproteccionTutors()) > 0) {
                foreach ($item->getCasoDesproteccionTutors() as $caso) {
                    $historialData[] = $caso->getCasoDesproteccion();
                }
            }
        }

        return $this->render(
            'historial/show_historial_desproteccion.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_desproteccion_index',
            ]
        );
    }

    #[Route(path: '/historial-tipomaltrato', name: 'historial_tipoviolencia_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/historial-tipomaltrato/page/{page<[1-9]\d*>}', name: 'historial_tipoviolencia_index_paginated', methods: ['GET'])]
    public function listHistorialTipoViolencia(Request $request, int $page, PersonaManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'historial_tipoviolencia_index');

        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        /** @var Usuario $user */
        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);
        $tipoPersona = 1;

        if (null === $request->query->get('tipoPersona')) {
            $request->query->set('tipoPersona', 1);
        } else {
            $tipoPersona = $request->query->get('tipoPersona');
        }
        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', $user->getCentroPoblado()?->getId());
        }

        $paginator = $manager->listHistorialTipoViolencia($request->query->all(), $page, $tipoPersona, $user);

        return $this->render(
            'historial/historial_tipo_violencia.html.twig',
            [
                'paginator' => $paginator,
                'tipoPersona' => $tipoPersona,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
            ]
        );
    }

    #[Route(path: '/mostrar-historial-tipo-maltrato', name: 'show_tipoviolencia_index', methods: ['GET'])]
    public function listShowTipoViolencia(Request $request, PersonaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_tipoviolencia_index');
        $numero = $request->query->get('nd');
        $tipoPersona = (int) $request->query->get('tp');
        $tipoViolencia = $request->query->get('tv');
        $personaid = $request->query->get('pid');
        $tipoViolenciaNombre = $request->query->get('tvn');
        $tipoPersonaNombre = 1 === $tipoPersona ? 'AGRESOR' : 'AGRAVIADO';

        /* @var Persona $persona */
        if (null !== $numero || '' !== $numero) {
            $persona = $manager->repository()->findOneBy(['numeroDocumento' => $numero]);
        } else {
            $persona = $manager->repository()->findOneBy(['persona' => $personaid]);
        }

        $historialData = [];
        if (1 === $tipoPersona) {
            foreach ($persona->getAgresors() as $item) {
                if (\count($item->getCasoViolenciaAgresors()) > 0) {
                    foreach ($item->getCasoViolenciaAgresors() as $caso) {
                        $historialData[] = $caso->getCasoViolencia();
                    }
                }
            }

        } else {
            foreach ($persona->getAgraviados() as $item) {
                if (\count($item->getCasoViolenciaAgraviados()) > 0) {
                    foreach ($item->getCasoViolenciaAgraviados() as $caso) {
                        $historialData[] = $caso->getCasoViolencia();
                    }
                }
            }
        }

        return $this->render(
            'historial/show_tipo_violencia.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'tipoPersonaNombre' => $tipoPersonaNombre,
                'tipoViolencia' => $tipoViolencia,
                'tipoViolenciaNombre' => $tipoViolenciaNombre,
                'section' => 'historial_tipoviolencia_index',
            ]
        );
    }

    #[Route(path: '/mostrartotal-historial-tipo-maltrato', name: 'showtotal_tipoviolencia_index', methods: ['GET'])]
    public function listTipoViolenciaTotal(Request $request, PersonaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_tipoviolencia_index');
        $numero = $request->query->get('nd');
        $tipoPersona = (int) $request->query->get('tp');
        $personaid = $request->query->get('pid');
        $tipoPersonaNombre = 1 === $tipoPersona ? 'AGRESOR' : 'AGRAVIADO';

        /* @var Persona $persona */
        if (null !== $numero || '' !== $numero) {
            $persona = $manager->repository()->findOneBy(['numeroDocumento' => $numero]);
        } else {
            $persona = $manager->repository()->findOneBy(['persona' => $personaid]);
        }

        $historialData = [];

        if (1 === $tipoPersona) {
            foreach ($persona->getAgresors() as $item) {
                if (\count($item->getCasoViolenciaAgresors()) > 0) {
                    foreach ($item->getCasoViolenciaAgresors() as $caso) {
                        $historialData[] = $caso->getCasoViolencia();
                    }
                }
            }
        } else {
            foreach ($persona->getAgraviados() as $item) {
                if (\count($item->getCasoViolenciaAgraviados()) > 0) {
                    foreach ($item->getCasoViolenciaAgraviados() as $caso) {
                        $historialData[] = $caso->getCasoViolencia();
                    }
                }
            }
        }

        return $this->render(
            'historial/showtotal_tipo_violencia.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'tipoPersonaNombre' => $tipoPersonaNombre,
                'section' => 'historial_tipoviolencia_index',
            ]
        );
    }

    #[Route(path: '/historialtrata', name: 'historial_trata_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/historialtrata/page/{page<[1-9]\d*>}', name: 'historial_trata_index_paginated', methods: ['GET'])]
    public function listHistorialTrata(Request $request, int $page, PersonaManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'historial_trata_index');

        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', $user->getCentroPoblado()->getId());
        }

        $paginator = $manager->listHistorialTrata($request->query->all(), $page, $user);

        return $this->render(
            'historial/historial_caso_trata.html.twig',
            [
                'paginator' => $paginator,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
            ]
        );
    }

    #[Route(path: '/historial-detenidos', methods: ['GET'], name: 'historial_detenido_index')]
    public function listHistorialDetenidos(Request $request, DetenidoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_trata_index');
        $numero = $request->query->get('numeroDocumento');
        $detenidos = $manager->repository()->findBy(['numeroDocumento' => $numero]);

        $historialData = [];
        $persona = null;
        /** @var Detenido[] $detenidos */
        foreach ($detenidos as $item) {
            $persona = $item->getPersona();
            $historialData[] = $item->getCasoTrata();
        }

        return $this->render(
            'historial/show_historial_trata.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_trata_index',
            ]
        );
    }

    #[Route(path: '/historial-victimas', methods: ['GET'], name: 'historial_victimas_index')]
    public function listHistorialVictimas(Request $request, VictimaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_trata_index');
        $numero = $request->query->get('numeroDocumento');
        $victimas = $manager->repository()->findBy(['numeroDocumento' => $numero]);

        $historialData = [];
        $persona = null;
        /** @var Victima[] $victimas */
        foreach ($victimas as $item) {
            $persona = $item->getPersona();
            $historialData[] = $item->getCasoTrata();
        }

        return $this->render(
            'historial/show_historial_trata.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_trata_index',
            ]
        );
    }

    #[Route(path: '/historialdesaparecido', name: 'historial_desaparecido_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/historialdesaparecido/page/{page<[1-9]\d*>}', name: 'historial_desaparecido_index_paginated', methods: ['GET'])]
    public function listHistorialDesaparecido(Request $request, int $page, PersonaManager $manager, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desaparecido_index');

        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        /** @var Usuario $user */
        $user = $this->getUser();
        $rteniente = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($rteniente, $user, $em);

        if (null === $request->query->get('centroPoblado') && false === $rteniente) {
            $request->query->set('centroPoblado', $user->getCentroPoblado()->getId());
        }
        $paginator = $manager->listHistorialDesaparecido($request->query->all(), $page, $user);

        return $this->render(
            'historial/historial_caso_desaparecido.html.twig',
            [
                'paginator' => $paginator,
                'provincias' => $provincias,
                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
            ]
        );
    }

    #[Route(path: '/historial-denunciante-des', methods: ['GET'], name: 'historial_denunciantedes_index')]
    public function listHistorialDenuncianteDes(Request $request, DenuncianteDesaparecidoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desaparecido_index');
        $numero = $request->query->get('numeroDocumento');
        $denunciantesDes = $manager->repository()->findBy(['numeroDocumento' => $numero]);

        $historialData = [];
        $persona = null;
        /** @var DenuncianteDesaparecido[] $denunciantesDes */
        foreach ($denunciantesDes as $item) {
            $persona = $item->getPersona();
            $historialData[] = $item->getCasoDesaparecido();
        }

        return $this->render(
            'historial/show_historial_desaparecido.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_desaparecido_index',
            ]
        );
    }

    #[Route(path: '/historial-desaparecidos', methods: ['GET'], name: 'historial_desaparecidos_index')]
    public function listHistorialDesaparecidos(Request $request, DesaparecidoManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desaparecido_index');
        $numero = $request->query->get('numeroDocumento');
        $desaparecidos = $manager->repository()->findBy(['numeroDocumento' => $numero]);

        $historialData = [];
        $persona = null;
        /** @var Desaparecido[] $desaparecidos */
        foreach ($desaparecidos as $item) {
            $persona = $item->getPersona();
            $historialData[] = $item->getCasoDesaparecido();
        }

        return $this->render(
            'historial/show_historial_desaparecido.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_desaparecido_index',
            ]
        );
    }

    #[Route(path: '/historial-total-trata', methods: ['GET'], name: 'historial_totaltrata_index')]
    public function listTotalTrata(Request $request, PersonaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_trata_index');
        $numero = $request->query->get('numeroDocumento');
        $personaid = $request->query->get('personaid');
        if (null !== $numero || '' !== $numero) {
            $persona = $manager->repository()->findOneBy(['numeroDocumento' => $numero]);
        } else {
            $persona = $manager->repository()->findOneBy(['persona' => $personaid]);
        }

        $historialData = [];
        /** @var Persona $persona */
        foreach ($persona->getDetenidos() as $item) {
            $historialData[] = $item->getCasoTrata();
        }

        foreach ($persona->getVictimas() as $item) {
            $historialData[] = $item->getCasoTrata();
        }

        return $this->render(
            'historial/show_historial_trata.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_trata_index',
            ]
        );
    }

    #[Route(path: '/historial-total-desaparecido', methods: ['GET'], name: 'historial_totaldesaparecido_index')]
    public function listTotalDesaparecido(Request $request, PersonaManager $manager): Response
    {
        $this->denyAccess(Security::LIST, 'historial_desaparecido_index');
        $numero = $request->query->get('numeroDocumento');
        $personaid = $request->query->get('personaid');
        if (null !== $numero || '' !== $numero) {
            $persona = $manager->repository()->findOneBy(['numeroDocumento' => $numero]);
        } else {
            $persona = $manager->repository()->findOneBy(['persona' => $personaid]);
        }

        $historialData = [];
        /** @var Persona $persona */
        foreach ($persona->getDenuncianteDesaparecidos() as $item) {
            $historialData[] = $item->getCasoDesaparecido();
        }

        foreach ($persona->getDesaparecidos() as $item) {
            $historialData[] = $item->getCasoDesaparecido();
        }

        return $this->render(
            'historial/show_historial_desaparecido.html.twig',
            [
                'persona' => $persona,
                'historialData' => $historialData,
                'section' => 'historial_desaparecido_index',
            ]
        );
    }
}
