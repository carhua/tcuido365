<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Utils;

class Generator
{
    public static function code(int $length = 6): string
    {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = mb_strlen($pattern) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $key .= $pattern[mt_rand(0, $max)];
        }

        return $key;
    }

    public static function slugify(string $string): string
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }

    public static function join(?string $pre, ?string $num, int $preCount = 4, int $numCount = 6): ?string
    {
        if (null === $pre || null === $num) {
            return null;
        }

        return mb_str_pad($pre, $preCount, '0', \STR_PAD_LEFT).'-'.
            mb_str_pad($num, $numCount, '0', \STR_PAD_LEFT);
    }

    public static function serialNumber($number, int $numCount = 6)
    {
        if (null === $number) {
            return null;
        }

        return mb_str_pad($number, $numCount, '0', \STR_PAD_LEFT);
    }

    public static function split(string $num, int $position = 1): ?string
    {
        $divide = explode('-', $num);
        if ($position >= 0 && $position < \count($divide)) {
            return $divide[$position];
        }

        return null;
    }

    public static function withoutWhiteSpaces(?string $text): ?string
    {
        return str_replace(' ', '', $text);
    }
}
