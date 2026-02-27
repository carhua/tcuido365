<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Traits;

trait TraitDate
{
    public static function listarMeses(): ?array
    {
        return ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre', ];
    }

    public static function getMes($mes)
    {
        switch ($mes) {
            case '1':
                return 'ENERO';
                break;
            case '2':
                return 'FEBRERO';
                break;
            case '3':
                return 'MARZO';
                break;
            case '4':
                return 'ABRIL';
                break;
            case '5':
                return 'MAYO';
                break;
            case '6':
                return 'JUNIO';
                break;
            case '7':
                return 'JULIO';
                break;
            case '8':
                return 'AGOSTO';
                break;
            case '9':
                return 'SETIEMBRE';
                break;
            case '10':
                return 'OCTUBRE';
                break;

            case '11':
                return 'NOVIEMBRE';
                break;
            case '12':
                return 'DICIEMBRE';
                break;
            default:
                // code...
                break;
        }
    }

    public static function listarAnios(): ?array
    {
        $anioInicial = 2020;
        $anioActual = date('Y');
        $lista = [];
        for ($i = $anioInicial; $i <= $anioActual; ++$i) {
            $lista[] = $i;
        }

        return $lista;
    }

    public static function exportMesesCv($casoscv): array
    {
        $data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 1; $i <= 12; ++$i) {
            foreach ($casoscv as $caso) {
                if ($caso['mes'] === $i) {
                    $data[$i - 1] = $caso['cantidad'];
                }
            }
        }

        return $data;
    }

    public static function exportColorExcel($value): ?string
    {
        $color = null;

        if ($value > 1 && $value <= 2) {
            $color = 'FAB378';
        }

        if ($value > 2) {
            $color = 'F99191';
        }

        return $color;
    }

    public static function formatFecha($fecha): ?string
    {
        $fechaNueva = new \DateTime($fecha);

        return $fechaNueva->format('d/m/Y');
    }

    public static function dataMeses(array $data): array
    {
        $items = [];
        $anios = [];
        foreach ($data as $value) {
            $items[$value['mes']][$value['anio']] = $value['cantidad'];
            $anios[$value['anio']] = (string) $value['anio'];
        }

        $mesNombres = [
            '1' => 'Ene',
            '2' => 'Feb',
            '3' => 'Mar',
            '4' => 'Abr',
            '5' => 'May',
            '6' => 'Jun',
            '7' => 'Jul',
            '8' => 'Ago',
            '9' => 'Set',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dic',
        ];

        $meses = [];
        $categoria = 'mes';
        foreach ($mesNombres as $mesId => $mesNombre) {
            $value = [];
            $existeValue = false;
            $value[$categoria] = $mesNombre;
            foreach ($anios as $anio) {
                if (isset($items[$mesId][$anio])) {
                    $value[$anio] = $items[$mesId][$anio];
                    $existeValue = true;
                }
            }
            if ($existeValue) {
                $meses[] = $value;
            }
        }

        return [
            'values' => $meses,
            'category' => $categoria,
            'series' => $anios,
        ];
    }

    public static function dataAnios(array $data): array
    {
        $items = [];
        foreach ($data as $value) {
            $items[$value['anio']] = ($items[$value['anio']] ?? 0) + (int) $value['cantidad'];
        }

        $values = [];
        foreach ($items as $anio => $item) {
            $values[] = [
                'anio' => (string) $anio,
                'cantidad' => $item,
            ];
        }

        return [
            'values' => $values,
            'categoryX' => 'anio',
            'valueY' => 'cantidad',
        ];
    }
}
