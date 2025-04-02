<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoViolenciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoViolenciaRepository::class)]
class CasoViolencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: TipoMaltrato::class)]
    private $tipoMaltrato;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class)]
    private $centroPoblado;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private $lugarMaltrato;

    #[ORM\Column(type: 'datetime')]
    private $fechaReporte;

    #[ORM\Column(type: 'text', nullable: true)]
    private $descripcionReporte;

    #[ORM\OneToMany(targetEntity: DetalleCasoViolencia::class, mappedBy: 'casoViolencia')]
    private $detalleCasoViolencias;

    #[ORM\OneToMany(targetEntity: CasoViolenciaDenunciante::class, mappedBy: 'casoViolencia', cascade: ['persist'])]
    private $casoViolenciaDenunciantes;

    #[ORM\OneToMany(targetEntity: CasoViolenciaAgraviado::class, mappedBy: 'casoViolencia', cascade: ['persist'])]
    private $casoViolenciaAgraviados;

    #[ORM\OneToMany(targetEntity: CasoViolenciaAgresor::class, mappedBy: 'casoViolencia', cascade: ['persist'])]
    private $casoViolenciaAgresors;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $distrito;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $codigoApp;

    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private $institucion;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $usuarioApp;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $estadoCaso;

    #[ORM\Column(type: 'text', nullable: true)]
    private $tipoMaltratos;

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
        $this->detalleCasoViolencias = new ArrayCollection();
        $this->casoViolenciaDenunciantes = new ArrayCollection();
        $this->casoViolenciaAgraviados = new ArrayCollection();
        $this->casoViolenciaAgresors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipoMaltrato(): ?TipoMaltrato
    {
        return $this->tipoMaltrato;
    }

    public function setTipoMaltrato(?TipoMaltrato $tipoMaltrato): self
    {
        $this->tipoMaltrato = $tipoMaltrato;

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

    public function getInstitucion(): ?Institucion
    {
        return $this->institucion;
    }

    public function setInstitucion(?Institucion $institucion): self
    {
        $this->institucion = $institucion;

        return $this;
    }

    public function getLugarMaltrato(): ?string
    {
        return $this->lugarMaltrato;
    }

    public function setLugarMaltrato(?string $lugarMaltrato): self
    {
        $this->lugarMaltrato = $lugarMaltrato;

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

    public function getDescripcionReporte(): ?string
    {
        return $this->descripcionReporte;
    }

    public function setDescripcionReporte(?string $descripcionReporte): self
    {
        $this->descripcionReporte = $descripcionReporte;

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
            $detalleCasoViolencia->setCasoViolencia($this);
        }

        return $this;
    }

    public function removeDetalleCasoViolencia(DetalleCasoViolencia $detalleCasoViolencia): self
    {
        if ($this->detalleCasoViolencias->contains($detalleCasoViolencia)) {
            $this->detalleCasoViolencias->removeElement($detalleCasoViolencia);
            // set the owning side to null (unless already changed)
            if ($detalleCasoViolencia->getCasoViolencia() === $this) {
                $detalleCasoViolencia->setCasoViolencia(null);
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
            $casoViolenciaDenunciante->setCasoViolencia($this);
        }

        return $this;
    }

    public function removeCasoViolenciaDenunciante(CasoViolenciaDenunciante $casoViolenciaDenunciante): self
    {
        if ($this->casoViolenciaDenunciantes->contains($casoViolenciaDenunciante)) {
            $this->casoViolenciaDenunciantes->removeElement($casoViolenciaDenunciante);
            // set the owning side to null (unless already changed)
            if ($casoViolenciaDenunciante->getCasoViolencia() === $this) {
                $casoViolenciaDenunciante->setCasoViolencia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CasoViolenciaAgraviado[]
     */
    public function getCasoViolenciaAgraviados(): Collection
    {
        return $this->casoViolenciaAgraviados;
    }

    public function addCasoViolenciaAgraviado(CasoViolenciaAgraviado $casoViolenciaAgraviado): self
    {
        if (!$this->casoViolenciaAgraviados->contains($casoViolenciaAgraviado)) {
            $this->casoViolenciaAgraviados[] = $casoViolenciaAgraviado;
            $casoViolenciaAgraviado->setCasoViolencia($this);
        }

        return $this;
    }

    public function removeCasoViolenciaAgraviado(CasoViolenciaAgraviado $casoViolenciaAgraviado): self
    {
        if ($this->casoViolenciaAgraviados->contains($casoViolenciaAgraviado)) {
            $this->casoViolenciaAgraviados->removeElement($casoViolenciaAgraviado);
            // set the owning side to null (unless already changed)
            if ($casoViolenciaAgraviado->getCasoViolencia() === $this) {
                $casoViolenciaAgraviado->setCasoViolencia(null);
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
            $casoViolenciaAgresor->setCasoViolencia($this);
        }

        return $this;
    }

    public function removeCasoViolenciaAgresor(CasoViolenciaAgresor $casoViolenciaAgresor): self
    {
        if ($this->casoViolenciaAgresors->contains($casoViolenciaAgresor)) {
            $this->casoViolenciaAgresors->removeElement($casoViolenciaAgresor);
            // set the owning side to null (unless already changed)
            if ($casoViolenciaAgresor->getCasoViolencia() === $this) {
                $casoViolenciaAgresor->setCasoViolencia(null);
            }
        }

        return $this;
    }

    public function getDistrito(): ?string
    {
        return $this->distrito;
    }

    public function setDistrito(?string $distrito): self
    {
        $this->distrito = $distrito;

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

    public function getTipoMaltratos(): ?string
    {
        return $this->tipoMaltratos;
    }

    public function setTipoMaltratos(?string $tipoMaltratos): self
    {
        $this->tipoMaltratos = $tipoMaltratos;

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

}
