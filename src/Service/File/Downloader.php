<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service\File;

final class Downloader
{
    private $download;

    public function __construct(FileDownload $download)
    {
        $this->download = $download;
    }

    /** @param File[]|File $files */
    public function down($files, string $fileName = '')
    {
        $files = \is_array($files) ? $files : [$files];
        $fileName = ('' !== $fileName) ? $fileName : $files[0]->name();

        return $this->download->down($files, $fileName);
    }
}
