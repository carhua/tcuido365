<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\UsuarioRolRepository')]
class UsuarioRol extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 30)]
    private $rol;

    #[ORM\ManyToMany(targetEntity: 'App\Entity\Usuario', mappedBy: 'usuarioRoles')]
    private $usuarios;

    #[ORM\ManyToMany(targetEntity: 'App\Entity\UsuarioPermiso', mappedBy: 'roles', cascade: ['persist', 'remove'])]
    private $permisos;

    public function __construct()
    {
        parent::__construct();
        $this->permisos = new ArrayCollection();
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
        $this->nombre = trim($nombre);

        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): self
    {
        $this->rol = trim($rol);

        return $this;
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
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->contains($usuario)) {
            $this->usuarios->removeElement($usuario);
        }

        return $this;
    }

    /**
     * @return Collection|UsuarioPermiso[]
     */
    public function getPermisos(): Collection
    {
        return $this->permisos;
    }

    public function addPermiso(UsuarioPermiso $permiso): self
    {
        if (!$this->permisos->contains($permiso)) {
            $permiso->addRole($this);
            $this->permisos[] = $permiso;
        }

        return $this;
    }

    public function removePermiso(UsuarioPermiso $permiso): self
    {
        if ($this->permisos->contains($permiso)) {
            $this->permisos->removeElement($permiso);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNombre();
    }
}
