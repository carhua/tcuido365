<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Manager\CasoDesaparecidoManager;
use App\Manager\CasoDesproteccionManager;
use App\Manager\CasoTrataManager;
use App\Manager\CasoViolenciaManager;
use App\Manager\DenuncianteManager;
use App\Manager\PersonaManager;
use App\Security\Security;
use App\Traits\TraitExcelFechas;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class ExcelController extends BaseController
{
    use TraitExcelFechas;
    use TraitUser;

    #[Route(path: '/excelCasosViolencia', name: 'excel_casos_violencia', methods: 'GET|POST')]
    public function exportaCasos(Request $request, CasoViolenciaManager $manager, EntityManagerInterface $em)
    {
        // $this->denyAccess(Security::EXPORT, 'caso_violencia_index');
        try {
            $fechaInicio = $request->query->get('finicial');
            $fechaFinal = $request->query->get('ffinal');
            $distritoId = $request->query->get('distrito');
            $provinciaId = $request->query->get('provincia');
            $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
            $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);

            $user = $this->getUser();
            $rteniente = self::validarRoles($user->getRoles());

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

            $data = $manager->listExcel($request->query->all(), $user);
            $fileNameTemp = self::casosViolencia($data, $fechaInicio, $fechaFinal);

            return $this->file($fileNameTemp, 'CasosViolenciaReporte.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/excelCasosDesproteccion', name: 'excel_casos_desproteccion', methods: 'GET|POST')]
    public function exportaCasosDes(Request $request, CasoDesproteccionManager $manager, EntityManagerInterface $em)
    {
        // $this->denyAccess(Security::EXPORT, 'caso_violencia_index');
        try {
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
                $request->query->set('estado', 'Notificado');
            }

            $data = $manager->listExcel($request->query->all(), $user);
            $fileNameTemp = self::casosDesproteccion($data, $fechaInicio, $fechaFinal);

            return $this->file($fileNameTemp, 'CasosDesproteccionReporte.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/excelCasosTrata', name: 'excel_casos_trata', methods: 'GET|POST')]
    public function exportaCasosTrata(Request $request, CasoTrataManager $manager, EntityManagerInterface $em)
    {
        // $this->denyAccess(Security::EXPORT, 'caso_violencia_index');
        try {
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

            $data = $manager->listExcel($request->query->all(), $user);

            $fileNameTemp = self::casosTrata($data, $fechaInicio, $fechaFinal);

            return $this->file($fileNameTemp, 'CasosTrataReporte.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/excelCasosDesaparicion', name: 'excel_casos_desaparecido', methods: 'GET|POST')]
    public function exportaCasosDesaparicion(Request $request, CasoDesaparecidoManager $manager, EntityManagerInterface $em)
    {
        // $this->denyAccess(Security::EXPORT, 'caso_violencia_index');
        try {
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
            if (null === $request->query->get('centroPoblado') && false === $rteniente) {
                $request->query->set('centroPoblado', 182);
            }

            if (null !== $oprovincia) {
                $request->query->set('oprovincia', $oprovincia);
            }

            if (null !== $odistrito) {
                $request->query->set('odistrito', $odistrito);
            }

            if (null === $request->query->get('estado')) {
                $request->query->set('estado', 'Notificado');
            }

            $data = $manager->listExcel($request->query->all(), $user);

            $fileNameTemp = self::casosDesaparecido($data, $fechaInicio, $fechaFinal);

            return $this->file($fileNameTemp, 'CasosDesaparecidosReporte.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/excelDenunciantes', name: 'excel_denunciantes', methods: 'GET|POST')]
    public function exportarDenunciantes(Request $request, DenuncianteManager $manager, EntityManagerInterface $em)
    {
        // $this->denyAccess(Security::EXPORT, 'caso_violencia_index');
        try {
            $user = $this->getUser();
            $rt = self::validarRoles($user->getRoles());

            if (null === $request->query->get('centroPoblado') && false === $rt) {
                $request->query->set('centroPoblado', 182);
            } else {
                $id = $request->query->get('centroPoblado');
                $centro = self::findCentro($id, $em);
            }

            $data = $manager->listExcel($request->query->all());

            $fileNameTemp = self::listDenunciante($data, $centro);

            return $this->file($fileNameTemp, 'DenuncianteReporte.xlsx', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/historial-excel-violencia', methods: ['GET'], name: 'historialexcel_violencia_index')]
    public function listHistorialViolencia(Request $request, PersonaManager $manager)
    {
        try {
            $this->denyAccess(Security::LIST, 'historialv_index');

            $user = $this->getUser();
            $rteniente = self::validarRoles($user->getRoles());

            if (null === $request->query->get('centroPoblado') && false === $rteniente) {
                $request->query->set('centroPoblado', 182);
            }

            $data = $manager->listHistorialViolenciaExcel($request->query->all(), $user);
            $fileNameTemp = self::historialCasosViolencia($data);

            return $this->file($fileNameTemp, 'HistorialCasosViolencia.xlsx',
                ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/historial-excel-desproteccion', methods: ['GET'], name: 'historialexcel_desproteccion_index')]
    public function listHistorialDesproteccion(Request $request, PersonaManager $manager)
    {
        try {
            $this->denyAccess(Security::LIST, 'historiald_index');

            $user = $this->getUser();
            $rteniente = self::validarRoles($user->getRoles());

            if (null === $request->query->get('centroPoblado') && false === $rteniente) {
                $request->query->set('centroPoblado', 182);
            }

            $data = $manager->listHistorialDesproteccionExcel($request->query->all(), $user);
            $fileNameTemp = self::historialCasosDesproteccion($data);

            return $this->file($fileNameTemp, 'HistorialCasosDesproteccion.xlsx',
                ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/historial-tipomaltrato-excel', methods: ['GET'], name: 'historialexcel_tipoviolencia_index')]
    public function listHistorialTipoViolencia(Request $request, PersonaManager $manager, EntityManagerInterface $em)
    {
        try {
            $this->denyAccess(Security::LIST, 'historial_tipoviolencia_index');
            $user = $this->getUser();
            $rt = self::validarRoles($user->getRoles());
            $tipoPersona = 1;
            $centro = 'TODOS';

            if (null === $request->query->get('tipoPersona')) {
                $request->query->set('tipoPersona', 1);
            } else {
                $tipoPersona = $request->query->get('tipoPersona');
            }
            if (null === $request->query->get('centroPoblado') && false === $rt) {
                $request->query->set('centroPoblado', 182);
            } else {
                $cp = $em->getRepository(CentroPoblado::class)->findOneBy(['id' => $request->query->get('centroPoblado')]);
                $centro = $cp->getNombre();
            }

            $data = $manager->listHistorialTipoViolenciaExcel($request->query->all(), $tipoPersona, $user);
            $fileNameTemp = self::historialTipoViolencia($data, $tipoPersona, $centro);

            return $this->file($fileNameTemp, "HistorialTipoViolencia'.xlsx",
                ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/historial-excel-trata', methods: ['GET'], name: 'historialexcel_trata_index')]
    public function listHistorialTrata(Request $request, PersonaManager $manager)
    {
        try {
            $this->denyAccess(Security::LIST, 'historial_trata_index');
            $user = $this->getUser();
            $rt = self::validarRoles($user->getRoles());

            if (null === $request->query->get('centroPoblado') && false === $rt) {
                $request->query->set('centroPoblado', 182);
            }

            $data = $manager->listHistorialTrataExcel($request->query->all(), $user);
            $fileNameTemp = self::historialCasosTrata($data);

            return $this->file($fileNameTemp, 'HistorialTrataPersonas.xlsx',
                ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    #[Route(path: '/historial-excel-desaparecido', methods: ['GET'], name: 'historialexcel_desaparecido_index')]
    public function listHistorialDesaparecido(Request $request, PersonaManager $manager, EntityManagerInterface $em)
    {
        try {
            $this->denyAccess(Security::LIST, 'historial_desaparecido_index');
            $user = $this->getUser();
            $rt = self::validarRoles($user->getRoles());

            if (null === $request->query->get('centroPoblado') && false === $rt) {
                $request->query->set('centroPoblado', 182);
            }

            $data = $manager->listHistorialDesaparecidoExcel($request->query->all(), $user);
            $fileNameTemp = self::historialCasosDesaparecidos($data);

            return $this->file($fileNameTemp, 'HistorialCasosDesaparecidos.xlsx',
                ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
