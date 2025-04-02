<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\CasoDesproteccion;
use App\Entity\CasoDesproteccionMenorEdad;
use App\Entity\CasoDesproteccionTutor;
use App\Entity\MenorEdad;
use App\Entity\Tutor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CasoDesproteccionApiController extends ApiController
{
    use TraitParameter;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/pre-register/casodesproteccion', name: 'api_registeraudi2_data', methods: ['POST'])]
    public function preregisterCasoDesproteccion(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $item = $content['lista'];
            $usuario = $content['usuario'];
            $em = $this->entityManager;

            // empezamos registrando en la tabla casoDesproteccion
            $casod = new CasoDesproteccion();
            $centroPobladoid = $usuario['centro_poblado_id']; // $item["centro_poblado_id"] != "" ? $item["centro_poblado_id"] : $usuario["centro_poblado_id"];
            $distritoId = $usuario['distrito_id']; // $item["distrito_id"] != "" ? $item["distrito_id"] : $usuario["distrito_id"];

            $casod->setDistrito(self::findDistrito($distritoId, $em)->getNombre());
            $casod->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
            $casod->setDescripcionReporte($item['descripcion_reporte']);
            $casod->setFechaReporte(new \DateTime($item['fecha_reporte']));
            // $casod->setSituacionEncontrada(self::findSituacion($item["situacion_encontrada_id"], $em));
            $casod->setLugarCaso($item['lugar_reporte']);
            $casod->setUsuarioApp($usuario['username']);
            $casod->setCodigoApp($item['name_id_form']);
            $casod->setEstadoCaso('Pendiente');
			
			$casod->setLatitud($item['lat']);
            $casod->setLongitud($item['lon']);
            $casod->setCodigo(time());

            // registrando menor edad
            $cadenaGeneral = [];
            foreach ($item['sub-form-menoredad'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = self::findCentroPoblado($centroPobladoid, $em); // para registrar en persona
                $datos['casoDesproteccion'] = 1;
                $persona = self::findPerson($datos, $em);

                $menor = new MenorEdad();
                $menor->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $menor->setNombres($datos['nombres']);
                $menor->setApellidos($datos['apellidos']);
                $menor->setEdad($datos['edad']);
                $menor->setNumeroDocumento($datos['numero_documento']);
                $menor->setSexo(self::findSexo($datos['sexo'], $em));
                $menor->setDireccion($datos['direccion']);
                $menor->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                $menor->setCodigoApp($datos['name_sub_form']);
                $menor->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                $menor->setPersona($persona);

                $casomenor = new CasoDesproteccionMenorEdad();
                $listSituaciones = $datos['situacion_encontrada_id'];
                $cadena = '';
                foreach ($listSituaciones as $value) {
                    $cadena = $cadena.$value['label'].', ';
                    $cadenaGeneral[] = $value['label'];
                }
                $casomenor->setSituacionesEncontradas($cadena);
                $casomenor->setMenorEdad($menor);
                $casod->addCasoDesproteccioMenorEdad($casomenor);
            }

            foreach ($item['sub-form-tutor'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = self::findCentroPoblado($centroPobladoid, $em);
                $datos['casoDesproteccion'] = 1;
                $persona = self::findPerson($datos, $em);

                $tutor = new Tutor();
                $tutor->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $tutor->setNombres($datos['nombres']);
                $tutor->setApellidos($datos['apellidos']);
                $tutor->setNumeroDocumento($datos['numero_documento']);
                $tutor->setSexo(self::findSexo($datos['sexo'], $em));
                $tutor->setVinculoFamiliar(self::findVinculo($datos['vinculo_familiar_id'], $em));
                $tutor->setTelefono($datos['telefono']);
                $tutor->setCodigoApp($datos['name_sub_form']);
                $tutor->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                $tutor->setPersona($persona);

                $casotutor = new CasoDesproteccionTutor();
                $casotutor->setTutor($tutor);

                $casod->addCasoDesproteccionTutor($casotutor);
            }

            $casod->setSituacionesEncontradas(implode(',', array_unique($cadenaGeneral)));
            $this->entityManager->persist($casod);
            $this->entityManager->flush();

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            dd($ex);
            return $this->response(['data' => ''.$ex->getMessage()], false);
        }
    }

    #[Route(path: '/register/casodesproteccion', name: 'api_register2_data', methods: ['POST'])]
    public function registerCasoDesproteccion(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $dataCasoDesproteccion = $content['lista'];
            $dataSubForms = $content['listasubform'];
            $usuario = $content['usuario'];
            $em = $this->entityManager;

            // empezamos registrando en la tabla casoDesproteccion
            foreach ($dataCasoDesproteccion as $item) {
                $casod = self::findCasoDesproteccion($item['name_id_form'], $em);
                if (!$casod instanceof \App\Entity\CasoDesproteccion) {
                    $casod = new CasoDesproteccion();
                }

                $centroPobladoid = $usuario['centro_poblado_id'];
                $distritoId = $usuario['distrito_id'];

                $casod->setDistrito(self::findDistrito($distritoId, $em)->getNombre());
                $casod->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                $casod->setDescripcionReporte($item['descripcion_reporte']);
                $casod->setFechaReporte(new \DateTime($item['fecha_reporte']));
                // $casod->setSituacionEncontrada(self::findSituacion($item["situacion_encontrada_id"], $em));
                $casod->setLugarCaso($item['lugar_reporte']);
                $casod->setUsuarioApp($usuario['username']);
                $casod->setCodigoApp($item['name_id_form']);
                $casod->setEstadoCaso('Notificado');
				
				$casod->setLatitud($item['lat']);
				$casod->setLongitud($item['lon']);

                // registrando menor edad
                $cadenaGeneral = [];
                foreach ($dataSubForms as $itemsub) {
                    switch ($itemsub['name_subform']) {
                        case 'sub-form-menoredad':
                            foreach ($itemsub['datos'] as $dato) {
                                if ($dato['idformpadre'] === $item['name_id_form']) {
                                    $datos = $dato['data'];
                                    $datos['centroPoblado'] = self::findCentroPoblado($centroPobladoid, $em);
                                    $datos['casoDesproteccion'] = 1;

                                    $menor = self::findMenor($datos['name_sub_form'], $em);
                                    if (!$menor instanceof \App\Entity\MenorEdad) {
                                        $menor = new MenorEdad();
                                    }
                                    $persona = self::findPerson($datos, $em);
                                    $menor->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                    $menor->setNombres($datos['nombres']);
                                    $menor->setApellidos($datos['apellidos']);
                                    $menor->setEdad($datos['edad']);
                                    $menor->setNumeroDocumento($datos['numero_documento']);
                                    $menor->setSexo(self::findSexo($datos['sexo'], $em));
                                    $menor->setDireccion($datos['direccion']);
                                    $menor->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                                    $menor->setCodigoApp($datos['name_sub_form']);
                                    $menor->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));

                                    $persona->setCasoDesproteccionTotal($persona->getCasoDesproteccionTotal() + 1);
                                    $menor->setPersona($persona);

                                    $listSituaciones = $datos['situacion_encontrada_id'];
                                    $cadena = '';
                                    foreach ($listSituaciones as $value) {
                                        $cadena = $cadena.$value['label'].', ';
                                        $cadenaGeneral[] = $value['label'];
                                    }

                                    if (0 === \count($menor->getCasoDesproteccioMenorEdads())) {
                                        $casomenor = new CasoDesproteccionMenorEdad();
                                        $casomenor->setSituacionesEncontradas($cadena);
                                        $casomenor->setMenorEdad($menor);
                                        $casod->addCasoDesproteccioMenorEdad($casomenor);
                                    }
                                }
                            }

                            break;
                        case 'sub-form-tutor':
                            foreach ($itemsub['datos'] as $dato) {
                                if ($dato['idformpadre'] === $item['name_id_form']) {
                                    $datos = $dato['data'];
                                    $datos['centroPoblado'] = self::findCentroPoblado($centroPobladoid, $em);
                                    $datos['casoDesproteccion'] = 1;

                                    $tutor = self::findTutor($datos['name_sub_form'], $em);
                                    if (!$tutor instanceof \App\Entity\Tutor) {
                                        $tutor = new Tutor();
                                    }
                                    $persona = self::findPerson($datos, $em);
                                    $tutor->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                    $tutor->setNombres($datos['nombres']);
                                    $tutor->setApellidos($datos['apellidos']);
                                    $tutor->setNumeroDocumento($datos['numero_documento']);
                                    $tutor->setSexo(self::findSexo($datos['sexo'], $em));
                                    $tutor->setVinculoFamiliar(self::findVinculo($datos['vinculo_familiar_id'], $em));
                                    $tutor->setTelefono($datos['telefono']);
                                    $tutor->setCodigoApp($datos['name_sub_form']);
                                    $tutor->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));

                                    $persona->setCasoDesproteccionTotal($persona->getCasoDesproteccionTotal() + 1);
                                    $tutor->setPersona($persona);

                                    if (0 === \count($tutor->getCasoDesproteccionTutors())) {
                                        $casotutor = new CasoDesproteccionTutor();
                                        $casotutor->setTutor($tutor);
                                        $casod->addCasoDesproteccionTutor($casotutor);
                                    }
                                }
                            }
                            break;
                    }
                }
                $casod->setSituacionesEncontradas(implode(',', array_unique($cadenaGeneral)));
                $this->entityManager->persist($casod);
                $this->entityManager->flush();
            }

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            return $this->response(['data' => ''.$ex->getMessage()], false);
        }
    }
}
