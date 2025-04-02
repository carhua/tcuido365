<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\DenuncianteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DenuncianteRepository::class)]
class Denunciante implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class)]
    private $tipoDocumento;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $numeroDocumento;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $nombres;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $apellidos;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $edad;

    #[ORM\ManyToOne(targetEntity: Sexo::class, inversedBy: 'detenidos')]
    private $sexo;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $telefono;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private $email;

    #[ORM\OneToMany(targetEntity: DetalleCasoViolencia::class, mappedBy: 'denunciante')]
    private $detalleCasoViolencias;

    #[ORM\OneToMany(targetEntity: CasoViolenciaDenunciante::class, mappedBy: 'denunciante')]
    private $casoViolenciaDenunciantes;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $codigoApp;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'denunciantes')]
    private $centroPoblado;

    #[ORM\ManyToOne(targetEntity: Persona::class, inversedBy: 'denunciantes', cascade: ['persist'])]
    private $persona;

    public function __construct()
    {
        $this->detalleCasoViolencias = new ArrayCollection();
        $this->casoViolenciaDenunciantes = new ArrayCollection();
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

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(?int $edad): self
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
            $detalleCasoViolencia->setDenunciante($this);
        }

        return $this;
    }

    public function removeDetalleCasoViolencia(DetalleCasoViolencia $detalleCasoViolencia): self
    {
        if ($this->detalleCasoViolencias->contains($detalleCasoViolencia)) {
            $this->detalleCasoViolencias->removeElement($detalleCasoViolencia);
            // set the owning side to null (unless already changed)
            if ($detalleCasoViolencia->getDenunciante() === $this) {
                $detalleCasoViolencia->setDenunciante(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CasoViolenciaDenunciante[]
     */
    public function getCasoViolenciaDenunciantes(): Collection
    {
        return $this->casoViolenciaDenunciantes;
    }

    public function addCasoViolenciaDenunciante(CasoViolenciaDenunciante $casoViolenciaDenunciante): self
    {
        if (!$this->casoViolenciaDenunciantes->contains($casoViolenciaDenunciante)) {
            $this->casoViolenciaDenunciantes[] = $casoViolenciaDenunciante;
            $casoViolenciaDenunciante->setDenunciante($this);
        }

        return $this;
    }

    public function removeCasoViolenciaDenunciante(CasoViolenciaDenunciante $casoViolenciaDenunciante): self
    {
        if ($this->casoViolenciaDenunciantes->contains($casoViolenciaDenunciante)) {
            $this->casoViolenciaDenunciantes->removeElement($casoViolenciaDenunciante);
            // set the owning side to null (unless already changed)
            if ($casoViolenciaDenunciante->getDenunciante() === $this) {
                $casoViolenciaDenunciante->setDenunciante(null);
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

    public function jsonSerialize(): array
    {
        return [
            'nombres' => $this->getNombres(),
            'apellidos' => $this->getApellidos(),
            'tipoDocumento' => $this->getTipoDocumento()->getNombre(),
            'numeroDocumento' => $this->getNumeroDocumento(),
            'edad' => $this->getEdad(),
            'sexo' => $this->getSexo(),
            'telefono' => $this->getTelefono(),
            'email' => $this->getEmail(),
        ];
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
