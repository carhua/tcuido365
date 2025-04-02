<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Security;

use App\Entity\Usuario;
use App\Entity\UsuarioPermiso;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class Security
{
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const USER_SUPER_ADMIN = 'admin';
    public const LIST = 'list';
    public const VIEW = 'view';
    public const NEW = 'new';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const PRINT = 'print';
    public const EXPORT = 'export';
    public const IMPORT = 'import';
    public const MASTER = 'master';

    private $tokenStorage;
    private $entityManager;
    /** @var Usuario */
    private $user;
    private $access = [];
    private $isSuperAdmin;
    private $subject;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function setUser(Usuario $user): void
    {
        $this->user = $user;
    }

    public function denyAccessUnlessGranted(
        string $attribute,
        string $subject,
        object $object = null,
        string $message = 'Acceso denegado...'
    ): void {
        if (!$this->hasAccess($attribute, $subject, $object)) {
            $exception = new AccessDeniedException($message);
            $exception->setAttributes([$attribute]);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    public function isAuthenticate(): bool
    {
        return $this->user() instanceof \App\Entity\Usuario;
    }

    public function user(): ?Usuario
    {
        if (null !== $this->user) {
            return $this->user;
        }

        if (null !== ($token = $this->tokenStorage->getToken())) {
            $this->user = $token->getUser();
        }

        return $this->user;
    }

    public function hasAccess(string $attribute, string $subject, object $object = null): bool
    {
        $this->subject = $subject;

        return $this->has($attribute, $object);
    }

    public function has(string $attribute, object $object = null, string $subject = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $subject = $subject ?? $this->subject;

        $access = $this->access();

        if (null === $subject || !isset($access[$subject])) {
            return false;
        }

        return $access[$subject][self::MASTER] || ($access[$subject][$attribute] && $this->owner($object));
    }

    public function owner(?object $object): bool
    {
        if (null === $object || null === $object->config()) {
            return true;
        }

        $propietarioId = $object->propietario() ? $object->propietario()->getId() : 0;
        $usuario = $this->user();

        return $propietarioId === $usuario['id'];
    }

    private function access(): array
    {
        if ($this->isSuperAdmin()) {
            return [];
        }

        if (0 !== \count($this->access)) {
            return $this->access;
        }

        $permisssions = $this->getPermissions();

        $this->access = [];
        foreach ($permisssions as $permission) {
            $this->access[$permission['route']] = $permission;
        }

        return $this->access;
    }

    private function getPermissions(): array
    {
        $usuario = $this->user();
        if ($usuario instanceof \App\Entity\Usuario) {
            return $this->entityManager->getRepository(UsuarioPermiso::class)->findPermisosByUsuarioIdAndRuta($usuario->getId());
        }

        return [];
    }

    public function isSuperAdmin(): bool
    {
        if (null === $this->isSuperAdmin) {
            $this->isSuperAdmin = $this->isGranted(self::ROLE_SUPER_ADMIN);
        }

        return $this->isSuperAdmin;
    }

    public function isGranted(string $role): bool
    {
        $usuario = $this->user();
        if (!$usuario instanceof \App\Entity\Usuario) {
            return false;
        }

        foreach ($usuario->getUsuarioRoles() as $rol) {
            if ($role === $rol->getRol()) {
                return true;
            }
        }

        return false;
    }

    public function entityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function repository(string $className): ObjectRepository
    {
        return $this->entityManager()->getRepository($className);
    }

    public function list(): bool
    {
        return $this->has(self::LIST);
    }

    public function view(): bool
    {
        return $this->has(self::VIEW);
    }

    public function new(): bool
    {
        return $this->has(self::NEW);
    }

    public function edit(): bool
    {
        return $this->has(self::EDIT);
    }

    public function delete(): bool
    {
        return $this->has(self::DELETE);
    }

    public function print(): bool
    {
        return $this->has(self::PRINT);
    }

    public function export(): bool
    {
        return $this->has(self::EXPORT);
    }

    public function import(): bool
    {
        return $this->has(self::IMPORT);
    }

    public function master(): bool
    {
        return $this->has(self::MASTER);
    }
}
