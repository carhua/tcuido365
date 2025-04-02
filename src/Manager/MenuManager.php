<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Entity\Menu;
use App\Repository\BaseRepository;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

final class MenuManager extends BaseManager
{
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Menu::class);
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }

    /** NO VA A SER USADA */
    public function listPadres(array $queryValues, int $page): Pagerfanta
    {
        $params = Paginator::params($queryValues, $page);

        return $this->repository()->findListPadres($params);
    }
}
