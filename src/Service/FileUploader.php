<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $secure = sha1(uniqid(mt_rand(), true));
        // Archivo con extension
        //        $secure = sha1(uniqid(mt_rand(), true)).'.'.$file->getClientOriginalExtension();

        try {
            $file->move($this->getTargetDirectory(), $secure);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $secure;
    }

    public function remove(string $nombre): void
    {
        if (null === $nombre || '' === trim($nombre)) {
            return;
        }

        $file = $this->getTargetDirectory().$nombre;

        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
