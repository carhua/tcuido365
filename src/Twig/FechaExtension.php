<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FechaExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('fecha', [$this, 'fechaFilter']),
            new TwigFilter('fechamedia', [$this, 'fechaMediaFilter']),
            new TwigFilter('fechalarga', [$this, 'fechaLargaFilter']),
        ];
    }

    public function fechaFilter(?\DateTimeInterface $fecha, ?string $format = 'd-m-Y'): string
    {
        if (!$fecha instanceof \DateTimeInterface) {
            return '';
        }

        return $fecha->format($format);
    }

    public function fechaMediaFilter(?\DateTimeInterface $fecha): string
    {
        if (!$fecha instanceof \DateTimeInterface) {
            return '';
        }

        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        return $fecha->format('d').' de '.mb_strtoupper($meses[$fecha->format('n') - 1]).' del '.$fecha->format('Y');
    }

    public function fechaLargaFilter(?\DateTimeInterface $fecha): string
    {
        if (!$fecha instanceof \DateTimeInterface) {
            return '';
        }

        $dias = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        return $dias[$fecha->format('w')].' '.$fecha->format('d').' de '.$meses[$fecha->format('n') - 1].' del '.$fecha->format('Y');
    }
}
