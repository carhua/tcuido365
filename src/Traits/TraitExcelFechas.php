<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Traits;

use App\Entity\Agraviado;
use App\Entity\Denunciante;
use App\Entity\Persona;
use App\Enum\LeyendaEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

use function chr;

trait TraitExcelFechas
{
    use TraitDate;

    public static function casosViolencia($data, $finicial, $ffinal)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE CASOS DE VIOLENCIA');
            $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
            $sheet->setCellValue('A2', 'PERIODO: '.self::formatFecha($finicial).' - '.self::formatFecha($ffinal));
            $spreadsheet->getActiveSheet()->mergeCells('A2:D2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:D3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'DISTRITO');
            $sheet->setCellValue('B5', 'CENTRO POBLADO');
            $sheet->setCellValue('C5', 'FECHA REPORTE');
            $sheet->setCellValue('D5', 'TIPO MALTRATO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:D5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:D5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:D5');

            $i = 6;
            // /** @var CasoViolencia[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getDistrito());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getFechaReporte()->format('d/m/Y'));
                $sheet->setCellValue('D'.$i, $dato->getTipoMaltratos());

                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('CasosViolencia');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporteKardex.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function casosDesproteccion($data, $finicial, $ffinal)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE CASOS DE RIESGO DE DESPROTECCION');
            $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
            $sheet->setCellValue('A2', 'PERIODO: '.self::formatFecha($finicial).' - '.self::formatFecha($ffinal));
            $spreadsheet->getActiveSheet()->mergeCells('A2:D2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:D3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'DISTRITO');
            $sheet->setCellValue('B5', 'CENTRO POBLADO');
            $sheet->setCellValue('C5', 'FECHA REPORTE');
            $sheet->setCellValue('D5', 'SITUACIONES ENCONTRADAS');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:D5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:D5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:D5');

            $i = 6;
            // /** @var CasoViolencia[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getDistrito());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getFechaReporte()->format('d/m/Y'));
                $sheet->setCellValue('D'.$i, $dato->getSituacionesEncontradas());

                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Casos-Desproteccion');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function casosTrata($data, $finicial, $ffinal)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'REPORTE DE CASOS DE TRATA DE PERSONAS');
            $spreadsheet->getActiveSheet()->mergeCells('B1:D1');
            $sheet->setCellValue('B2', 'PERIODO: '.self::formatFecha($finicial).' - '.self::formatFecha($ffinal));
            $spreadsheet->getActiveSheet()->mergeCells('B2:D2');
            $sheet->setCellValue('B3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('B3:D3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'DISTRITO');
            $sheet->setCellValue('B5', 'CENTRO POBLADO');
            $sheet->setCellValue('C5', 'FECHA REPORTE');
            $sheet->setCellValue('D5', 'TIPO DE EXPLOTACIONES');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:D5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:D5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:D5');

            $i = 6;
            // /** @var CasoViolencia[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getDistrito());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getFechaReporte()->format('d/m/Y'));
                $sheet->setCellValue('D'.$i, $dato->getTipoExplotacionesGeneral());

                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Casos-Trata-Personas');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function casosDesaparecido($data, $finicial, $ffinal)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'REPORTE DE CASOS DE PERSONAS DESAPARECIDAS');
            $spreadsheet->getActiveSheet()->mergeCells('B1:G1');
            $sheet->setCellValue('B2', 'PERIODO: '.self::formatFecha($finicial).' - '.self::formatFecha($ffinal));
            $spreadsheet->getActiveSheet()->mergeCells('B2:G2');
            $sheet->setCellValue('B3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('B3:G3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:G3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:G3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'DISTRITO');
            $sheet->setCellValue('B5', 'CENTRO POBLADO');
            $sheet->setCellValue('C5', 'FECHA REPORTE');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:C5');

            $i = 6;
            // /** @var CasoViolencia[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getDistrito());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getFechaReporte()->format('d/m/Y H:i'));
                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Casos-Personas-Desaparecidas');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function listDenunciante($data, $centroPoblado)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE DENUNCIANTES');
            $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
            $sheet->setCellValue('A2', 'CENTRO POBLADO: '.$centroPoblado->getNombre());
            $spreadsheet->getActiveSheet()->mergeCells('A2:D2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:D3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TIPO DOCUMENTO');
            $sheet->setCellValue('B5', 'Nº DOCUMENTO');
            $sheet->setCellValue('C5', 'NOMBRES');
            $sheet->setCellValue('D5', 'APELLIDOS');
            $sheet->setCellValue('E5', 'EDAD');
            $sheet->setCellValue('F5', 'SEXO');
            $sheet->setCellValue('G5', 'TELEFONO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:G5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:G5');

            $i = 6;
            /** @var Denunciante[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getTipoDocumento()->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('C'.$i, $dato->getNombres());
                $sheet->setCellValue('D'.$i, $dato->getApellidos());
                $sheet->setCellValue('E'.$i, $dato->getEdad());
                $sheet->setCellValue('F'.$i, $dato->getSexo());
                $sheet->setCellValue('G'.$i, $dato->getTelefono());

                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Denunciantes');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function listAgraviados($data, $anio, $mes)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE AGRAVIADOS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
            $sheet->setCellValue('A2', 'PERIODO: '.self::getMes($mes).' - '.$anio);
            $spreadsheet->getActiveSheet()->mergeCells('A2:D2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:D3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TIPO DOCUMENTO');
            $sheet->setCellValue('B5', 'Nº DOCUMENTO');
            $sheet->setCellValue('C5', 'NOMBRES');
            $sheet->setCellValue('D5', 'APELLIDOS');
            $sheet->setCellValue('E5', 'EDAD');
            $sheet->setCellValue('F5', 'SEXO');
            $sheet->setCellValue('G5', 'TELEFONO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:G5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:G5');

            $i = 6;
            /** @var Agraviado[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getTipoDocumento()->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('C'.$i, $dato->getNombres());
                $sheet->setCellValue('D'.$i, $dato->getApellidos());
                $sheet->setCellValue('E'.$i, $dato->getEdad());
                $sheet->setCellValue('F'.$i, $dato->getSexo());
                $sheet->setCellValue('G'.$i, $dato->getTelefono());

                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Denunciantes-'.self::getMes($mes).'-'.$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function historialCasosViolencia($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'HISTORIAL DE CASOS DE VIOLENCIA');
            $spreadsheet->getActiveSheet()->mergeCells('B1:K1');
            $sheet->setCellValue('B2', 'Listado de personas con la cantidad de casos de violencia reportados');
            $spreadsheet->getActiveSheet()->mergeCells('B2:K2');
            $sheet->setCellValue('B3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('B3:K3');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('B4', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('B4:K4');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:K4')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:K4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(100);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A6', 'DISTRITO');
            $sheet->setCellValue('B6', 'CENTRO POBLADO');
            $sheet->setCellValue('C6', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('D6', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('E6', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('F6', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('G6', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('H5', 'CASOS DE VIOLENCIA REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('H5:K5');
            $sheet->setCellValue('H6', 'DENUNCIANTE');
            $sheet->setCellValue('I6', 'AGRESOR');
            $sheet->setCellValue('J6', 'AGRAVIADO');
            $sheet->setCellValue('K6', 'TOTAL');
            //  $spreadsheet->getActiveSheet()->mergeCells('I5:I6');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:K6')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:K6')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A6:K6');

            $i = 7;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getCentroPoblado()->getDistrito()->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('D'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('E'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('F'.$i, $dato->getEdad());
                $sheet->setCellValue('G'.$i, $dato->getSexo());
                $sheet->setCellValue('H'.$i, \count($dato->getDenunciantes()));
                $sheet->setCellValue('I'.$i, \count($dato->getAgresors()));
                $sheet->setCellValue('J'.$i, \count($dato->getAgraviados()));
                $sheet->setCellValue('K'.$i, '=H'.$i.'+ I'.$i.'+ J'.$i);
                $number1 = $spreadsheet->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('J'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }
                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('HistorialCasosViolencia');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function historialCasosDesproteccion($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'HISTORIAL DE CASOS DE RIESGO DE DESPROTECCION');
            $spreadsheet->getActiveSheet()->mergeCells('B1:J1');
            $sheet->setCellValue('B2', 'Listado de personas con la cantidad de casos de riesgo de desproteccion reportados');
            $spreadsheet->getActiveSheet()->mergeCells('B2:J2');
            $sheet->setCellValue('B3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('B3:J3');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('B4', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('B4:J4');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:J4')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:J4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(100);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A6', 'DISTRITO');
            $sheet->setCellValue('B6', 'CENTRO POBLADO');
            $sheet->setCellValue('C6', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('D6', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('E6', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('F6', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('G6', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('H5', 'CASOS DE RIESGO DE DESPROTECCION REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('H5:J5');
            $sheet->setCellValue('H6', 'MENOR EDAD');
            $sheet->setCellValue('I6', 'TUTOR');
            $sheet->setCellValue('J6', 'TOTAL');

            //  $spreadsheet->getActiveSheet()->mergeCells('I5:I6');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:J6')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:J6')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A6:J6');

            $i = 7;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getCentroPoblado()->getDistrito()->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('D'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('E'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('F'.$i, $dato->getEdad());
                $sheet->setCellValue('G'.$i, $dato->getSexo());
                $sheet->setCellValue('H'.$i, \count($dato->getMenorEdads()));
                $sheet->setCellValue('I'.$i, \count($dato->getTutors()));
                $sheet->setCellValue('J'.$i, '=H'.$i.'+ I'.$i);

                $number1 = $spreadsheet->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('H'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }

                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            /*   for ($col = 65; $col <= 78; $col++) {
                   $spreadsheet->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
               }*/

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);

            $sheet->setTitle('HistorialRiesgoDesproteccion');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function historialTipoViolencia($data, $tipoPersona, $centro)
    {
        try {
            $spreadsheet = new Spreadsheet();
            // $spreadsheet->setActiveSheetIndex($j);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'HISTORIAL DE CASOS POR TIPO DE MALTRATO');
            $spreadsheet->getActiveSheet()->mergeCells('B1:L1');
            $sheet->setCellValue('B2', 'Tipo Persona: '.(1 === $tipoPersona ? 'Agresor' : 'Agraviado').' -  Centro Poblado: '.$centro);
            $spreadsheet->getActiveSheet()->mergeCells('B2:L2');
            $sheet->setCellValue('B3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('B3:L3');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('B4', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('B4:L4');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:L4')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:L4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(100);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A6', 'DISTRITO');
            $sheet->setCellValue('B6', 'CENTRO POBLADO');
            $sheet->setCellValue('C6', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('D6', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('E6', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('F6', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('G6', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            if (1 === $tipoPersona) {
                $sheet->setCellValue('H5', 'TIPOS DE MALTRATO QUE HA COMETIDO');
            } else {
                $sheet->setCellValue('H5', 'TIPOS DE MALTRATO QUE HA RECIBIDO');
            }

            $spreadsheet->getActiveSheet()->mergeCells('H5:L5');
            $sheet->setCellValue('H6', 'PSICOLOGICO');
            $sheet->setCellValue('I6', 'FISICO');
            $sheet->setCellValue('J6', 'SEXUAL');
            $sheet->setCellValue('K6', 'ECONOMICO/PATRIMONIAL');
            $sheet->setCellValue('L6', 'TOTAL');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:L6')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:L6')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A6:L6');

            $i = 7;
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato['nombreDistrito']);
                $sheet->setCellValue('B'.$i, $dato['nombreCentro']);
                $sheet->setCellValue('C'.$i, $dato['tipoDocumento']);
                $sheet->setCellValue('D'.$i, $dato['numeroDocumento']);
                $sheet->setCellValue('E'.$i, $dato['nombres'].' '.$dato['apellidos']);
                $sheet->setCellValue('F'.$i, $dato['edad']);
                $sheet->setCellValue('G'.$i, $dato['sexo']);
                $sheet->setCellValue('H'.$i, $dato['tipoPsicologico']);
                $sheet->setCellValue('I'.$i, $dato['tipoFisico']);
                $sheet->setCellValue('J'.$i, $dato['tipoSexual']);
                $sheet->setCellValue('K'.$i, $dato['tipoEconomico']);
                $sheet->setCellValue('L'.$i, '=H'.$i.'+ I'.$i.'+ J'.$i.' +K'.$i);
                $number = $spreadsheet->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();

                // aplicando colores al excel
                $color = self::exportColorExcel(number_format($number));
                if (null !== $color) {
                    $spreadsheet->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color);
                }
                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo bordes a las filas
            $styleBorders = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A6:L'.$i)->applyFromArray($styleBorders);

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('HistorialTipoViolencia');
            // $spreadsheet->addSheet($sheet);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function historialCasosTrata($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'HISTORIAL DE CASOS DE TRATA DE PERSONAS');
            $spreadsheet->getActiveSheet()->mergeCells('B1:J1');
            $sheet->setCellValue('B2', 'Listado de personas con la cantidad de casos de trata de personas reportados');
            $spreadsheet->getActiveSheet()->mergeCells('B2:J2');
            $sheet->setCellValue('B3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('B3:J3');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('B4', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('B4:J4');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:J4')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:J4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(100);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A6', 'DISTRITO');
            $sheet->setCellValue('B6', 'CENTRO POBLADO');
            $sheet->setCellValue('C6', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('D6', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('E6', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('F6', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('G6', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('H5', 'CASOS DE TRATA DE PERSONAS REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('H5:J5');
            $sheet->setCellValue('H6', 'DETENIDO');
            $sheet->setCellValue('I6', 'VICTIMA');
            $sheet->setCellValue('J6', 'TOTAL');

            //  $spreadsheet->getActiveSheet()->mergeCells('I5:I6');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:J6')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:J6')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A6:J6');

            $i = 7;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getCentroPoblado()->getDistrito()->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('D'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('E'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('F'.$i, $dato->getEdad());
                $sheet->setCellValue('G'.$i, $dato->getSexo());
                $sheet->setCellValue('H'.$i, \count($dato->getDetenidos()));
                $sheet->setCellValue('I'.$i, \count($dato->getVictimas()));
                $sheet->setCellValue('J'.$i, '=H'.$i.'+ I'.$i);

                $number1 = $spreadsheet->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('H'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }

                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            // estableciendo anchos de columa
            /*            for ($col = 65; $col <= 78; $col++) {
                            $spreadsheet->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
                        }*/

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);

            $sheet->setTitle('HistorialTrataPersonas');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function historialCasosDesaparecidos($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', 'HISTORIAL DE CASOS DE PERSONAS DESAPARECIDAS');
            $spreadsheet->getActiveSheet()->mergeCells('B1:J1');
            $sheet->setCellValue('B2', 'Listado de personas con la cantidad de casos de personas desaparecidas reportados');
            $spreadsheet->getActiveSheet()->mergeCells('B2:J2');
            $sheet->setCellValue('B3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('B3:J3');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('B4', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('B4:J4');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:J4')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:J4')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(100);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A6', 'DISTRITO');
            $sheet->setCellValue('B6', 'CENTRO POBLADO');
            $sheet->setCellValue('C6', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('D6', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('E6', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('F6', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('G6', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('H5', 'CASOS DE PERSONAS DESAPARECIDAS REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('H5:I5');
            $sheet->setCellValue('H6', 'DENUNCIANTE');
            $sheet->setCellValue('I6', 'DESAPARECIDO');
            $sheet->setCellValue('J6', 'TOTAL');

            //  $spreadsheet->getActiveSheet()->mergeCells('I5:I6');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:J6')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:J6')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A6:J6');

            $i = 7;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getCentroPoblado()->getDistrito()->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('D'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('E'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('F'.$i, $dato->getEdad());
                $sheet->setCellValue('G'.$i, $dato->getSexo());
                $sheet->setCellValue('H'.$i, \count($dato->getDenuncianteDesaparecidos()));
                $sheet->setCellValue('I'.$i, \count($dato->getDesaparecidos()));
                $sheet->setCellValue('J'.$i, '=H'.$i.'+ I'.$i);

                $number1 = $spreadsheet->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('H'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }
                ++$i;
            }

            self::agregarLeyenda($sheet, $i);

            //            //estableciendo anchos de columa
            //            for ($col = 65; $col <= 78; $col++) {
            //                $spreadsheet->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
            //            }

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(17);

            $sheet->setTitle('HistorialPersonasDesaparecidas');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    private static function agregarLeyenda($sheet, $i): void
    {
        ++$i;
        $leyenda = LeyendaEnum::DATOS_CONFIDENCIALES;

        $sheet->setCellValue('A'.$i, $leyenda);
        $sheet->mergeCells('A'.$i.':B'.$i);

        $styleArrayLeyenda = [
            'font' => [
                'bold' => true,
                'size' => '11',
                'color' => [
                    'rgb' => '000000',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'D3D3D3',
                ],
            ],
        ];

        $sheet->getStyle('A'.$i.':B'.$i)->applyFromArray($styleArrayLeyenda);
    }

    public static function regionesExp($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE REGIONES');
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'NOMBRE');
            $sheet->setCellValue('B5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:B5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:B5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:B5');

            $i = 6;
            //             /** @var Region[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getNombre());
                $sheet->setCellValue('B'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('Region');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Region.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function provinciasExp($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE PROVINCIAS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'PROVINCIA');
            $sheet->setCellValue('B5', 'REGION');
            $sheet->setCellValue('C5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:C5');

            $i = 6;
            //                         /** @var Provincia[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getRegion()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('Provincia');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Provincia.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function distritosExp($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE DISTRITOS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'DISTRITO');
            $sheet->setCellValue('B5', 'PROVINCIA');
            $sheet->setCellValue('C5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:C5');

            $i = 6;
            //                         /** @var Distrito[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getProvincia()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('Distrito');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Distrito.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function institucionesExp($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE INSTITUCIONES');
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'NOMBRE');
            $sheet->setCellValue('B5', 'Provincia/Distrito/Centro Poblado');
            $sheet->setCellValue('C5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:C5');

            $i = 6;
            //                         /** @var Distrito[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getName());
                $sheet->setCellValue('B'.$i, $dato->getProvincia()->getNombre().' / '.$dato->getDistrito()->getNombre().' / '.$dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('C'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn(3)->setAutoSize(false);
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn(3)->setWidth(16);

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('Distrito');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Distrito.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function centroPobladoExp($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE CENTROS POBLADOS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'C.UBIGEO');
            $sheet->setCellValue('B5', 'NOMBRE');
            $sheet->setCellValue('C5', 'CATEGORIA');
            $sheet->setCellValue('D5', 'DISTRITO');
            $sheet->setCellValue('E5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:E5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:E5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:E5');

            $i = 6;
            //                         /** @var CentroPoblado[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getCodigo());
                $sheet->setCellValue('B'.$i, $dato->getNombre());
                $sheet->setCellValue('C'.$i, $dato->getCategoria());
                $sheet->setCellValue('D'.$i, $dato->getDistrito()->getNombre());
                $sheet->setCellValue('E'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('CentroPoblado');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'CentroPoblado.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function blogExport($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE NOTICIAS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:F2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:F3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:F3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:F3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TITULO');
            $sheet->setCellValue('B5', 'DESCRIPCION');
            $sheet->setCellValue('C5', 'PROVINCIA');
            $sheet->setCellValue('D5', 'DISTRITO');
            $sheet->setCellValue('E5', 'CENTRO POBLADO');
            $sheet->setCellValue('F5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:F5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:F5');

            $i = 6;
            //                                     /** @var Blog[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getTitulo());
                $sheet->setCellValue('B'.$i, $dato->getDescripcion());
                $sheet->setCellValue('C'.$i, $dato->getProvincia()->getNombre());
                $sheet->setCellValue('D'.$i, $dato->getDistrito()->getNombre());
                $sheet->setCellValue('E'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('F'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('Noticia');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Noticia.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function usuarioExport($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE USUARIOS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:H2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:H3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:H3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:H3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'USUARIO');
            $sheet->setCellValue('B5', 'NOMBRE');
            $sheet->setCellValue('C5', 'EMAIL');
            $sheet->setCellValue('D5', 'ROLES');
            $sheet->setCellValue('E5', 'PROVINCIA');
            $sheet->setCellValue('F5', 'DISTRITO');
            $sheet->setCellValue('G5', 'CENTRO POBLADO');
            $sheet->setCellValue('H5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:H5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:H5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:H5');

            $i = 6;
            //                                     /** @var Usuario[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $rolesAsString = implode(', ', $dato->getRoles());
                $sheet->setCellValue('A'.$i, $dato->getUsername());
                $sheet->setCellValue('B'.$i, $dato->getFullName());
                $sheet->setCellValue('C'.$i, $dato->getEmail());
                $sheet->setCellValue('D'.$i, $rolesAsString);
                $sheet->setCellValue('E'.$i, $dato->getProvincia()->getNombre());
                $sheet->setCellValue('F'.$i, $dato->getDistrito()->getNombre());
                $sheet->setCellValue('G'.$i, $dato->getCentroPoblado()->getNombre());
                $sheet->setCellValue('H'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('Noticia');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Noticia.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function usuarioRolExport($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE ROLES');
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:D3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'NOMBRE');
            $sheet->setCellValue('B5', 'ROL');
            $sheet->setCellValue('C5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:C5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:C5');

            $i = 6;
            //                                     /** @var UsuarioRol[] $data */
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getNombre());
                $sheet->setCellValue('B'.$i, $dato->getRol());
                $sheet->setCellValue('C'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle('UsuarioRol');
            // $sheet->setTitle("Casos-" . self::getMes($mes)."-".$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'UsuarioRol.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }

    public static function generarExcel($data, $titulo, $nombreHoja, $nombreArchivo)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', $titulo);
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $fechaActual = date('Y-m-d');
            $fechaFormateada = self::formatFecha($fechaActual);
            $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.$fechaFormateada);
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:B3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // agregando logo tcuido
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('./img/tcuido_logo.png');
            $drawing->setWidth(95);
            $drawing->setHeight(75);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'NOMBRE');
            $sheet->setCellValue('B5', 'ACTIVO');

            // estilos para las cabeceras
            $styleArrayCabeceras = [
                'font' => [
                    'bold' => true,
                    'size' => '11',
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A5:B5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A5:B5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:B5');

            $i = 6;
            //  $sheet->setCellValue('A' . $i,"Hola");
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getNombre());
                $sheet->setCellValue('B'.$i, $dato->isActive());

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn(2)->setAutoSize(false);
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth(16);

            self::agregarLeyenda($sheet, $i);

            $sheet->setTitle($nombreHoja);
            $writer = new Xlsx($spreadsheet);
            $temp_file = tempnam(sys_get_temp_dir(), $nombreArchivo);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            return new Response($ex->getMessage());
        }
    }
}
