<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Traits;

use App\Entity\Agraviado;
use App\Entity\CasoViolencia;
use App\Entity\Denunciante;
use App\Entity\Persona;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;

trait TraitExcel
{
    use TraitDate;

    public static function casosViolencia($data, $anio, $mes)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE CASOS DE VIOLENCIA');
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

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Casos-'.self::getMes($mes).'-'.$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporteKardex.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            echo ' '.$ex->getMessage();
        }
    }

    public static function casosDesproteccion($data, $anio, $mes)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE CASOS DE RIESGO DE DESPROTECCION');
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

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Casos-'.self::getMes($mes).'-'.$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            echo ' '.$ex->getMessage();
        }
    }

    public static function casosTrata($data, $anio, $mes)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE CASOS DE TRATA DE PERSONAS');
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

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Casos-'.self::getMes($mes).'-'.$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            echo ' '.$ex->getMessage();
        }
    }

    public static function casosDesaparecido($data, $anio, $mes)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'REPORTE DE CASOS DE PERSONAS DESAPARECIDAS');
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
                $sheet->setCellValue('C'.$i, $dato->getFechaReporte()->format('d/m/Y H:i'));
                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('Casos-'.self::getMes($mes).'-'.$anio);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            echo ' '.$ex->getMessage();
        }
    }

    public static function listDenunciante($data, $centroPoblado)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
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
            echo ' '.$ex->getMessage();
        }
    }

    public static function listAgraviados($data, $anio, $mes)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
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
            echo ' '.$ex->getMessage();
        }
    }

    public static function historialCasosViolencia($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'HISTORIAL DE CASOS DE VIOLENCIA');
            $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
            $sheet->setCellValue('A2', 'Listado de personas con la cantidad de casos de violencia reportados');
            $spreadsheet->getActiveSheet()->mergeCells('A2:I2');
            $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
            $spreadsheet->getActiveSheet()->mergeCells('A3:I3');

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

            $spreadsheet->getActiveSheet()->getStyle('A1:I3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:I3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('B5', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('C5', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('D5', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('E5', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('F4', 'CASOS DE VIOLENCIA REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('F4:I4');
            $sheet->setCellValue('F5', 'DENUNCIANTE');
            $sheet->setCellValue('G5', 'AGRESOR');
            $sheet->setCellValue('H5', 'AGRAVIADO');
            $sheet->setCellValue('I5', 'TOTAL');
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
            $spreadsheet->getActiveSheet()->getStyle('A4:I5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A4:I5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:I5');

            $i = 6;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('B'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('C'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('D'.$i, $dato->getEdad());
                $sheet->setCellValue('E'.$i, $dato->getSexo());
                $sheet->setCellValue('F'.$i, \count($dato->getDenunciantes()));
                $sheet->setCellValue('G'.$i, \count($dato->getAgresors()));
                $sheet->setCellValue('H'.$i, \count($dato->getAgraviados()));
                $sheet->setCellValue('I'.$i, '=F'.$i.'+ G'.$i.'+ H'.$i);
                $number1 = $spreadsheet->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('G'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('H'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }
                ++$i;
            }

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
            echo ' '.$ex->getMessage();
        }
    }

    public static function historialCasosDesproteccion($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'HISTORIAL DE CASOS DE RIESGO DE DESPROTECCION');
            $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
            $sheet->setCellValue('A2', 'Listado de personas con la cantidad de casos de riesgo de desproteccion reportados');
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

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('B5', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('C5', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('D5', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('E5', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('F4', 'CASOS DE RIESGO DE DESPROTECCION REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('F4:H4');
            $sheet->setCellValue('F5', 'MENOR EDAD');
            $sheet->setCellValue('G5', 'TUTOR');
            $sheet->setCellValue('H5', 'TOTAL');

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
            $spreadsheet->getActiveSheet()->getStyle('A4:H5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A4:H5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:H5');

            $i = 6;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('B'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('C'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('D'.$i, $dato->getEdad());
                $sheet->setCellValue('E'.$i, $dato->getSexo());
                $sheet->setCellValue('F'.$i, \count($dato->getMenorEdads()));
                $sheet->setCellValue('G'.$i, \count($dato->getTutors()));
                $sheet->setCellValue('H'.$i, '=F'.$i.'+ G'.$i);

                $number1 = $spreadsheet->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('F'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('G'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('HistorialRiesgoDesproteccion');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            echo ' '.$ex->getMessage();
        }
    }

    public static function historialTipoViolencia($data, $tipoPersona, $centro)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */

            // $spreadsheet->setActiveSheetIndex($j);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'HISTORIAL DE CASOS POR TIPO DE MALTRATO');
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $sheet->setCellValue('A2', 'Tipo Persona: '.(1 === $tipoPersona ? 'Agresor' : 'Agraviado').' -  Centro Poblado: '.$centro);
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

            $spreadsheet->getActiveSheet()->getStyle('A1:J3')->applyFromArray($styleArrayTitulo);
            $spreadsheet->getActiveSheet()->getStyle('A1:J3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('B5', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('C5', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('D5', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('E5', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            if (1 === $tipoPersona) {
                $sheet->setCellValue('F4', 'TIPOS DE MALTRARO QUE HA COMETIDO');
            } else {
                $sheet->setCellValue('F4', 'TIPOS DE MALTRARO QUE HA RECIBIDO');
            }

            $spreadsheet->getActiveSheet()->mergeCells('F4:J4');
            $sheet->setCellValue('F5', 'PSICOLOGICO');
            $sheet->setCellValue('G5', 'FISICO');
            $sheet->setCellValue('H5', 'SEXUAL');
            $sheet->setCellValue('I5', 'ECONOMICO/PATRIMONIAL');
            $sheet->setCellValue('J5', 'TOTAL');

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
            $spreadsheet->getActiveSheet()->getStyle('A4:J5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A4:J5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:J5');

            $i = 6;
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato['tipoDocumento']);
                $sheet->setCellValue('B'.$i, $dato['numeroDocumento']);
                $sheet->setCellValue('C'.$i, $dato['nombres'].' '.$dato['apellidos']);
                $sheet->setCellValue('D'.$i, $dato['edad']);
                $sheet->setCellValue('E'.$i, $dato['sexo']);
                $sheet->setCellValue('F'.$i, $dato['tipoPsicologico']);
                $sheet->setCellValue('G'.$i, $dato['tipoFisico']);
                $sheet->setCellValue('H'.$i, $dato['tipoSexual']);
                $sheet->setCellValue('I'.$i, $dato['tipoEconomico']);
                $sheet->setCellValue('J'.$i, '=F'.$i.'+ G'.$i.'+ H'.$i.' +I'.$i);
                $number = $spreadsheet->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();

                // aplicando colores al excel
                $color = self::exportColorExcel(number_format($number));
                if (null !== $color) {
                    $spreadsheet->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color);
                }
                ++$i;
            }

            // estableciendo bordes a las filas
            $styleBorders = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('A6:J'.$i)->applyFromArray($styleBorders);

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
            echo ' '.$ex->getMessage();
        }
    }

    public static function historialCasosTrata($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'HISTORIAL DE CASOS DE TRATA DE PERSONAS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
            $sheet->setCellValue('A2', 'Listado de personas con la cantidad de casos de trata de personas reportados');
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

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('B5', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('C5', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('D5', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('E5', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('F4', 'CASOS DE TRATA DE PERSONAS REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('F4:H4');
            $sheet->setCellValue('F5', 'DETENIDO');
            $sheet->setCellValue('G5', 'VICTIMA');
            $sheet->setCellValue('H5', 'TOTAL');

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
            $spreadsheet->getActiveSheet()->getStyle('A4:H5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A4:H5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:H5');

            $i = 6;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('B'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('C'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('D'.$i, $dato->getEdad());
                $sheet->setCellValue('E'.$i, $dato->getSexo());
                $sheet->setCellValue('F'.$i, \count($dato->getDetenidos()));
                $sheet->setCellValue('G'.$i, \count($dato->getVictimas()));
                $sheet->setCellValue('H'.$i, '=F'.$i.'+ G'.$i);

                $number1 = $spreadsheet->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('F'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('G'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }

                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('HistorialTrataPersonas');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            echo ' '.$ex->getMessage();
        }
    }

    public static function historialCasosDesaparecidos($data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            /* @var $sheet Worksheet */
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'HISTORIAL DE CASOS DE PERSONAS DESAPARECIDAS');
            $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
            $sheet->setCellValue('A2', 'Listado de personas con la cantidad de casos de personas desaparecidas reportados');
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

            // establecer las cabeceras
            $sheet->setCellValue('A5', 'TIPO DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
            $sheet->setCellValue('B5', 'Nº DOCUMENTO');
            // $spreadsheet->getActiveSheet()->mergeCells('B5:B6');
            $sheet->setCellValue('C5', 'NOMBRES Y APELLIDOS');
            // $spreadsheet->getActiveSheet()->mergeCells('C5:C6');
            $sheet->setCellValue('D5', 'EDAD');
            // $spreadsheet->getActiveSheet()->mergeCells('D5:D6');
            $sheet->setCellValue('E5', 'SEXO');
            // $spreadsheet->getActiveSheet()->mergeCells('E5:E6');
            $sheet->setCellValue('F4', 'CASOS DE TRATA DE PERSONAS REPORTADOS');
            $spreadsheet->getActiveSheet()->mergeCells('F4:H4');
            $sheet->setCellValue('F5', 'DENUNCIANTE');
            $sheet->setCellValue('G5', 'DESAPARECIDO');
            $sheet->setCellValue('H5', 'TOTAL');

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
            $spreadsheet->getActiveSheet()->getStyle('A4:H5')->applyFromArray($styleArrayCabeceras);
            $spreadsheet->getActiveSheet()->getStyle('A4:H5')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('215967');
            $spreadsheet->getActiveSheet()->setAutoFilter('A5:H5');

            $i = 6;
            /** @var Persona[] $data */
            foreach ($data as $dato) {
                $sheet->setCellValue('A'.$i, $dato->getTipoDocumento());
                $sheet->setCellValue('B'.$i, $dato->getNumeroDocumento());
                $sheet->setCellValue('C'.$i, $dato->getNombres().' '.$dato->getApellidos());
                $sheet->setCellValue('D'.$i, $dato->getEdad());
                $sheet->setCellValue('E'.$i, $dato->getSexo());
                $sheet->setCellValue('F'.$i, \count($dato->getDenuncianteDesaparecidos()));
                $sheet->setCellValue('G'.$i, \count($dato->getDesaparecidos()));
                $sheet->setCellValue('H'.$i, '=F'.$i.'+ G'.$i);

                $number1 = $spreadsheet->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $number2 = $spreadsheet->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

                $color1 = self::exportColorExcel(number_format($number1));
                if (null !== $color1) {
                    $spreadsheet->getActiveSheet()->getStyle('F'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color1);
                }

                $color2 = self::exportColorExcel(number_format($number2));
                if (null !== $color2) {
                    $spreadsheet->getActiveSheet()->getStyle('G'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color2);
                }
                ++$i;
            }

            // estableciendo anchos de columa
            for ($col = 65; $col <= 78; ++$col) {
                $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
            }

            $sheet->setTitle('HistorialPersonasDesaparecidas');
            $writer = new Xlsx($spreadsheet);
            $fileName = 'reporte.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return $temp_file;
        } catch (\Exception $ex) {
            echo ' '.$ex->getMessage();
        }
    }
}
