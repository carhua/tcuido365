<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Utils;

class Math
{
    public static function round(?float $value, int $decimal = 2): float
    {
        return round($value, $decimal, \PHP_ROUND_HALF_DOWN);
    }

    public static function number(?float $value, int $decimal = 2, bool $comma = true): string
    {
        if (null === $value || false === is_numeric($value)) {
            return '0'; // return ''; 07052019
        }

        if ($comma) {
            return number_format($value, $decimal);
        }

        return number_format($value, $decimal, '.', ''); // sin separador de miles ,
    }

    public static function percentage(?float $value, ?float $total, int $decimal = 2): float
    {
        if (null === $total || 0 === $total) {
            return 0;
        }

        return round(100 * $value / $total, $decimal, \PHP_ROUND_HALF_DOWN);
    }
}
