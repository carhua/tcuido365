<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\CasoTrata;
use App\Entity\Detenido;
use App\Entity\Victima;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CasoTrataApiController extends ApiController
{
    use TraitParameter;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Pre-registra un caso de trata de personas con estado 'Pendiente'.
     * Guarda los datos iniciales del caso, incluyendo detenidos y víctimas.
     * @param Request $request La solicitud HTTP con los datos del caso en formato JSON.
     * @return Response Una respuesta JSON indicando el resultado de la operación.
     */
    #[Route(path: '/pre-register/casotrata', name: 'api_preregistertrata_data', methods: ['POST'])]
    public function preregisterCasoTrata(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $item = $content['lista'];
            $usuario = $content['usuario'];
            $em = $this->entityManager;

            // empezamos registrando en la tabla casoTrata
            $casod = new CasoTrata();
            $centroPobladoid = $usuario['centro_poblado_id'];
            $distritoId = $usuario['distrito_id'];

            $casod->setDistrito(self::findDistrito($distritoId, $em)->getNombre());
            $casod->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
            $casod->setDescripcionReporte($item['descripcion_reporte']);
            $casod->setFechaReporte(new \DateTime($item['fecha_reporte']));
            $casod->setUsuarioApp($usuario['username']);
            $casod->setCodigoApp($item['name_id_form']);
            $casod->setEstadoCaso('Pendiente');
			
			$casod->setLatitud($item['lat']);
            $casod->setLongitud($item['lon']);
            $casod->setCodigo(time());

            // registrando detenido
            $centroPobladoPersona = self::findCentroPoblado($centroPobladoid, $em);

            foreach ($item['sub-form-detenido'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = $centroPobladoPersona; // para registrar en persona
                $datos['casoTrata'] = 1;
                $persona = self::findPerson($datos, $em);

                $det = new Detenido();
                $det->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $det->setNombres($datos['nombres']);
                $det->setApellidos($datos['apellidos']);
                $det->setEdad($datos['edad']);
                $det->setNumeroDocumento($datos['numero_documento']);
                $det->setEdad($datos['edad']);
                $det->setSexo(self::findSexo($datos['sexo'], $em));
                $det->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                $det->setFormaCaptacion(self::findFormaCaptacion($datos['forma_captacion_id'], $em));
                $det->setCodigoApp($datos['name_sub_form']);
                $det->setPersona($persona);
                $casod->addDetenido($det);
            }

            // registrando victima
            $cadenaGeneral = [];
            foreach ($item['sub-form-victima'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = $centroPobladoPersona;
                $datos['casoTrata'] = 1;
                $persona = self::findPerson($datos, $em);

                $vic = new Victima();
                $vic->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $vic->setNombres($datos['nombres']);
                $vic->setApellidos($datos['apellidos']);
                $vic->setNumeroDocumento($datos['numero_documento']);
                $vic->setEdad($datos['edad']);
                $vic->setSexo(self::findSexo($datos['sexo'], $em));
                $vic->setCodigoApp($datos['name_sub_form']);
                $vic->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                $vic->setPersona($persona);

                $listExplotacion = $datos['tipo_explotacion_id'];
                $cadena = '';
                foreach ($listExplotacion as $value) {
                    $cadena = $cadena.$value['label'].', ';
                    $cadenaGeneral[] = $value['label'];
                }

                $vic->setTipoExplotaciones($cadena);
                $vic->setLugarFormaRescate($datos['lugar_forma_rescate']);
                $casod->addVictima($vic);
            }

            $casod->setTipoExplotacionesGeneral(implode(',', array_unique($cadenaGeneral)));
            $this->entityManager->persist($casod);
            $this->entityManager->flush();

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            return $this->response(['data' => ''.$ex->getMessage()], false);
        }
    }

    /**
     * Registra o actualiza un caso de trata de personas, marcándolo como 'Notificado'.
     * Procesa los datos completos del caso, incluyendo sub-formularios de detenidos y víctimas.
     * @param Request $request La solicitud HTTP con los datos del caso y sub-formularios en formato JSON.
     * @return Response Una respuesta JSON indicando el resultado de la operación.
     */
    #[Route(path: '/register/casotrata', name: 'api_registertrata_data', methods: ['POST'])]
    public function registerCasoTrata(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $dataCasoDesproteccion = $content['lista'];
            $dataSubForms = $content['listasubform'];
            $usuario = $content['usuario'];
            $em = $this->entityManager;

            // empezamos registrando en la tabla casoDesproteccion
            foreach ($dataCasoDesproteccion as $item) {
                $casod = self::findCasoTrata($item['name_id_form'], $em);
                if (!$casod instanceof \App\Entity\CasoTrata) {
                    $casod = new CasoTrata();
                }

                $centroPobladoid = $usuario['centro_poblado_id'];
                $distritoId = $usuario['distrito_id'];

                $casod->setDistrito(self::findDistrito($distritoId, $em)->getNombre());
                $casod->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                $casod->setDescripcionReporte($item['descripcion_reporte']);
                $casod->setFechaReporte(new \DateTime($item['fecha_reporte']));
                $casod->setUsuarioApp($usuario['username']);
                $casod->setCodigoApp($item['name_id_form']);
                $casod->setEstadoCaso('Notificado');
				
				$casod->setLatitud($item['lat']);
				$casod->setLongitud($item['lon']);

                $centroPobladoPersona = self::findCentroPoblado($centroPobladoid, $em);
                // registrando detenido
                $cadenaGeneral = [];
                foreach ($dataSubForms as $itemsub) {
                    switch ($itemsub['name_subform']) {
                        case 'sub-form-detenido':
                            foreach ($itemsub['datos'] as $dato) {
                                if ($dato['idformpadre'] === $item['name_id_form']) {
                                    $datos = $dato['data'];
                                    $datos['centroPoblado'] = $centroPobladoPersona;
                                    $datos['casoTrata'] = 1;

                                    $det = self::findDetenido($datos['name_sub_form'], $em);
                                    if (!$det instanceof \App\Entity\Detenido) {
                                        $det = new Detenido();
                                    }

                                    $det->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                    $det->setNombres($datos['nombres']);
                                    $det->setApellidos($datos['apellidos']);
                                    $det->setEdad($datos['edad']);
                                    $det->setNumeroDocumento($datos['numero_documento']);
                                    $det->setSexo(self::findSexo($datos['sexo'], $em));
                                    $det->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                                    $det->setCodigoApp($datos['name_sub_form']);
                                    $det->setFormaCaptacion(self::findFormaCaptacion($datos['forma_captacion_id'], $em));
                                    $persona = self::findPerson($datos, $em);
                                    $persona->setCasoTrataTotal($persona->getCasoTrataTotal() + 1);
                                    $det->setPersona($persona);
                                    $casod->addDetenido($det);
                                }
                            }

                            break;
                        case 'sub-form-victima':
                            foreach ($itemsub['datos'] as $dato) {
                                if ($dato['idformpadre'] === $item['name_id_form']) {
                                    $datos = $dato['data'];
                                    $datos['centroPoblado'] = $centroPobladoPersona;
                                    $datos['casoTrata'] = 1;

                                    $vic = self::findVictima($datos['name_sub_form'], $em);
                                    if (!$vic instanceof \App\Entity\Victima) {
                                        $vic = new Victima();
                                    }

                                    $vic->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                    $vic->setNombres($datos['nombres']);
                                    $vic->setApellidos($datos['apellidos']);
                                    $vic->setNumeroDocumento($datos['numero_documento']);
                                    $vic->setSexo(self::findSexo($datos['sexo'], $em));
                                    $vic->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                                    $vic->setCodigoApp($datos['name_sub_form']);

                                    $persona = self::findPerson($datos, $em);
                                    $persona->setCasoTrataTotal($persona->getCasoTrataTotal() + 1);
                                    $vic->setPersona($persona);

                                    $listExplotacion = $datos['tipo_explotacion_id'];
                                    $cadena = '';
                                    foreach ($listExplotacion as $value) {
                                        $cadena = $cadena.$value['label'].', ';
                                        $cadenaGeneral[] = $value['label'];
                                    }
                                    $vic->setTipoExplotaciones($cadena);
                                    $vic->setLugarFormaRescate($datos['lugar_forma_rescate']);
                                    $casod->addVictima($vic);
                                }
                            }
                            break;
                    }
                }

                $casod->setTipoExplotacionesGeneral(implode(',', array_unique($cadenaGeneral)));
                $this->entityManager->persist($casod);
                $this->entityManager->flush();
            }

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            return $this->response(['data' => $ex->getMessage()], false);
        }
    }
}
