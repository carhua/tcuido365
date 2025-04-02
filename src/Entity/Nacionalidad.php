<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\NacionalidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NacionalidadRepository::class)]
class Nacionalidad extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\OneToMany(targetEntity: Detenido::class, mappedBy: 'nacionalidad')]
    private $detenidos;

    #[ORM\OneToMany(targetEntity: Victima::class, mappedBy: 'nacionalidad')]
    private $victimas;

    #[ORM\OneToMany(targetEntity: Desaparecido::class, mappedBy: 'nacionalidad')]
    private $desaparecidos;

    public function __construct()
    {
        parent::__construct();
        $this->detenidos = new ArrayCollection();
        $this->victimas = new ArrayCollection();
        $this->desaparecidos = new ArrayCollection();
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

    /**
     * @return Collection|Detenido[]
     */
    public function getDetenidos(): Collection
    {
        return $this->detenidos;
    }

    public function addDetenido(Detenido $detenido): self
    {
        if (!$this->detenidos->contains($detenido)) {
            $this->detenidos[] = $detenido;
            $detenido->setNacionalidad($this);
        }

        return $this;
    }

    public function removeDetenido(Detenido $detenido): self
    {
        if ($this->detenidos->contains($detenido)) {
            $this->detenidos->removeElement($detenido);
            // set the owning side to null (unless already changed)
            if ($detenido->getNacionalidad() === $this) {
                $detenido->setNacionalidad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Victima[]
     */
    public function getVictimas(): Collection
    {
        return $this->victimas;
    }

    public function addVictima(Victima $victima): self
    {
        if (!$this->victimas->contains($victima)) {
            $this->victimas[] = $victima;
            $victima->setNacionalidad($this);
        }

        return $this;
    }

    public function removeVictima(Victima $victima): self
    {
        if ($this->victimas->contains($victima)) {
            $this->victimas->removeElement($victima);
            // set the owning side to null (unless already changed)
            if ($victima->getNacionalidad() === $this) {
                $victima->setNacionalidad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Desaparecido[]
     */
    public function getDesaparecidos(): Collection
    {
        return $this->desaparecidos;
    }

    public function addDesaparecido(Desaparecido $desaparecido): self
    {
        if (!$this->desaparecidos->contains($desaparecido)) {
            $this->desaparecidos[] = $desaparecido;
            $desaparecido->setNacionalidad($this);
        }

        return $this;
    }

    public function removeDesaparecido(Desaparecido $desaparecido): self
    {
        if ($this->desaparecidos->contains($desaparecido)) {
            $this->desaparecidos->removeElement($desaparecido);
            // set the owning side to null (unless already changed)
            if ($desaparecido->getNacionalidad() === $this) {
                $desaparecido->setNacionalidad(null);
            }
        }

        return $this;
    }
}
