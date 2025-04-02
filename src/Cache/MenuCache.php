<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Cache;

final class MenuCache extends BaseCache
{
    public const KEY_MENU_CACHE = '__MENU__';
    public const KEY_MENU_PERMISSION = '_PERMISS_';
    public const KEY_MENU_BUILD = '_BUILD_';

    public function permisos(callable $callback)
    {
        return $this->get(self::KEY_MENU_PERMISSION, $callback);
    }

    public function menus(callable $callback)
    {
        return $this->get(self::KEY_MENU_BUILD, $callback);
    }

    public function tags(): array
    {
        return [self::KEY_MENU_CACHE];
    }
}
