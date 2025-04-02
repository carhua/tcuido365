<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Twig;

use App\Entity\Parametro;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EstadoExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('estado', [$this, 'estadoFilter']),
            new TwigFilter('semaforo', [$this, 'semaforoFilter']),
        ];
    }

    public function estadoFilter(Parametro $value): string
    {
        $class = '';
        if ('P' === $value->getAlias()) {
            $class = 'warning';
        }
        if ('A' === $value->getAlias()) {
            $class = 'success';
        }
        if ('C' === $value->getAlias()) {
            $class = 'danger';
        }

        return '<small><span class="kt-badge kt-badge--inline kt-badge--'.$class.'">'.$value->getNombre().'</span></small>';
    }

    public function semaforoFilter($value): string
    {
        $color = '';

        if ($value > 1 && $value <= 2) {
            $color = 'table-warning';
        }

        if ($value > 2) {
            $color = 'table-danger';
        }

        return $color;
    }
}
