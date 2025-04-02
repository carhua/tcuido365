<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MenuExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('build_menu', [MenuRuntime::class, 'buildMenu']),
        ];
    }
}
