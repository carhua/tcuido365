<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoTrataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoTrataRepository::class)]
class CasoTrata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $distrito;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'casoTratas')]
    private $centroPoblado;

    #[ORM\Column(type: 'datetime')]
    private $fechaReporte;

    #[ORM\Column(type: 'text', nullable: true)]
    private $descripcionReporte;

    #[ORM\Column(type: 'string', length: 100)]
    private $codigoApp;

    #[ORM\Column(type: 'string', length: 100)]
    private $usuarioApp;

    #[ORM\Column(type: 'string', length: 100)]
    private $estadoCaso;

    #[ORM\OneToMany(targetEntity: Detenido::class, mappedBy: 'casoTrata', cascade: ['persist'])]
    private $detenidos;

    #[ORM\OneToMany(targetEntity: Victima::class, mappedBy: 'casoTrata', cascade: ['persist'])]
    private $victimas;

    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private $institucion;

    #[ORM\Column(type: 'text', nullable: true)]
    private $tipoExplotacionesGeneral;

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
        $this->detenidos = new ArrayCollection();
        $this->victimas = new ArrayCollection();
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

    public function getCodigoApp(): ?string
    {
        return $this->codigoApp;
    }

    public function setCodigoApp(string $codigoApp): self
    {
        $this->codigoApp = $codigoApp;

        return $this;
    }

    public function getUsuarioApp(): ?string
    {
        return $this->usuarioApp;
    }

    public function setUsuarioApp(string $usuarioApp): self
    {
        $this->usuarioApp = $usuarioApp;

        return $this;
    }

    public function getEstadoCaso(): ?string
    {
        return $this->estadoCaso;
    }

    public function setEstadoCaso(string $estadoCaso): self
    {
        $this->estadoCaso = $estadoCaso;

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
            $detenido->setCasoTrata($this);
        }

        return $this;
    }

    public function removeDetenido(Detenido $detenido): self
    {
        if ($this->detenidos->contains($detenido)) {
            $this->detenidos->removeElement($detenido);
            // set the owning side to null (unless already changed)
            if ($detenido->getCasoTrata() === $this) {
                $detenido->setCasoTrata(null);
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
            $victima->setCasoTrata($this);
        }

        return $this;
    }

    public function removeVictima(Victima $victima): self
    {
        if ($this->victimas->contains($victima)) {
            $this->victimas->removeElement($victima);
            // set the owning side to null (unless already changed)
            if ($victima->getCasoTrata() === $this) {
                $victima->setCasoTrata(null);
            }
        }

        return $this;
    }

    public function getTipoExplotacionesGeneral(): ?string
    {
        return $this->tipoExplotacionesGeneral;
    }

    public function setTipoExplotacionesGeneral(?string $tipoExplotacionesGeneral): self
    {
        $this->tipoExplotacionesGeneral = $tipoExplotacionesGeneral;

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
