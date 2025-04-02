<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service;

final class Error
{
    private $message;
    private $code;
    private $detail;

    public function __construct(string $message, int $code = 0, string $detail = '')
    {
        $this->message = $message;
        $this->code = $code;
        $this->detail = $detail;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function detail(): string
    {
        return $this->detail;
    }
}
