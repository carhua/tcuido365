<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\CasoDesaparecido;
use App\Entity\DenuncianteDesaparecido;
use App\Entity\Desaparecido;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CasoDesaparecidoApiController extends ApiController
{
    use TraitParameter;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Pre-registra un caso de persona desaparecida con estado 'Pendiente'.
     * Este endpoint guarda los datos iniciales del caso enviados desde la aplicación móvil.
     * @param Request $request La solicitud HTTP con los datos del caso en formato JSON.
     * @return Response Una respuesta JSON indicando el resultado de la operación.
     */
    #[Route(path: '/pre-register/casodesaparecido', name: 'api_preregisterdesaparecido_data', methods: ['POST'])]
    public function preregisterCasoTrata(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $item = $content['lista'];
            $usuario = $content['usuario'];
            $em = $this->entityManager;

            // empezamos registrando en la tabla casoTrata
            $casod = new CasoDesaparecido();
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

            foreach ($item['sub-form-denunciante-des'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = $centroPobladoPersona; // para registrar en persona
                $datos['casoDesaparecido'] = 1;
                $persona = self::findPerson($datos, $em);

                $det = new DenuncianteDesaparecido();
                $det->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $det->setNombres($datos['nombres']);
                $det->setApellidos($datos['apellidos']);
                $det->setEdad(isset($datos['edad']) ? (int) $datos['edad'] : null);
                $det->setNumeroDocumento($datos['numero_documento']);
                $det->setSexo(self::findSexo($datos['sexo'], $em));
                $det->setCodigoApp($datos['name_sub_form']);
                $det->setPersona($persona);
                $casod->addDenuncianteDesaparecido($det);
            }

            // registrando victima
            foreach ($item['sub-form-desaparecido'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = $centroPobladoPersona;
                $datos['casoDesaparecido'] = 1;
                $persona = self::findPerson($datos, $em);

                $des = new Desaparecido();
                $des->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $des->setNombres($datos['nombres']);
                $des->setApellidos($datos['apellidos']);
                $des->setNumeroDocumento($datos['numero_documento']);
                $des->setEdad(isset($datos['edad']) ? (int) $datos['edad'] : null);
                $des->setSexo(self::findSexo($datos['sexo'], $em));
                $des->setCodigoApp($datos['name_sub_form']);
                $des->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                $des->setLugarDesaparicion($datos['lugar_desaparicion']);
                $des->setDiscapacidad($datos['discapacidad']);
                $des->setDireccionDesaparicion($datos['direccion_desaparicion']);
                $des->setPersona($persona);
                $casod->addDesaparecido($des);
            }

            $this->entityManager->persist($casod);
            $this->entityManager->flush();

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            return $this->response(['data' => $ex->getMessage()], false);
        }
    }

    /**
     * Registra o actualiza un caso de persona desaparecida, marcándolo como 'Notificado'.
     * Este endpoint procesa los datos completos del caso, incluyendo sub-formularios, para finalizar el registro.
     * @param Request $request La solicitud HTTP con los datos del caso y sub-formularios en formato JSON.
     * @return Response Una respuesta JSON indicando el resultado de la operación.
     */
    #[Route(path: '/register/casodesaparecido', name: 'api_registerdesaparecido_data', methods: ['POST'])]
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
                $casod = self::findCasoDesaparecido($item['name_id_form'], $em);
                if (!$casod instanceof CasoDesaparecido) {
                    $casod = new CasoDesaparecido();
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
                foreach ($dataSubForms as $itemsub) {
                    switch ($itemsub['name_subform']) {
                        case 'sub-form-denunciante-des':
                            foreach ($itemsub['datos'] as $dato) {
                                if ($dato['idformpadre'] === $item['name_id_form']) {
                                    $datos = $dato['data'];
                                    $datos['centroPoblado'] = $centroPobladoPersona;
                                    $datos['casoDesaparecido'] = 1;

                                    $det = self::findDenuncianteDes($datos['name_sub_form'], $em);
                                    if (!$det instanceof DenuncianteDesaparecido) {
                                        $det = new DenuncianteDesaparecido();
                                    }
                                    $det->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                    $det->setNombres($datos['nombres']);
                                    $det->setApellidos($datos['apellidos']);
                                    $det->setEdad(isset($datos['edad']) ? (int) $datos['edad'] : null);
                                    $det->setNumeroDocumento($datos['numero_documento']);
                                    $det->setSexo(self::findSexo($datos['sexo'], $em));
                                    $det->setCodigoApp($datos['name_sub_form']);
                                    $persona = self::findPerson($datos, $em);
                                    $persona->setCasoDesaparecidoTotal($persona->getCasoDesaparecidoTotal() + 1);
                                    $det->setPersona($persona);
                                    $casod->addDenuncianteDesaparecido($det);
                                }
                            }

                            break;
                        case 'sub-form-desaparecido':
                            foreach ($itemsub['datos'] as $dato) {
                                if ($dato['idformpadre'] === $item['name_id_form']) {
                                    $datos = $dato['data'];
                                    $datos['centroPoblado'] = $centroPobladoPersona;
                                    $datos['casoDesaparecido'] = 1;

                                    $des = self::findDesaparecido($datos['name_sub_form'], $em);
                                    if (!$des instanceof Desaparecido) {
                                        $des = new Desaparecido();
                                    }

                                    $des->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                    $des->setNombres($datos['nombres']);
                                    $des->setApellidos($datos['apellidos']);
                                    $des->setNumeroDocumento($datos['numero_documento']);
                                    $des->setEdad(isset($datos['edad']) ? (int) $datos['edad'] : null);
                                    $des->setSexo(self::findSexo($datos['sexo'], $em));
                                    $des->setCodigoApp($datos['name_sub_form']);
                                    $des->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                                    $des->setLugarDesaparicion($datos['lugar_desaparicion']);
                                    $des->setDiscapacidad($datos['discapacidad']);
                                    $des->setDireccionDesaparicion($datos['direccion_desaparicion']);

                                    $persona = self::findPerson($datos, $em);
                                    $persona->setCasoDesaparecidoTotal($persona->getCasoDesaparecidoTotal() + 1);
                                    $des->setPersona($persona);
                                    $casod->addDesaparecido($des);
                                }
                            }
                            break;
                    }
                }

                $this->entityManager->persist($casod);
                $this->entityManager->flush();
            }

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            return $this->response(['data' => $ex->getMessage()], false);
        }
    }
}
