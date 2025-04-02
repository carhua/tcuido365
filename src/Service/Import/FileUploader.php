<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service\Import;

use App\Dto\FileDto;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FileUploader
{
    public function __construct(
        private string $targetDirectoryUploader,
        private SluggerInterface $slugger
    ) {
    }

    public function up(UploadedFile $file, string $path = null): FileDto
    {
        $fileName = $file->getClientOriginalName();
        $originalFilename = pathinfo($fileName, \PATHINFO_FILENAME);
        $originalExtension = pathinfo($fileName, \PATHINFO_EXTENSION);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$originalExtension;
        $path = $this->targetDirectory().($path ?? '');

        try {
            $file->move(
                $path,
                $newFilename
            );
        } catch (FileException) {
            throw new \RuntimeException('Error upload file');
        }

        return new FileDto($newFilename, $path, $file);
    }

    public function down(FileDto $file): void
    {
        $filenamePath = $file->path().'/'.$file->name();
        if (file_exists($filenamePath)) {
            unlink($filenamePath);
        }
    }

    public function targetDirectory(): string
    {
        return $this->targetDirectoryUploader;
    }
}
