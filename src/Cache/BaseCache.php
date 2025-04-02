<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Cache;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

abstract class BaseCache
{
    public const CACHE_TIME = 36000; // 36000 //10 horas
    public const CACHE_TIME_SHORT = 3600; // 3600; // 1 hora
    public const CACHE_TIME_LONG = 864000; // 864000; // 10 dias

    private bool $active = true;

    public function __construct(private TagAwareCacheInterface $cache)
    {
    }

    public function get(string $key, callable $callback, array $tags = [], int $time = self::CACHE_TIME)
    {
        if (false === $this->active) {
            return \call_user_func($callback);
        }

        $tags = array_merge($tags, $this->tags());

        return $this->cache->get($key, function (ItemInterface $item) use ($callback, $tags, $time) {
            $item->tag($tags);
            $item->expiresAfter($time);

            return \call_user_func($callback);
        });
    }

    public function delete(string $key): bool
    {
        try {
            return $this->cache->delete($key);
        } catch (InvalidArgumentException $e) {
        }

        return false;
    }

    public function deleteTags(array $tags): bool
    {
        try {
            return $this->cache->invalidateTags($tags);
        } catch (InvalidArgumentException $e) {
        }

        return false;
    }

    public function update(): bool
    {
        return $this->deleteTags($this->tags());
    }

    abstract public function tags(): array;

    public function enable(): void
    {
        $this->active = true;
    }

    public function disable(): void
    {
        $this->active = false;
    }
}
