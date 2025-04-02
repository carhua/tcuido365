<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service\File;

use App\Entity\Adjunto;

final class FileUpload
{
    private $file;

    public function file(): ?Adjunto
    {
        return $this->file;
    }

    public function setFile(Adjunto $file): self
    {
        $this->file = $file;

        return $this;
    }
}
