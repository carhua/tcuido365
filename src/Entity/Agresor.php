<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\AgresorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgresorRepository::class)]
class Agresor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class)]
    private $tipoDocumento;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $numeroDocumento;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $nombres;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $apellidos;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $edad;

    #[ORM\ManyToOne(targetEntity: Sexo::class, inversedBy: 'detenidos')]
    private $sexo;

    #[ORM\ManyToOne(targetEntity: EstadoCivil::class)]
    private $estadoCivil;

    #[ORM\ManyToOne(targetEntity: Nacionalidad::class)]
    private $nacionalidad;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $direccion;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $telefono;

    #[ORM\ManyToOne(targetEntity: VinculoFamiliar::class)]
    private $vinculoFamiliar;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private $email;

    #[ORM\OneToMany(targetEntity: DetalleCasoViolencia::class, mappedBy: 'agresor')]
    private $detalleCasoViolencias;

    #[ORM\OneToMany(targetEntity: CasoViolenciaAgresor::class, mappedBy: 'agresor')]
    private $casoViolenciaAgresors;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $codigoApp;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'agresors')]
    private $centroPoblado;

    #[ORM\ManyToOne(targetEntity: Persona::class, inversedBy: 'agresors', cascade: ['persist'])]
    private $persona;

    public function __construct()
    {
        $this->detalleCasoViolencias = new ArrayCollection();
        $this->casoViolenciaAgresors = new ArrayCollection();
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

    public function setNumeroDocumento(?string $numeroDocumento): self
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

    public function getEdad(): ?string
    {
        return $this->edad;
    }

    public function setEdad(string $edad): self
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

    public function getEstadoCivil(): ?EstadoCivil
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil(?EstadoCivil $estadoCivil): self
    {
        $this->estadoCivil = $estadoCivil;

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

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
     * @return Collection|DetalleCasoViolencia[]
     */
    public function getDetalleCasoViolencias(): Collection
    {
        return $this->detalleCasoViolencias;
    }

    public function addDetalleCasoViolencia(DetalleCasoViolencia $detalleCasoViolencia): self
    {
        if (!$this->detalleCasoViolencias->contains($detalleCasoViolencia)) {
            $this->detalleCasoViolencias[] = $detalleCasoViolencia;
            $detalleCasoViolencia->setAgresor($this);
        }

        return $this;
    }

    public function removeDetalleCasoViolencia(DetalleCasoViolencia $detalleCasoViolencia): self
    {
        if ($this->detalleCasoViolencias->contains($detalleCasoViolencia)) {
            $this->detalleCasoViolencias->removeElement($detalleCasoViolencia);
            // set the owning side to null (unless already changed)
            if ($detalleCasoViolencia->getAgresor() === $this) {
                $detalleCasoViolencia->setAgresor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CasoViolenciaAgresor[]
     */
    public function getCasoViolenciaAgresors(): Collection
    {
        return $this->casoViolenciaAgresors;
    }

    public function addCasoViolenciaAgresor(CasoViolenciaAgresor $casoViolenciaAgresor): self
    {
        if (!$this->casoViolenciaAgresors->contains($casoViolenciaAgresor)) {
            $this->casoViolenciaAgresors[] = $casoViolenciaAgresor;
            $casoViolenciaAgresor->setAgresor($this);
        }

        return $this;
    }

    public function removeCasoViolenciaAgresor(CasoViolenciaAgresor $casoViolenciaAgresor): self
    {
        if ($this->casoViolenciaAgresors->contains($casoViolenciaAgresor)) {
            $this->casoViolenciaAgresors->removeElement($casoViolenciaAgresor);
            // set the owning side to null (unless already changed)
            if ($casoViolenciaAgresor->getAgresor() === $this) {
                $casoViolenciaAgresor->setAgresor(null);
            }
        }

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
