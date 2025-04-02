<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service\File;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class FileDownloadSingle implements FileDownload
{
    public function down(array $files, string $filename)
    {
        if (1 !== \count($files)) {
            throw new \RuntimeException('Not support');
        }

        $file = $files[0];
        $response = new BinaryFileResponse($file->path());
        $response->setContentDisposition(FileDownload::DISPOSITION_ATTACHMENT, $filename);

        return $response;
    }
}
