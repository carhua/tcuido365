<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\AccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccionRepository::class)]
class Accion extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $codigo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?int $estado_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $institucion_id = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne(targetEntity: Institucion::class, inversedBy: 'acciones')]
    private ?Institucion $institucion = null;

    #[ORM\ManyToOne(targetEntity: Estado::class, inversedBy: 'acciones')]
    private ?Estado $estado = null;

    public function getInstitucion(): ?Institucion
    {
        return $this->institucion;
    }

    public function setInstitucion(?Institucion $institucion): self
    {
        $this->institucion = $institucion;

        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEstadoId(): ?int
    {
        return $this->estado_id;
    }

    public function setEstadoId(int $estado_id): static
    {
        $this->estado_id = $estado_id;

        return $this;
    }

    public function getInstitucionId(): ?int
    {
        return $this->institucion_id;
    }

    public function setInstitucionId(?int $institucion_id): static
    {
        $this->institucion_id = $institucion_id;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}
