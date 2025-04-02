<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\MenorEdadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenorEdadRepository::class)]
class MenorEdad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class)]
    private $tipoDocumento;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombres;

    #[ORM\Column(type: 'string', length: 100)]
    private $apellidos;

    #[ORM\Column(type: 'integer')]
    private $edad;

    #[ORM\ManyToOne(targetEntity: Sexo::class, inversedBy: 'detenidos')]
    private $sexo;

    #[ORM\ManyToOne(targetEntity: Nacionalidad::class)]
    private $nacionalidad;

    #[ORM\Column(type: 'string', length: 200)]
    private $direccion;

    #[ORM\OneToMany(targetEntity: DetalleCasoDesproteccion::class, mappedBy: 'menorEdad')]
    private $detalleCasoDesproteccions;

    #[ORM\OneToMany(targetEntity: CasoDesproteccionMenorEdad::class, mappedBy: 'menorEdad')]
    private $casoDesproteccionMenorEdads;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $numeroDocumento;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $codigoApp;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'menorEdads')]
    private $centroPoblado;

    #[ORM\ManyToOne(targetEntity: Persona::class, inversedBy: 'menorEdads', cascade: ['persist'])]
    private $persona;

    public function __construct()
    {
        $this->detalleCasoDesproteccions = new ArrayCollection();
        $this->casoDesproteccionMenorEdads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipoDocumento(): ?TipoDocumento
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?TipoDocumento $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): self
    {
        $this->edad = $edad;

        return $this;
    }

    public function getSexo(): ?Sexo
    {
        return $this->sexo;
    }

    public function setSexo(?Sexo $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getNacionalidad(): ?Nacionalidad
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(?Nacionalidad $nacionalidad): self
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * @return Collection|DetalleCasoDesproteccion[]
     */
    public function getDetalleCasoDesproteccions(): Collection
    {
        return $this->detalleCasoDesproteccions;
    }

    public function addDetalleCasoDesproteccion(DetalleCasoDesproteccion $detalleCasoDesproteccion): self
    {
        if (!$this->detalleCasoDesproteccions->contains($detalleCasoDesproteccion)) {
            $this->detalleCasoDesproteccions[] = $detalleCasoDesproteccion;
            $detalleCasoDesproteccion->setMenorEdad($this);
        }

        return $this;
    }

    public function removeDetalleCasoDesproteccion(DetalleCasoDesproteccion $detalleCasoDesproteccion): self
    {
        if ($this->detalleCasoDesproteccions->contains($detalleCasoDesproteccion)) {
            $this->detalleCasoDesproteccions->removeElement($detalleCasoDesproteccion);
            // set the owning side to null (unless already changed)
            if ($detalleCasoDesproteccion->getMenorEdad() === $this) {
                $detalleCasoDesproteccion->setMenorEdad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CasoDesproteccionMenorEdad[]
     */
    public function getCasoDesproteccioMenorEdads(): Collection
    {
        return $this->casoDesproteccionMenorEdads;
    }

    public function addCasoDesproteccioMenorEdad(CasoDesproteccionMenorEdad $casoDesproteccioMenorEdad): self
    {
        if (!$this->casoDesproteccionMenorEdads->contains($casoDesproteccioMenorEdad)) {
            $this->casoDesproteccionMenorEdads[] = $casoDesproteccioMenorEdad;
            $casoDesproteccioMenorEdad->setMenorEdad($this);
        }

        return $this;
    }

    public function removeCasoDesproteccioMenorEdad(CasoDesproteccionMenorEdad $casoDesproteccioMenorEdad): self
    {
        if ($this->casoDesproteccionMenorEdads->contains($casoDesproteccioMenorEdad)) {
            $this->casoDesproteccionMenorEdads->removeElement($casoDesproteccioMenorEdad);
            // set the owning side to null (unless already changed)
            if ($casoDesproteccioMenorEdad->getMenorEdad() === $this) {
                $casoDesproteccioMenorEdad->setMenorEdad(null);
            }
        }

        return $this;
    }

    public function getNumeroDocumento(): ?string
    {
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento(?string $numeroDocumento): self
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    public function getCodigoApp(): ?string
    {
        return $this->codigoApp;
    }

    public function setCodigoApp(?string $codigoApp): self
    {
        $this->codigoApp = $codigoApp;

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

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): self
    {
        $this->persona = $persona;

        return $this;
    }
}
