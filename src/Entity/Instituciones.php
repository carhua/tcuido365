<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\InstitucionesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstitucionesRepository::class)]
class Instituciones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column]
    private ?int $region_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $provincia_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $distrito_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $centro_poblado_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getRegionId(): ?int
    {
        return $this->region_id;
    }

    public function setRegionId(int $region_id): static
    {
        $this->region_id = $region_id;

        return $this;
    }

    public function getProvinciaId(): ?int
    {
        return $this->provincia_id;
    }

    public function setProvinciaId(?int $provincia_id): static
    {
        $this->provincia_id = $provincia_id;

        return $this;
    }

    public function getDistritoId(): ?int
    {
        return $this->distrito_id;
    }

    public function setDistritoId(?int $distrito_id): static
    {
        $this->distrito_id = $distrito_id;

        return $this;
    }

    public function getCentroPobladoId(): ?int
    {
        return $this->centro_poblado_id;
    }

    public function setCentroPobladoId(?int $centro_poblado_id): static
    {
        $this->centro_poblado_id = $centro_poblado_id;

        return $this;
    }
}
