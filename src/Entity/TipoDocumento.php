<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\TipoDocumentoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipoDocumentoRepository::class)]
class TipoDocumento extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $nombre;

    #[ORM\OneToMany(targetEntity: Victima::class, mappedBy: 'tipoDocumento')]
    private $victimas;

    #[ORM\OneToMany(targetEntity: Detenido::class, mappedBy: 'tipoDocumento')]
    private $detenidos;

    #[ORM\OneToMany(targetEntity: DenuncianteDesaparecido::class, mappedBy: 'tipoDocumento')]
    private $denuncianteDesaparecidos;

    public function __construct()
    {
        parent::__construct();
        $this->victimas = new ArrayCollection();
        $this->detenidos = new ArrayCollection();
        $this->denuncianteDesaparecidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
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
            $victima->setTipoDocumento($this);
        }

        return $this;
    }

    public function removeVictima(Victima $victima): self
    {
        if ($this->victimas->contains($victima)) {
            $this->victimas->removeElement($victima);
            // set the owning side to null (unless already changed)
            if ($victima->getTipoDocumento() === $this) {
                $victima->setTipoDocumento(null);
            }
        }

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
            $detenido->setTipoDocumento($this);
        }

        return $this;
    }

    public function removeDetenido(Detenido $detenido): self
    {
        if ($this->detenidos->contains($detenido)) {
            $this->detenidos->removeElement($detenido);
            // set the owning side to null (unless already changed)
            if ($detenido->getTipoDocumento() === $this) {
                $detenido->setTipoDocumento(null);
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
            $denuncianteDesaparecido->setTipoDocumento($this);
        }

        return $this;
    }

    public function removeDenuncianteDesaparecido(DenuncianteDesaparecido $denuncianteDesaparecido): self
    {
        if ($this->denuncianteDesaparecidos->contains($denuncianteDesaparecido)) {
            $this->denuncianteDesaparecidos->removeElement($denuncianteDesaparecido);
            // set the owning side to null (unless already changed)
            if ($denuncianteDesaparecido->getTipoDocumento() === $this) {
                $denuncianteDesaparecido->setTipoDocumento(null);
            }
        }

        return $this;
    }
}
