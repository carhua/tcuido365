<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $titulo;

    #[ORM\Column(type: 'text')]
    private $descripcion;

    #[ORM\ManyToOne(targetEntity: Adjunto::class, inversedBy: 'blogs', cascade: ['persist'])]
    private $adjunto;

    #[ORM\ManyToOne(targetEntity: Provincia::class, inversedBy: 'blogs')]
    private $provincia;

    #[ORM\ManyToOne(targetEntity: Distrito::class, inversedBy: 'blogs')]
    private $distrito;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'blogs')]
    private $centroPoblado;

    #[ORM\Column(type: 'string', nullable: true)]
    private $tipo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getAdjunto(): ?Adjunto
    {
        return $this->adjunto;
    }

    public function setAdjunto(?Adjunto $adjunto): self
    {
        $this->adjunto = $adjunto;

        return $this;
    }

    public function getDistrito(): ?Distrito
    {
        return $this->distrito;
    }

    public function setDistrito(?Distrito $distrito): self
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

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo): void
    {
        $this->tipo = $tipo;
    }
}
