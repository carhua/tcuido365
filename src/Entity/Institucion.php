<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\InstitucionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstitucionRepository::class)]
class Institucion extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $direccion = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $telefono = null;

    #[ORM\Column]
    private ?int $region_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $provincia_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $distrito_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $centro_poblado_id = null;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'instituciones')]
    private ?Region $region = null;

    #[ORM\ManyToOne(targetEntity: Provincia::class, inversedBy: 'instituciones')]
    private ?Provincia $provincia = null;

    #[ORM\ManyToOne(targetEntity: Distrito::class, inversedBy: 'instituciones')]
    private ?Distrito $distrito = null;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'usuarios')]
    private ?CentroPoblado $centroPoblado;

    #[ORM\OneToMany(targetEntity: Usuario::class, mappedBy: 'institucion')]
    private $usuarios;

    public function __construct()
    {
        parent::__construct();
        $this->usuarios = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): static
    {
        $this->telefono = $telefono;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getDistrito(): ?Distrito
    {
        return $this->distrito;
    }

    public function setDistrito(?Distrito $distrito): self
    {
        $this->distrito = $distrito;

        return $this;
    }

    public function getCentroPoblado(): ?CentroPoblado
    {
        return $this->centroPoblado;
    }

    public function setCentroPoblado(?CentroPoblado $centroPoblado): self
    {
        $this->centroPoblado = $centroPoblado;

        return $this;
    }

    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setInstitucion($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        // set the owning side to null (unless already changed)
        if ($this->usuarios->removeElement($usuario) && $usuario->getInstitucion() === $this) {
            $usuario->setInstitucion(null);
        }

        return $this;
    }
}
