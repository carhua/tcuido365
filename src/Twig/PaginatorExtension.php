<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Twig;

use Pagerfanta\Pagerfanta;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class PaginatorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('index', [$this, 'indexFilter']),
            new TwigFilter('indexReverse', [$this, 'indexReverseFilter']),
        ];
    }

    public function indexFilter(int $index, Pagerfanta $paginator): int
    {
        return $index + ($paginator->getCurrentPage() - 1) * $paginator->getMaxPerPage();
    }

    public function indexReverseFilter(int $index, Pagerfanta $paginator): int
    {
        return ($paginator->count() + 1) - ($index + ($paginator->getCurrentPage() - 1) * $paginator->getCurrentPage());
    }
}
