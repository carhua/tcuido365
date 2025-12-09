<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ParametroApiController extends ApiController
{
    /**
     * Define la estructura del formulario principal para reportar casos de maltrato (violencia).
     * @param Request $request
     * @return Response JSON con la configuración del formulario.
     */
    #[Route(path: '/formmaltrato/v1/form', methods: ['GET'], name: 'api_parameter_v1_list')]
    public function dataFormMaltrato(Request $request): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
          /*  [
                "name" => "distrito_id",
                "type" => "select-distrito",
                "required" => false,
                "title" => "Distrito",
            ],
            [
                "name" => "centro_poblado_id",
                "type" => "select-fijo",
                "required" => false,
                "title" => "Centro Poblado (*)"
            ],*/
         /*   [
                "name" => "tipo_maltrato_id",
                "type" => "select",
                "required" => true,
                "title" => "Tipo Maltrato (*)",
                "options" => $this->listTipoMaltrato($em)
            ],*/
            [
                'name' => 'lugar_maltrato',
                'type' => 'text',
                'required' => false,
                'title' => 'Lugar/AA.HH',
            ],
            [
                'name' => 'fecha_denuncia',
                'type' => 'date',
                'required' => true,
                'title' => 'Fecha Reporte (*)',
            ],
            [
                'name' => 'descripcion_denuncia',
                'type' => 'textarea',
                'required' => false,
                'title' => 'Descripción/Hechos Ocurridos',
            ],
            [
                'name' => 'label_fija',
                'type' => 'label-fija',
                'title' => 'Adicionar detalles',
            ],
            [
                'name' => 'sub-form-denunciante',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Denunciante',
                'icon' => 'icono1.png',
            ],
            [
                'name' => 'sub-form-agraviado',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Presunto Agraviado',
                 'icon' => 'icono2.png',
            ],
            [
                'name' => 'sub-form-agresor',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Presunto Agresor',
                 'icon' => 'icono3.png',
            ],
            [
                'name' => 'name_id_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos del denunciante en un caso de violencia.
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-denunciante/v1/form', methods: ['GET'], name: 'api_form2_v1_list')]
    public function dataFormDenunciante(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true, // Este campo es siempre requerido para determinar la lógica condicional
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($entityManager),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true, // Por defecto es requerido
                'title' => 'Número Documento (*)',
                // Si tipo_documento_id es 'Anónimo' (asumiendo ID 0), este campo se vuelve opcional.
                // La lógica en el cliente deberá interpretar esta propiedad.
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true, // Por defecto es requerido
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true, // Por defecto es requerido
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number', // Cambiado de 'text' a 'number' para consistencia
                'required' => true, // Por defecto es requerido
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => false,
                'title' => 'Sexo',
                'options' => $this->listSexo($entityManager),
            ],
            [
                'name' => 'telefono',
                'type' => 'number',
                'required' => true,
                'title' => 'Teléfono (*)',
            ],
            [
                'name' => 'email',
                'type' => 'text',
                'required' => false,
                'title' => 'Correo Electrónico',
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos del agraviado en un caso de violencia.
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-agraviado/v1/form', methods: ['GET'], name: 'api_form3_v1_list')]
    public function dataFormAgraviado(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $em = $entityManager;

        $data = [
            [
                'name' => 'tipo_maltrato_id',
                'type' => 'select-multiple',
                'required' => true,
                'title' => 'Tipo Maltrato (*)',
                'options' => $this->listTipoMaltrato($em),
            ],

            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Número Documento (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number',
                'required' => true,
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => true,
                'title' => 'Sexo (*)',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'estado_civil_id',
                'type' => 'select',
                'required' => false,
                'title' => 'Estado Civil (*)',
                'options' => $this->listEstados($em),
            ],
            [
                'name' => 'nacionalidad_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Nacionalidad (*)',
                'options' => $this->listNacionalidad($em),
            ],
            [
                'name' => 'vinculo_familiar_id',
                'type' => 'select',
                'required' => false,
                'title' => 'Vínculo Familiar Presunto Agraviado (*)',
                'options' => $this->listVinculos($em),
            ],
            [
                'name' => 'direccion',
                'type' => 'text',
                'required' => false,
                'title' => 'Dirección (*)',
            ],
            [
                'name' => 'referencia_domicilio',
                'type' => 'text',
                'required' => false,
                'title' => 'Referencia Domicilio (*)',
            ],
            [
                'name' => 'discapacidad',
                'type' => 'radio',
                'required' => false,
                'title' => 'Discapacidad (*)',
                'options' => [
                    ['key' => 'Si', 'label' => 'Si'],
                    ['key' => 'No', 'label' => 'No'],
                ],
            ],
            [
                'name' => 'gestacion',
                'type' => 'radio',
                'required' => false,
                'title' => 'Gestación (*)',
                'options' => [
                    ['key' => 'Si', 'label' => 'Si'],
                    ['key' => 'No', 'label' => 'No'],
                ],
            ],

            [
                'name' => 'telefono',
                'type' => 'number',
                'required' => false,
                'title' => 'Teléfono',
            ],
            [
                'name' => 'email',
                'type' => 'text',
                'required' => false,
                'title' => 'Correo Electrónico',
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos del agresor en un caso de violencia.
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-agresor/v1/form', methods: ['GET'], name: 'api_form4_v1_list')]
    public function dataFormAgresor(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $em = $entityManager;

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Número Documento',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number',
                'required' => true,
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => true,
                'title' => 'Sexo (*)',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'estado_civil_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Estado Civil (*)',
                'options' => $this->listEstados($em),
            ],
            [
                'name' => 'nacionalidad_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Nacionalidad (*)',
                'options' => $this->listNacionalidad($em),
            ],
            [
                'name' => 'vinculo_familiar_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Vínculo Familiar Presunto Agresor (*)',
                'options' => $this->listVinculos($em),
            ],
            [
                'name' => 'direccion',
                'type' => 'text',
                'required' => false,
                'title' => 'Dirección (*)',
            ],

            [
                'name' => 'telefono',
                'type' => 'number',
                'required' => false,
                'title' => 'Teléfono',
            ],
            [
                'name' => 'email',
                'type' => 'text',
                'required' => false,
                'title' => 'Correo Electrónico',
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del formulario principal para reportar casos de desprotección familiar.
     * @param Request $request
     * @return Response JSON con la configuración del formulario.
     */
    #[Route(path: '/formdesproteccion/v1/form', methods: ['GET'], name: 'api_form5_v1_form')]
    public function dataFormDesproteccion(Request $request): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
           /* [
                "name" => "distrito_id",
                "type" => "select-distrito",
                "required" => false,
                "title" => "Distrito",

            ],
            [
                "name" => "centro_poblado_id",
                "type" => "select-fijo",
                "required" => false,
                "title" => "Centro Poblado",
            ],*/
            [
                'name' => 'lugar_reporte',
                'type' => 'text',
                'required' => false,
                'title' => 'Lugar/AA.HH',
            ],
            [
                'name' => 'fecha_reporte',
                'type' => 'date',
                'required' => true,
                'title' => 'Fecha Reporte (*)',
            ],
      /*      [
                "name" => "situacion_encontrada_id",
                "type" => "select2",
                "required" => true,
                "title" => "Situacion Encontrada (*)",
                "options" => $this->listSituaciones($em)
            ],*/
            [
                'name' => 'descripcion_reporte',
                'type' => 'textarea',
                'required' => false,
                'title' => 'Descripción Reporte',
            ],
            [
                'name' => 'label_fija',
                'type' => 'label-fija',
                'title' => 'Adicionar Detalles',
            ],
            [
                'name' => 'sub-form-menoredad',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Menor de Edad',
                 'icon' => 'icono4.png',
            ],
            [
                'name' => 'sub-form-tutor',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Tutor',
                 'icon' => 'icono5.png',
            ],
            [
                'name' => 'name_id_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos del menor de edad en un caso de desprotección.
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-menoredad/v1/form', methods: ['GET'], name: 'api_form6_v1_list')]
    public function dataFormMenorEdad(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $em = $entityManager;

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Número Documento (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number',
                'required' => true,
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => true,
                'title' => 'Sexo (*)',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'nacionalidad_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Nacionalidad (*)',
                'options' => $this->listNacionalidad($em),
            ],
            [
                'name' => 'direccion',
                'type' => 'text',
                'required' => true,
                'title' => 'Direccion (*)',
            ],
            [
                'name' => 'situacion_encontrada_id',
                'type' => 'select-multiple',
                'required' => true,
                'title' => 'Situaciones Encontradas (*)',
                'options' => $this->listSituaciones($em),
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos del tutor en un caso de desprotección.
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-tutor/v1/form', methods: ['GET'], name: 'api_form7_v1_list')]
    public function dataFormTutor(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $em = $entityManager;

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Número Documento (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number',
                'required' => true,
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => true,
                'title' => 'Sexo (*)',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'vinculo_familiar_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Vínculo Familiar (*)',
                'options' => $this->listVinculos($em),
            ],
            [
                'name' => 'telefono',
                'type' => 'text',
                'required' => false,
                'title' => 'Teléfono/Celular',
            ],

            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    // PARAMETROS PARA EL MODULO DE CASO DE TRATA DE PERSONAS
    /**
     * Define la estructura del formulario principal para reportar casos de trata de personas.
     * @param Request $request
     * @return Response JSON con la configuración del formulario.
     */
    #[Route(path: '/formtrata/v1/form', methods: ['GET'], name: 'api_formtrata_v1_form')]
    public function dataFormTrata(Request $request): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
         /*   [
                "name" => "distrito_id",
                "type" => "select-distrito",
                "required" => false,
                "title" => "Distrito",
            ],
            [
                "name" => "centro_poblado_id",
                "type" => "select-fijo",
                "required" => false,
                "title" => "Centro Poblado",
            ],*/
            [
                'name' => 'fecha_reporte',
                'type' => 'date',
                'required' => true,
                'title' => 'Fecha Reporte (*)',
            ],
            [
                'name' => 'descripcion_reporte',
                'type' => 'textarea',
                'required' => false,
                'title' => 'Descripción Reporte',
            ],
            [
                'name' => 'label_fija',
                'type' => 'label-fija',
                'title' => 'Adicionar detalles',
            ],
            [
                'name' => 'sub-form-detenido',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Detenido',
                 'icon' => 'icono6.png',
            ],
            [
                'name' => 'sub-form-victima',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Victima',
                 'icon' => 'icono7.png',
            ],
            [
                'name' => 'name_id_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos del detenido en un caso de trata de personas.
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-detenido/v1/form', methods: ['GET'], name: 'api_formdetenido_v1_list')]
    public function dataFormDetenido(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Numero Documento (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number',
                'required' => true,
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => true,
                'title' => 'Sexo (*)',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'nacionalidad_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Nacionalidad (*)',
                'options' => $this->listNacionalidad($em),
            ],
            [
                'name' => 'forma_captacion_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Forma de captación(*)',
                'options' => $this->listCaptaciones($em),
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos de la víctima en un caso de trata de personas.
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-victima/v1/form', methods: ['GET'], name: 'api_formvictima_v1_list')]
    public function dataFormVictima(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Número Documento (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number',
                'required' => true,
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => true,
                'title' => 'Sexo (*)',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'nacionalidad_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Nacionalidad (*)',
                'options' => $this->listNacionalidad($em),
            ],
            [
                'name' => 'tipo_explotacion_id',
                'type' => 'select-multiple',
                'required' => true,
                'title' => 'Tipo Explotacion (*)',
                'options' => $this->listTipoExplotaciones($em),
            ],
            [
                'name' => 'lugar_forma_rescate',
                'type' => 'textarea',
                'required' => true,
                'title' => 'Lugar y Forma de rescate (*)',
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    // PARAMETROS PARA EL MODULO DE CASO DE PERSONAS DESAPARECIDAS
    /**
     * Define la estructura del formulario principal para reportar casos de personas desaparecidas.
     * @param Request $request
     * @return Response JSON con la configuración del formulario.
     */
    #[Route(path: '/formdesaparecido/v1/form', methods: ['GET'], name: 'api_formdesaparecido_v1_form')]
    public function dataFormDesaparecido(Request $request): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
            [
                'name' => 'fecha_reporte',
                'type' => 'date',
                'required' => true,
                'title' => 'Fecha Reporte (*)',
            ],
            [
                'name' => 'descripcion_reporte',
                'type' => 'textarea',
                'required' => false,
                'title' => 'Descripción Reporte',
            ],
            [
                'name' => 'label_fija',
                'type' => 'label-fija',
                'title' => 'Adicionar detalles',
            ],
            [
                'name' => 'sub-form-denunciante-des',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Denunciante',
                 'icon' => 'icono8.png',
            ],
            [
                'name' => 'sub-form-desaparecido',
                'type' => 'sub-form',
                'required' => false,
                'title' => 'Desaparecido',
                 'icon' => 'icono9.png',
            ],
          /*  [
                "name" => "label_fija",
                "type" => "label-fija",
                'title' => "Nota: Este caso sera reportado a"
            ],*/
            [
                'name' => 'name_id_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    // denunciante-des
    /**
     * Define la estructura del sub-formulario para los datos del denunciante en un caso de persona desaparecida.
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-denunciante-des/v1/form', methods: ['GET'], name: 'api_formdenunciantedes_v1_list')]
    public function dataFormDenuncianteDes(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Número Documento',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'text',
                'required' => false,
                'title' => 'Edad',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => false,
                'title' => 'Sexo',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'telefono',
                'type' => 'number',
                'required' => true,
                'title' => 'Telefono (*)',
            ],
            [
                'name' => 'email',
                'type' => 'text',
                'required' => false,
                'title' => 'Correo Electrónico',
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }

    /**
     * Define la estructura del sub-formulario para los datos de la persona desaparecida.
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response JSON con la configuración del sub-formulario.
     */
    #[Route(path: '/sub-form-desaparecido/v1/form', methods: ['GET'], name: 'api_formdesaparecido_v1_list')]
    public function dataSubFormDesaparecido(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessAuthorization($request);

        $data = [
            [
                'name' => 'tipo_documento_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Tipo Documento (*)',
                'options' => $this->listTipoDocumento($em),
            ],
            [
                'name' => 'numero_documento',
                'type' => 'text-document',
                'required' => true,
                'title' => 'Número Documento (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'nombres',
                'type' => 'text',
                'required' => true,
                'title' => 'Nombres (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'apellidos',
                'type' => 'text',
                'required' => true,
                'title' => 'Apellidos (*)',
                'conditionalRequired' => [
                    'dependsOnField' => 'tipo_documento_id',
                    'condition' => [
                        'operator' => 'equals',
                        'value' => 3,
                    ],
                    'becomesRequired' => false,
                ],
            ],
            [
                'name' => 'edad',
                'type' => 'number',
                'required' => true,
                'title' => 'Edad (*)',
            ],
            [
                'name' => 'sexo',
                'type' => 'select',
                'required' => true,
                'title' => 'Sexo (*)',
                'options' => $this->listSexo($em),
            ],
            [
                'name' => 'nacionalidad_id',
                'type' => 'select',
                'required' => true,
                'title' => 'Nacionalidad (*)',
                'options' => $this->listNacionalidad($em),
            ],
            [
                'name' => 'telefono',
                'type' => 'number',
                'required' => true,
                'title' => 'Telefono (*)',
            ],
            [
                'name' => 'discapacidad',
                'type' => 'radio',
                'required' => true,
                'title' => 'Discapacidad (*)',
                'options' => [
                    ['key' => 'Si', 'label' => 'Si'],
                    ['key' => 'No', 'label' => 'No'],
                ],
            ],
            [
                'name' => 'direccion_desaparicion',
                'type' => 'textarea',
                'required' => true,
                'title' => 'Dirección desaparición (*)',
            ],
            [
                'name' => 'lugar_desaparicion',
                'type' => 'textarea',
                'required' => true,
                'title' => 'Lugar de desaparición (*)',
            ],
            [
                'name' => 'name_sub_form',
                'type' => 'idform',
            ],
        ];

        return $this->response(['data' => $data]);
    }
}
