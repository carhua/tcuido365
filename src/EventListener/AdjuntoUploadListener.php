<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\EventListener;

use App\Entity\Adjunto;
use App\Service\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class AdjuntoUploadListener
{
    public function __construct(private FileUploader $uploader)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->uploadFile($entity);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->removeFile($entity);
    }

    private function uploadFile($entity): void
    {
        if (!$entity instanceof Adjunto) {
            return;
        }

        $file = $entity->getFile();

        if ($file instanceof UploadedFile) {
            $secure = $this->uploader->upload($file);
            $this->uploader->remove($entity->getTmpNombre());
            $entity->setSecure($secure);
        } elseif ($file instanceof File) {
            $entity->setNombre($file->getFilename()); // verificar
        }
    }

    private function removeFile($entity): void
    {
        if (!$entity instanceof Adjunto) {
            return;
        }

        $nombre = $entity->path();
        $this->uploader->remove($nombre);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Adjunto) {
            return;
        }

        if ($fileName = $entity->getNombre()) {
            $entity->setNombre(new File($this->uploader->getTargetDirectory().'/'.$fileName));
        }
    }
}
