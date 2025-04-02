<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoDesaparecidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoDesaparecidoRepository::class)]
class CasoDesaparecido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $distrito;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'casoDesaparecidos')]
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

    #[ORM\OneToMany(targetEntity: DenuncianteDesaparecido::class, mappedBy: 'casoDesaparecido', cascade: ['persist', 'remove'])]
    private $denuncianteDesaparecidos;

    #[ORM\OneToMany(targetEntity: Desaparecido::class, mappedBy: 'casoDesaparecido', cascade: ['persist', 'remove'])]
    private $desaparecidos;

    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private $institucion;

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
        $this->denuncianteDesaparecidos = new ArrayCollection();
        $this->desaparecidos = new ArrayCollection();
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

    public function setDescripcionReporte(string $descripcionReporte): self
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
            $denuncianteDesaparecido->setCasoDesaparecido($this);
        }

        return $this;
    }

    public function removeDenuncianteDesaparecido(DenuncianteDesaparecido $denuncianteDesaparecido): self
    {
        if ($this->denuncianteDesaparecidos->contains($denuncianteDesaparecido)) {
            $this->denuncianteDesaparecidos->removeElement($denuncianteDesaparecido);
            // set the owning side to null (unless already changed)
            if ($denuncianteDesaparecido->getCasoDesaparecido() === $this) {
                $denuncianteDesaparecido->setCasoDesaparecido(null);
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
            $desaparecido->setCasoDesaparecido($this);
        }

        return $this;
    }

    public function removeDesaparecido(Desaparecido $desaparecido): self
    {
        if ($this->desaparecidos->contains($desaparecido)) {
            $this->desaparecidos->removeElement($desaparecido);
            // set the owning side to null (unless already changed)
            if ($desaparecido->getCasoDesaparecido() === $this) {
                $desaparecido->setCasoDesaparecido(null);
            }
        }

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
