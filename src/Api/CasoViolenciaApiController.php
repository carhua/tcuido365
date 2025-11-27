<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\Agraviado;
use App\Entity\Agresor;
use App\Entity\CasoViolencia;
use App\Entity\CasoViolenciaAgraviado;
use App\Entity\CasoViolenciaAgresor;
use App\Entity\CasoViolenciaDenunciante;
use App\Entity\Denunciante;
use App\Entity\MenorEdad;
use App\Entity\Tutor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CasoViolenciaApiController extends ApiController
{
    use TraitParameter;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Pre-registra un caso de violencia con estado 'Pendiente'.
     * Este endpoint guarda los datos iniciales del caso, incluyendo denunciantes, agraviados y agresores.
     * @param Request $request La solicitud HTTP con los datos del caso en formato JSON.
     * @return Response Una respuesta JSON indicando el resultado de la operación.
     */
    #[Route(path: '/pre-register/casoviolencia', name: 'api_registerauditoria_data', methods: ['POST'])]
    public function preRegisterCasoViolencia(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $item = $content['lista'];
            $usuario = $content['usuario'];
            $em = $this->entityManager;

            //   foreach ($dataCasoViolencia as $item) {
            $casod = new CasoViolencia();
            $centroPobladoid = $usuario['centro_poblado_id'];
            $distritoId = $usuario['distrito_id'];

            $casod->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
            // $casod->setTipoMaltrato(self::findTipoMaltrato($item["tipo_maltrato_id"], $em));
            $casod->setDescripcionReporte($item['descripcion_denuncia']);
            $casod->setFechaReporte(new \DateTime($item['fecha_denuncia']));
            $casod->setLugarMaltrato($item['lugar_maltrato']);
            $casod->setDistrito(self::findDistrito($distritoId, $em)->getNombre());
            $casod->setUsuarioApp($usuario['username']);
            $casod->setCodigoApp($item['name_id_form']);
            $casod->setEstadoCaso('Pendiente');
			
			$casod->setLatitud($item['lat']);
            $casod->setLongitud($item['lon']);
            $casod->setCodigo(time());

            // registrando denunciante
            $centroPoblado = self::findCentroPoblado($centroPobladoid, $em);
            foreach ($item['sub-form-denunciante'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = $centroPoblado;
                $datos['casoViolencia'] = 1;
                $den = new Denunciante();
                $den->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $den->setNombres($datos['nombres']);
                $den->setApellidos($datos['apellidos']);
                $den->setEdad($datos['edad'] ?: 0);
                $den->setNumeroDocumento($datos['numero_documento']);
                $den->setSexo(self::findSexo($datos['sexo'], $em));
                $den->setTelefono($datos['telefono']);
                $den->setEmail($datos['email']);
                $den->setCodigoApp($datos['name_sub_form']);
                $den->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                if ('' === $datos['nombres']) {
                    $datos['numero_documento'] = '00000000';
                    $datos['nombres'] = 'Anonimo';
                    $datos['apellidos'] = 'Anonimo';
                    $persona = self::findPerson($datos, $em);
                } else {
                    $persona = self::findPerson($datos, $em);
                }
                $den->setPersona($persona);

                $casoden = new CasoViolenciaDenunciante();
                $casoden->setDenunciante($den);
                $casod->addCasoViolenciaDenunciante($casoden);
            }

            $cadenaGeneral = [];
            foreach ($item['sub-form-agraviado'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = $centroPoblado;
                $datos['casoViolencia'] = 1;
                $persona = self::findPerson($datos, $em);
                $den = new Agraviado();
                $den->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $den->setNombres($datos['nombres']);
                $den->setApellidos($datos['apellidos']);
                $den->setEdad($datos['edad']);
                $den->setNumeroDocumento($datos['numero_documento']);
                $den->setSexo(self::findSexo($datos['sexo'], $em));
                $den->setTelefono($datos['telefono']);
                $den->setEmail($datos['email']);
                $den->setEstadoCivil(self::findEstadoCivil($datos['estado_civil_id'], $em));
                $den->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                $den->setGestacion($datos['gestacion']);
                $den->setDiscapacidad($datos['discapacidad']);
                $den->setVinculoFamiliar(self::findVinculo($datos['vinculo_familiar_id'], $em));
                $den->setDireccion($datos['direccion']);
                $den->setReferenciaDomicilio($datos['referencia_domicilio']);
                $den->setCodigoApp($datos['name_sub_form']);
                $den->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                $den->setPersona($persona);

                $casoden = new CasoViolenciaAgraviado();
                $listMaltratos = $datos['tipo_maltrato_id'];
                $cadena = '';
                foreach ($listMaltratos as $value) {
                    $cadena = $cadena.$value['label'].', ';
                    $cadenaGeneral[] = $value['label'];
                }
                $casoden->setTipoMaltratos($cadena);
                $casoden->setAgraviado($den);
                $casod->addCasoViolenciaAgraviado($casoden);
            }

            foreach ($item['sub-form-agresor'] as $dato) {
                $datos = $dato;
                $datos['centroPoblado'] = $centroPoblado;
                $datos['casoViolencia'] = 1;
                $persona = self::findPerson($datos, $em);
                $den = new Agresor();
                $den->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                $den->setNombres($datos['nombres']);
                $den->setApellidos($datos['apellidos']);
                $den->setEdad($datos['edad']);
                $den->setNumeroDocumento($datos['numero_documento']);
                $den->setSexo(self::findSexo($datos['sexo'], $em));
                $den->setTelefono($datos['telefono']);
                $den->setEmail($datos['email']);
                $den->setVinculoFamiliar(self::findVinculo($datos['vinculo_familiar_id'], $em));
                $den->setEstadoCivil(self::findEstadoCivil($datos['estado_civil_id'], $em));
                $den->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                $den->setDireccion($datos['direccion']);
                $den->setCodigoApp($datos['name_sub_form']);
                $den->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                $den->setPersona($persona);

                $casoden = new CasoViolenciaAgresor();
                $casoden->setAgresor($den);
                $casod->addCasoViolenciaAgresor($casoden);
            }

            //    }
            $casod->setTipoMaltratos(implode(',', array_unique($cadenaGeneral)));
            $this->entityManager->persist($casod);
            $this->entityManager->flush();

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            return $this->response(['data' => $ex->getMessage()], false);
        }
    }

    /**
     * Registra o actualiza un caso de violencia, marcándolo como 'Notificado'.
     * Procesa los datos completos del caso, incluyendo sub-formularios, para finalizar el registro.
     * @param Request $request La solicitud HTTP con los datos del caso y sub-formularios en formato JSON.
     * @return Response Una respuesta JSON indicando el resultado de la operación.
     */
    #[Route(path: '/register/casoviolencia', name: 'api_register_data', methods: ['POST'])]
    public function registerCasoViolencia(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $dataCasoViolencia = $content['lista'];
            $dataSubForms = $content['listasubform'];
            $usuario = $content['usuario'];
            $em = $this->entityManager;

            // empezamos registrando en la tabla casoDesproteccion
            foreach ($dataCasoViolencia as $item) {
                $casod = self::findCasoViolencia($item['name_id_form'], $em);
                if (!$casod instanceof \App\Entity\CasoViolencia) {
                    $casod = new CasoViolencia();
                }

                $centroPobladoid = $usuario['centro_poblado_id'];
                $distritoId = $usuario['distrito_id'];

                $casod->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                // $casod->setTipoMaltrato(self::findTipoMaltrato($item["tipo_maltrato_id"], $em));
                $casod->setDescripcionReporte($item['descripcion_denuncia']);
                $casod->setFechaReporte(new \DateTime($item['fecha_denuncia']));
                $casod->setLugarMaltrato($item['lugar_maltrato']);
                $casod->setDistrito(self::findDistrito($distritoId, $em)->getNombre());
                $casod->setUsuarioApp($usuario['username']);
                $casod->setCodigoApp($item['name_id_form']);
                $casod->setEstadoCaso('Notificado');
				
				$casod->setLatitud($item['lat']);
				$casod->setLongitud($item['lon']);
                $casod->setCodigo(time());

                // registrando datos sub-forms
                $cadenaGeneral = [];
                $centroPoblado = self::findCentroPoblado($centroPobladoid, $em); // para registrar en persona
                foreach ($dataSubForms as $itemsub) {
                    switch ($itemsub['name_subform']) {
                        case 'sub-form-denunciante':
                            if (null !== $itemsub['datos']) {
                                foreach ($itemsub['datos'] as $dato) {
                                    if ($dato['idformpadre'] === $item['name_id_form']) {
                                        $datos = $dato['data'];
                                        $datos['centroPoblado'] = $centroPoblado;
                                        $datos['casoViolencia'] = 1;
                                        $den = self::findDenunciante($datos['name_sub_form'], $em);
                                        if (!$den instanceof Denunciante) {
                                            $den = new Denunciante();
                                        }
                                        $persona = self::findPerson($datos, $em);
                                        $den->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                        $den->setNombres($datos['nombres']);
                                        $den->setApellidos($datos['apellidos']);
                                        $den->setEdad($datos['edad'] ?: 0);
                                        $den->setNumeroDocumento($datos['numero_documento']);
                                        $den->setSexo(self::findSexo($datos['sexo'], $em));
                                        $den->setTelefono($datos['telefono']);
                                        $den->setEmail($datos['email']);
                                        $den->setCodigoApp($datos['name_sub_form']);
                                        $den->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                                        if ('' === $datos['nombres']) {
                                            $datos['numero_documento'] = '00000000';
                                            $datos['nombres'] = 'Anonimo';
                                            $datos['apellidos'] = 'Anonimo';
                                            $persona = self::findPerson($datos, $em);
                                        } else {
                                            $persona->setCasoViolenciaTotal($persona->getCasoViolenciaTotal() + 1);
                                            $persona = self::findPerson($datos, $em);
                                        }
                                        $den->setPersona($persona);

                                        if (0 === \count($den->getCasoViolenciaDenunciantes())) {
                                            $casoden = new CasoViolenciaDenunciante();
                                            $casoden->setDenunciante($den);
                                            // $casoden->setDatosGenerales(json_encode($den, 128));
                                            $casod->addCasoViolenciaDenunciante($casoden);
                                        }
                                    }
                                }
                            }

                            break;
                        case 'sub-form-agraviado':
                            if (null !== $itemsub['datos']) {
                                foreach ($itemsub['datos'] as $dato) {
                                    if ($dato['idformpadre'] === $item['name_id_form']) {
                                        $datos = $dato['data'];
                                        $datos['centroPoblado'] = $centroPoblado;
                                        $datos['casoViolencia'] = 1;
                                        $den = self::findAgraviado($datos['name_sub_form'], $em);
                                        if (!$den instanceof Agraviado) {
                                            $den = new Agraviado();
                                        }
                                        $persona = self::findPerson($datos, $em);
                                        $den->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                        $den->setNombres($datos['nombres']);
                                        $den->setApellidos($datos['apellidos']);
                                        $den->setEdad($datos['edad']);
                                        $den->setNumeroDocumento($datos['numero_documento']);
                                        $den->setSexo(self::findSexo($datos['sexo'], $em));
                                        $den->setTelefono($datos['telefono']);
                                        $den->setEmail($datos['email']);
                                        $den->setEstadoCivil(self::findEstadoCivil($datos['estado_civil_id'], $em));
                                        $den->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                                        $den->setGestacion($datos['gestacion']);
                                        $den->setDiscapacidad($datos['discapacidad']);
                                        $den->setVinculoFamiliar(self::findVinculo($datos['vinculo_familiar_id'], $em));
                                        $den->setDireccion($datos['direccion']);
                                        $den->setReferenciaDomicilio($datos['referencia_domicilio']);
                                        $den->setCodigoApp($datos['name_sub_form']);
                                        $den->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                                        $persona->setCasoViolenciaTotal($persona->getCasoViolenciaTotal() + 1);
                                        $den->setPersona($persona);

                                        $listMaltratos = $datos['tipo_maltrato_id'];
                                        $cadena = '';
                                        foreach ($listMaltratos as $value) {
                                            $cadena = $cadena.$value['label'].', ';
                                            $cadenaGeneral[] = $value['label'];
                                        }
                                        if (0 === \count($den->getCasoViolenciaAgraviados())) {
                                            $casoden = new CasoViolenciaAgraviado();
                                            $casoden->setAgraviado($den);
                                            $casod->addCasoViolenciaAgraviado($casoden);
                                        }
                                    }
                                }
                            }

                            break;
                        case 'sub-form-agresor':
                            if (null !== $itemsub['datos']) {
                                foreach ($itemsub['datos'] as $dato) {
                                    if ($dato['idformpadre'] === $item['name_id_form']) {
                                        $datos = $dato['data'];
                                        $datos['centroPoblado'] = $centroPoblado;
                                        $datos['casoViolencia'] = 1;
                                        $den = self::findAgresor($datos['name_sub_form'], $em);
                                        if (!$den instanceof \App\Entity\Agresor) {
                                            $den = new Agresor();
                                        }
                                        $persona = self::findPerson($datos, $em);
                                        $den->setTipoDocumento(self::findTipoDocumento($datos['tipo_documento_id'], $em));
                                        $den->setNombres($datos['nombres']);
                                        $den->setApellidos($datos['apellidos']);
                                        $den->setEdad($datos['edad']);
                                        $den->setNumeroDocumento($datos['numero_documento']);
                                        $den->setSexo(self::findSexo($datos['sexo'], $em));
                                        $den->setTelefono($datos['telefono']);
                                        $den->setEmail($datos['email']);
                                        $den->setVinculoFamiliar(self::findVinculo($datos['vinculo_familiar_id'], $em));
                                        $den->setEstadoCivil(self::findEstadoCivil($datos['estado_civil_id'], $em));
                                        $den->setNacionalidad(self::findNacionalidad($datos['nacionalidad_id'], $em));
                                        $den->setDireccion($datos['direccion']);
                                        $den->setCodigoApp($datos['name_sub_form']);
                                        $den->setCentroPoblado(self::findCentroPoblado($centroPobladoid, $em));
                                        $persona->setCasoViolenciaTotal($persona->getCasoViolenciaTotal() + 1);
                                        $den->setPersona($persona);

                                        if (0 === \count($den->getCasoViolenciaAgresors())) {
                                            $casoden = new CasoViolenciaAgresor();
                                            $casoden->setAgresor($den);
                                            $casod->addCasoViolenciaAgresor($casoden);
                                        }
                                    }
                                }
                            }
                            break;
                    }
                }
                $casod->setTipoMaltratos(implode(',', array_unique($cadenaGeneral)));
                $this->entityManager->persist($casod);
                $this->entityManager->flush();
            }

            return $this->response(['data' => []]);
        } catch (\Exception $ex) {
            return $this->response(['data' => $ex->getMessage()], false);
        }
    }

    /**
     * Busca una persona por su número de documento en diferentes roles (Denunciante, Agraviado, etc.)
     * o consulta una API externa si no se encuentra localmente.
     * @param Request $request La solicitud HTTP con el número de documento en formato JSON.
     * @return Response Una respuesta JSON con los datos de la persona encontrada.
     */
    #[Route(path: '/find/persona', name: 'api_find_persona', methods: ['POST'])]
    public function findPersona(Request $request): Response
    {
        try {
            $content = json_decode($request->getContent(), true);
            $numeroDocumento = $content;
            $em = $this->entityManager;

            $persona = $em->getRepository(Denunciante::class)
                ->findOneBy(['numeroDocumento' => $numeroDocumento]);

            $data = null;
            if (null !== $persona) {
                $data['nombres'] = $persona->getNombres();
                $data['apellidos'] = $persona->getApellidos();
                $data['edad'] = $persona->getEdad();
                $data['sexo'] = self::getParameterApi($persona->getSexo());
                $data['telefono'] = $persona->getTelefono();
                $data['email'] = $persona->getEmail();
            } else {
                $persona = $em->getRepository(Agraviado::class)
                    ->findOneBy(['numeroDocumento' => $numeroDocumento]);
                if (null !== $persona) {
                    $data['nombres'] = $persona->getNombres();
                    $data['apellidos'] = $persona->getApellidos();
                    $data['edad'] = $persona->getEdad();
                    $data['sexo'] = self::getParameterApi($persona->getSexo());
                    $data['telefono'] = $persona->getTelefono();
                    $data['email'] = $persona->getEmail();
                    $data['estadoCivil'] = self::getParameterApi($persona->getEstadoCivil());
                    $data['nacionalidad'] = self::getParameterApi($persona->getNacionalidad());
                    $data['vinculo'] = self::getParameterApi($persona->getVinculoFamiliar());
                    $data['direccion'] = $persona->getDireccion();
                    $data['referencia'] = $persona->getReferenciaDomicilio();
                    $data['discapacidad'] = $persona->getDiscapacidad();
                    $data['gestacion'] = $persona->getGestacion();
                    $data['telefono'] = $persona->getTelefono();
                    $data['email'] = $persona->getEmail();
                } else {
                    $persona = $em->getRepository(Agresor::class)
                        ->findOneBy(['numeroDocumento' => $numeroDocumento]);

                    if (null !== $persona) {
                        $data['nombres'] = $persona->getNombres();
                        $data['apellidos'] = $persona->getApellidos();
                        $data['edad'] = $persona->getEdad();
                        $data['sexo'] = self::getParameterApi($persona->getSexo());
                        $data['estadoCivil'] = self::getParameterApi($persona->getEstadoCivil());
                        $data['nacionalidad'] = self::getParameterApi($persona->getNacionalidad());
                        $data['vinculo'] = self::getParameterApi($persona->getVinculoFamiliar());
                        $data['direccion'] = $persona->getDireccion();
                        $data['telefono'] = $persona->getTelefono();
                        $data['email'] = $persona->getEmail();
                    } else {
                        $persona = $em->getRepository(MenorEdad::class)
                            ->findOneBy(['numeroDocumento' => $numeroDocumento]);
                        if (null !== $persona) {
                            $data['nombres'] = $persona->getNombres();
                            $data['apellidos'] = $persona->getApellidos();
                            $data['edad'] = $persona->getEdad();
                            $data['sexo'] = self::getParameterApi($persona->getSexo());
                            $data['nacionalidad'] = self::getParameterApi($persona->getNacionalidad());
                            $data['direccion'] = $persona->getDireccion();
                        } else {
                            $persona = $em->getRepository(Tutor::class)
                                ->findOneBy(['numeroDocumento' => $numeroDocumento]);
                            if (null !== $persona) {
                                $data['nombres'] = $persona->getNombres();
                                $data['apellidos'] = $persona->getApellidos();
                                $data['sexo'] = self::getParameterApi($persona->getSexo());
                                $data['vinculo'] = self::getParameterApi($persona->getVinculoFamiliar());
                                $data['telefono'] = $persona->getTelefono();
                            }else{
                                $persona = $this->consultaDNI($numeroDocumento);
                                $data['nombres'] = $persona->nombres;
                                $data['apellidos'] = $persona->apellidoPaterno.' '.$persona->apellidoMaterno;
                                $data['sexo'] = '';
                                $data['vinculo'] = '';
                                $data['telefono'] = '';
                            }
                        }
                    }
                }
            }

            return $this->response(['data' => $data]);
        } catch (\Exception $ex) {
            return $this->response(['data' => $ex->getMessage()], false);
        }
    }
	
	public function consultaDNI($dni){
        $token="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InNwcnlzYWxlczAxQGdtYWlsLmNvbSJ9.biHs8iG14CEMalUxmoj_dYfnxreSwHDhA-_yBFHMP_k";
        $ch = curl_init("https://dniruc.apisperu.com/api/v1/dni/".$dni."?token=$token");
        curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $output = curl_exec($ch);  
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $json=json_decode($output);
            return $json;
    }
}
