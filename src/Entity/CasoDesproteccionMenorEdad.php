<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoDesproteccionMenorEdadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoDesproteccionMenorEdadRepository::class)]
class CasoDesproteccionMenorEdad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CasoDesproteccion::class, inversedBy: 'casoDesproteccionMenorEdads')]
    private $casoDesproteccion;

    #[ORM\ManyToOne(targetEntity: MenorEdad::class, inversedBy: 'casoDesproteccionMenorEdads', cascade: ['persist'])]
    private $menorEdad;

    #[ORM\Column(type: 'text', nullable: true)]
    private $situacionesEncontradas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCasoDesproteccion(): ?CasoDesproteccion
    {
        return $this->casoDesproteccion;
    }

    public function setCasoDesproteccion(?CasoDesproteccion $casoDesproteccion): self
    {
        $this->casoDesproteccion = $casoDesproteccion;

        return $this;
    }

    public function getMenorEdad(): ?MenorEdad
    {
        return $this->menorEdad;
    }

    public function setMenorEdad(?MenorEdad $menorEdad): self
    {
        $this->menorEdad = $menorEdad;

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
}
