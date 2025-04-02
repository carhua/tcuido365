<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Entity\TipoDocumento;
use App\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;

final class TipoDocumentoManager extends BaseManager
{
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(TipoDocumento::class);
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}
