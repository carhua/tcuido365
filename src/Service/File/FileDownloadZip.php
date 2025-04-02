<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Core\Adjunto\Infrastructure\File;

use Pidia\Core\Adjunto\Domain\File;
use Pidia\Core\Adjunto\Domain\FileDownload;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

use function Symfony\Component\String\u;

final class FileDownloadZip implements FileDownload
{
    public function down(array $files, string $filename)
    {
        $filename = $this->generateFileName($filename);
        $file = $this->generateZip($files, $filename);

        $response = new BinaryFileResponse($file->path());
        $response->setContentDisposition(FileDownload::DISPOSITION_ATTACHMENT, $filename);

        return $response;
    }

    private function generateZip(array $files, string $filename): File
    {
        $zip = new \ZipArchive();
        $tempNamezip = tempnam(sys_get_temp_dir(), $filename);

        if (true === $zip->open($tempNamezip)) { // , ZIPARCHIVE::CREATE
            $tempFiles = [];
            foreach ($files as $file) {
                $path = realpath($file->path());
                if (file_exists($path)) {
                    $newFile = (string) sys_get_temp_dir().'/'.$file->name();
                    copy($path, $newFile);
                    $zip->addFile($newFile, $file->name());
                    $tempFiles[] = $newFile;
                }
            }
            $zip->close();

            // Delete files temp
            foreach ($tempFiles as $tempFile) {
                unlink($tempFile);
            }
        }

        return new File($filename, $tempNamezip);
    }

    private function generateFileName(string $fileName): string
    {
        if (false === u($fileName)->endsWith('.zip')) {
            return $fileName.'.zip';
        }

        return $fileName;
    }
}
