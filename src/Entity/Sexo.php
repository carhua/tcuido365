<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\SexoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SexoRepository::class)]
class Sexo extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\OneToMany(targetEntity: Detenido::class, mappedBy: 'sexo')]
    private $detenidos;

    #[ORM\OneToMany(targetEntity: Victima::class, mappedBy: 'sexo')]
    private $victimas;

    #[ORM\OneToMany(targetEntity: DenuncianteDesaparecido::class, mappedBy: 'sexo')]
    private $denuncianteDesaparecidos;

    #[ORM\OneToMany(targetEntity: Desaparecido::class, mappedBy: 'sexo')]
    private $desaparecidos;

    public function __construct()
    {
        parent::__construct();
        $this->detenidos = new ArrayCollection();
        $this->victimas = new ArrayCollection();
        $this->denuncianteDesaparecidos = new ArrayCollection();
        $this->desaparecidos = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getNombre();
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
            $detenido->setSexo($this);
        }

        return $this;
    }

    public function removeDetenido(Detenido $detenido): self
    {
        if ($this->detenidos->contains($detenido)) {
            $this->detenidos->removeElement($detenido);
            // set the owning side to null (unless already changed)
            if ($detenido->getSexo() === $this) {
                $detenido->setSexo(null);
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
            $victima->setSexo($this);
        }

        return $this;
    }

    public function removeVictima(Victima $victima): self
    {
        if ($this->victimas->contains($victima)) {
            $this->victimas->removeElement($victima);
            // set the owning side to null (unless already changed)
            if ($victima->getSexo() === $this) {
                $victima->setSexo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DenuncianteDesaparecido[]
     */
    public function getDenuncianteDesaparecidos(): Collection
    {
        return $this->denuncianteDesaparecidos;
    }

    public function addDenuncianteDesaparecido(DenuncianteDesaparecido $denuncianteDesaparecido): self
    {
        if (!$this->denuncianteDesaparecidos->contains($denuncianteDesaparecido)) {
            $this->denuncianteDesaparecidos[] = $denuncianteDesaparecido;
            $denuncianteDesaparecido->setSexo($this);
        }

        return $this;
    }

    public function removeDenuncianteDesaparecido(DenuncianteDesaparecido $denuncianteDesaparecido): self
    {
        if ($this->denuncianteDesaparecidos->contains($denuncianteDesaparecido)) {
            $this->denuncianteDesaparecidos->removeElement($denuncianteDesaparecido);
            // set the owning side to null (unless already changed)
            if ($denuncianteDesaparecido->getSexo() === $this) {
                $denuncianteDesaparecido->setSexo(null);
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
            $desaparecido->setSexo($this);
        }

        return $this;
    }

    public function removeDesaparecido(Desaparecido $desaparecido): self
    {
        if ($this->desaparecidos->contains($desaparecido)) {
            $this->desaparecidos->removeElement($desaparecido);
            // set the owning side to null (unless already changed)
            if ($desaparecido->getSexo() === $this) {
                $desaparecido->setSexo(null);
            }
        }

        return $this;
    }
}
