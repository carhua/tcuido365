<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Entity\Traits\CreatedUpdatedTrait;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class Base
{
    use CreatedUpdatedTrait;
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Usuario')]
    #[ORM\JoinColumn(nullable: true)]
    protected $owner;

    #[ORM\Column(type: 'boolean')]
    #[Groups('default')]
    protected $isActive = true;

    #[ORM\Column(type: 'uuid')]
    protected $uuid;

    public function __construct()
    {
        $this->updatedDatetime();
        $this->uuid = Uuid::uuid4();
    }

    public function owner(): ?Usuario
    {
        return $this->owner;
    }

    /** @param Usuario|UserInterface|null $owner */
    public function setOwner(?Usuario $owner): void
    {
        $this->owner = $owner;
    }

    public function uuid(): string
    {
        if (null === $this->uuid) {
            $this->uuid = Uuid::uuid4();
        }

        return $this->uuid->toString();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function enable(): void
    {
        $this->isActive = true;
    }

    public function disable(): void
    {
        $this->isActive = false;
    }

    public function changeActive(): bool
    {
        $this->isActive = !$this->isActive;

        return $this->isActive;
    }
}
