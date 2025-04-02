<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoDesproteccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoDesproteccionRepository::class)]
class CasoDesproteccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $distrito;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class)]
    private $centroPoblado;

    #[ORM\Column(type: 'string', length: 100)]
    private $lugarCaso;

    #[ORM\Column(type: 'datetime')]
    private $fechaReporte;

    #[ORM\ManyToOne(targetEntity: SituacionEncontrada::class)]
    private $situacionEncontrada;

    #[ORM\Column(type: 'text')]
    private $descripcionReporte;

    #[ORM\OneToMany(targetEntity: DetalleCasoDesproteccion::class, mappedBy: 'casoDesproteccion')]
    private $detalleCasoDesproteccions;

    #[ORM\OneToMany(targetEntity: CasoDesproteccionMenorEdad::class, mappedBy: 'casoDesproteccion', cascade: ['persist'])]
    private $casoDesproteccionMenorEdads;

    #[ORM\OneToMany(targetEntity: CasoDesproteccionTutor::class, mappedBy: 'casoDesproteccion', cascade: ['persist'])]
    private $casoDesproteccionTutors;

    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private $institucion;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $codigoApp;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $usuarioApp;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $estadoCaso;

    #[ORM\Column(type: 'text', nullable: true)]
    private $situacionesEncontradas;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $latitud = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $longitud = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $codigo = null;

    #[ORM\Column(nullable: true)]
    private ?int $usuario_caso = null;

    public function __construct()
    {
        $this->detalleCasoDesproteccions = new ArrayCollection();
        $this->casoDesproteccionMenorEdads = new ArrayCollection();
        $this->casoDesproteccionTutors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDistrito(): ?string
    {
        return $this->distrito;
    }

    public function setDistrito(string $distrito): self
    {
        $this->distrito = $distrito;

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

    public function getLugarCaso(): ?string
    {
        return $this->lugarCaso;
    }

    public function setLugarCaso(string $lugarCaso): self
    {
        $this->lugarCaso = $lugarCaso;

        return $this;
    }

    public function getFechaReporte(): ?\DateTimeInterface
    {
        return $this->fechaReporte;
    }

    public function setFechaReporte(\DateTimeInterface $fechaReporte): self
    {
        $this->fechaReporte = $fechaReporte;

        return $this;
    }

    public function getSituacionEncontrada(): ?SituacionEncontrada
    {
        return $this->situacionEncontrada;
    }

    public function setSituacionEncontrada(?SituacionEncontrada $situacionEncontrada): self
    {
        $this->situacionEncontrada = $situacionEncontrada;

        return $this;
    }

    public function getDescripcionReporte(): ?string
    {
        return $this->descripcionReporte;
    }

    public function setDescripcionReporte(string $descripcionReporte): self
    {
        $this->descripcionReporte = $descripcionReporte;

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
            $detalleCasoDesproteccion->setCasoDesproteccion($this);
        }

        return $this;
    }

    public function removeDetalleCasoDesproteccion(DetalleCasoDesproteccion $detalleCasoDesproteccion): self
    {
        if ($this->detalleCasoDesproteccions->contains($detalleCasoDesproteccion)) {
            $this->detalleCasoDesproteccions->removeElement($detalleCasoDesproteccion);
            // set the owning side to null (unless already changed)
            if ($detalleCasoDesproteccion->getCasoDesproteccion() === $this) {
                $detalleCasoDesproteccion->setCasoDesproteccion(null);
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
            $casoDesproteccioMenorEdad->setCasoDesproteccion($this);
        }

        return $this;
    }

    public function removeCasoDesproteccioMenorEdad(CasoDesproteccionMenorEdad $casoDesproteccioMenorEdad): self
    {
        if ($this->casoDesproteccionMenorEdads->contains($casoDesproteccioMenorEdad)) {
            $this->casoDesproteccionMenorEdads->removeElement($casoDesproteccioMenorEdad);
            // set the owning side to null (unless already changed)
            if ($casoDesproteccioMenorEdad->getCasoDesproteccion() === $this) {
                $casoDesproteccioMenorEdad->setCasoDesproteccion(null);
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
            $casoDesproteccionTutor->setCasoDesproteccion($this);
        }

        return $this;
    }

    public function removeCasoDesproteccionTutor(CasoDesproteccionTutor $casoDesproteccionTutor): self
    {
        if ($this->casoDesproteccionTutors->contains($casoDesproteccionTutor)) {
            $this->casoDesproteccionTutors->removeElement($casoDesproteccionTutor);
            // set the owning side to null (unless already changed)
            if ($casoDesproteccionTutor->getCasoDesproteccion() === $this) {
                $casoDesproteccionTutor->setCasoDesproteccion(null);
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

    public function getUsuarioApp(): ?string
    {
        return $this->usuarioApp;
    }

    public function setUsuarioApp(?string $usuarioApp): self
    {
        $this->usuarioApp = $usuarioApp;

        return $this;
    }

    public function getEstadoCaso(): ?string
    {
        return $this->estadoCaso;
    }

    public function setEstadoCaso(?string $estadoCaso): self
    {
        $this->estadoCaso = $estadoCaso;

        return $this;
    }

    public function getSituacionesEncontradas(): ?string
    {
        return $this->situacionesEncontradas;
    }

    public function setSituacionesEncontradas(?string $situacionesEncontradas): self
    {
        $this->situacionesEncontradas = $situacionesEncontradas;

        return $this;
    }

    public function getLatitud(): ?string
    {
        return $this->latitud;
    }

    public function setLatitud(?string $latitud): static
    {
        $this->latitud = $latitud;

        return $this;
    }

    public function getLongitud(): ?string
    {
        return $this->longitud;
    }

    public function setLongitud(?string $longitud): static
    {
        $this->longitud = $longitud;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getUsuarioCaso(): ?int
    {
        return $this->usuario_caso;
    }

    public function setUsuarioCaso(?int $usuario_caso): static
    {
        $this->usuario_caso = $usuario_caso;

        return $this;
    }

    public function getInstitucion(): ?Institucion
    {
        return $this->institucion;
    }

    public function setInstitucion(?Institucion $institucion): self
    {
        $this->institucion = $institucion;

        return $this;
    }
}
