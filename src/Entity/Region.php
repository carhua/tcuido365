<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private $ubigeo;

    #[ORM\OneToMany(targetEntity: Provincia::class, mappedBy: 'region')]
    private $provincias;

    #[ORM\OneToMany(targetEntity: Usuario::class, mappedBy: 'region')]
    private $usuarios;

    public function __construct()
    {
        parent::__construct();
        $this->provincias = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): self
    {
        $this->ubigeo = $ubigeo;

        return $this;
    }

    /**
     * @return Collection|Provincia[]
     */
    public function getProvincias(): Collection
    {
        return $this->provincias;
    }

    public function addProvincia(Provincia $provincia): self
    {
        if (!$this->provincias->contains($provincia)) {
            $this->provincias[] = $provincia;
            $provincia->setRegion($this);
        }

        return $this;
    }

    public function removeProvincia(Provincia $provincia): self
    {
        // set the owning side to null (unless already changed)
        if ($this->provincias->removeElement($provincia) && $provincia->getRegion() === $this) {
            $provincia->setRegion(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNombre();
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setRegion($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        // set the owning side to null (unless already changed)
        if ($this->usuarios->removeElement($usuario) && $usuario->getRegion() === $this) {
            $usuario->setRegion(null);
        }

        return $this;
    }
}
