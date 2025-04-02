<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager;

use App\Entity\Base;
use App\Repository\BaseRepository;
use App\Service\Error;
use App\Service\Export\ExportExcel;
use App\Service\File\Downloader;
use App\Service\File\FileDownloadSingle;
use App\Utils\Paginator;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Pagerfanta\Pagerfanta;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class BaseManager
{
    protected $errors;

    abstract public function repository(): BaseRepository;

    public function list(array $queryValues, int $page): Pagerfanta
    {
        $params = Paginator::params($queryValues, $page);

        return $this->repository()->findLatest($params);
    }

    public function save(Base $entity): bool
    {
        return $this->repository()->save($entity);
    }

    public function saveUser(UserInterface $entity): bool
    {
        return $this->repository()->save($entity);
    }

    public function remove(Base $entity): bool
    {
        try {
            return $this->repository()->remove($entity);
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
            $this->errors[] = new Error('Error por ORM');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->errors[] = new Error('Error por Clave foranea');
        } catch (\Exception $e) {
            $this->errors[] = new Error($e->getMessage());
        }

        return false;
    }

    public function exportOfQuery(array $queryValues, array $headers, string $title, string $sheetTitle = 'info'): Response
    {
        $params = Paginator::params($queryValues);
        $items = $this->repository()->filter($params);

        return $this->export($items, $headers, $title, $sheetTitle);
    }

    public function export(array $items, array $headers, string $title, string $sheetTitle = 'info'): Response
    {
        try {
            $export = new ExportExcel($headers, $items, $title, $sheetTitle);
            $file = $export->fileDownload();
            $downloader = new Downloader(new FileDownloadSingle());

            return $downloader->down($file);
        } catch (SpreadsheetException $e) {
            $this->errors[] = new Error('Exportar fallo!!');
        }

        return new Response('Error');
    }

    public function addError(Error $error): void
    {
        $this->errors[] = $error;
    }

    public function errors(): array
    {
        return $this->errors ?? [];
    }
}
