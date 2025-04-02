<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\TutorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorRepository::class)]
class Tutor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class)]
    private $tipoDocumento;

    #[ORM\Column(type: 'string', length: 100)]
    private $numeroDocumento;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombres;

    #[ORM\Column(type: 'string', length: 100)]
    private $apellidos;

    #[ORM\ManyToOne(targetEntity: Sexo::class, inversedBy: 'detenidos')]
    private $sexo;

    #[ORM\ManyToOne(targetEntity: VinculoFamiliar::class)]
    private $vinculoFamiliar;

    #[ORM\OneToMany(targetEntity: DetalleCasoDesproteccion::class, mappedBy: 'tutor')]
    private $detalleCasoDesproteccions;

    #[ORM\OneToMany(targetEntity: CasoDesproteccionTutor::class, mappedBy: 'tutor')]
    private $casoDesproteccionTutors;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $telefono;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $codigoApp;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'tutors')]
    private $centroPoblado;

    #[ORM\ManyToOne(targetEntity: Persona::class, inversedBy: 'tutors', cascade: ['persist'])]
    private $persona;

    public function __construct()
    {
        $this->detalleCasoDesproteccions = new ArrayCollection();
        $this->casoDesproteccionTutors = new ArrayCollection();
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

    public function getNumeroDocumento(): ?string
    {
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento(string $numeroDocumento): self
    {
        $this->numeroDocumento = $numeroDocumento;

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

    public function getSexo(): ?Sexo
    {
        return $this->sexo;
    }

    public function setSexo(?Sexo $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getVinculoFamiliar(): ?VinculoFamiliar
    {
        return $this->vinculoFamiliar;
    }

    public function setVinculoFamiliar(?VinculoFamiliar $vinculoFamiliar): self
    {
        $this->vinculoFamiliar = $vinculoFamiliar;

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
            $detalleCasoDesproteccion->setTutor($this);
        }

        return $this;
    }

    public function removeDetalleCasoDesproteccion(DetalleCasoDesproteccion $detalleCasoDesproteccion): self
    {
        if ($this->detalleCasoDesproteccions->contains($detalleCasoDesproteccion)) {
            $this->detalleCasoDesproteccions->removeElement($detalleCasoDesproteccion);
            // set the owning side to null (unless already changed)
            if ($detalleCasoDesproteccion->getTutor() === $this) {
                $detalleCasoDesproteccion->setTutor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CasoDesproteccionTutor[]
     */
    public function getCasoDesproteccionTutors(): Collection
    {
        return $this->casoDesproteccionTutors;
    }

    public function addCasoDesproteccionTutor(CasoDesproteccionTutor $casoDesproteccionTutor): self
    {
        if (!$this->casoDesproteccionTutors->contains($casoDesproteccionTutor)) {
            $this->casoDesproteccionTutors[] = $casoDesproteccionTutor;
            $casoDesproteccionTutor->setTutor($this);
        }

        return $this;
    }

    public function removeCasoDesproteccionTutor(CasoDesproteccionTutor $casoDesproteccionTutor): self
    {
        if ($this->casoDesproteccionTutors->contains($casoDesproteccionTutor)) {
            $this->casoDesproteccionTutors->removeElement($casoDesproteccionTutor);
            // set the owning side to null (unless already changed)
            if ($casoDesproteccionTutor->getTutor() === $this) {
                $casoDesproteccionTutor->setTutor(null);
            }
        }

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

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
