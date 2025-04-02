<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\UsuarioPermisoRepository')]
class UsuarioPermiso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private $id;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $listar;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $mostrar;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $crear;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $editar;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $eliminar;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $imprimir;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $exportar;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $importar;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $maestro;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Menu')]
    private $menu;

    #[ORM\ManyToMany(targetEntity: 'App\Entity\UsuarioRol', inversedBy: 'permisos')]
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getListar(): ?bool
    {
        return $this->listar;
    }

    public function setListar(?bool $listar): self
    {
        $this->listar = $listar;

        return $this;
    }

    public function getMostrar(): ?bool
    {
        return $this->mostrar;
    }

    public function setMostrar(?bool $mostrar): self
    {
        $this->mostrar = $mostrar;

        return $this;
    }

    public function getCrear(): ?bool
    {
        return $this->crear;
    }

    public function setCrear(?bool $crear): self
    {
        $this->crear = $crear;

        return $this;
    }

    public function getEditar(): ?bool
    {
        return $this->editar;
    }

    public function setEditar(?bool $editar): self
    {
        $this->editar = $editar;

        return $this;
    }

    public function getEliminar(): ?bool
    {
        return $this->eliminar;
    }

    public function setEliminar(?bool $eliminar): self
    {
        $this->eliminar = $eliminar;

        return $this;
    }

    public function getImprimir(): ?bool
    {
        return $this->imprimir;
    }

    public function setImprimir(?bool $imprimir): self
    {
        $this->imprimir = $imprimir;

        return $this;
    }

    public function getExportar(): ?bool
    {
        return $this->exportar;
    }

    public function setExportar(?bool $exportar): self
    {
        $this->exportar = $exportar;

        return $this;
    }

    public function getImportar(): ?bool
    {
        return $this->exportar;
    }

    public function setImportar(?bool $exportar): self
    {
        $this->exportar = $exportar;

        return $this;
    }

    public function getMaestro(): ?bool
    {
        return $this->maestro;
    }

    public function setMaestro(?bool $maestro): self
    {
        $this->maestro = $maestro;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function addRole(UsuarioRol $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    public function removeRole(UsuarioRol $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * @return Collection|UsuarioRol[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }
}
