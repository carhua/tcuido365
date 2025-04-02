<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\DetalleCasoDesproteccionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetalleCasoDesproteccionRepository::class)]
class DetalleCasoDesproteccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CasoDesproteccion::class, inversedBy: 'detalleCasoDesproteccions')]
    private $casoDesproteccion;

    #[ORM\ManyToOne(targetEntity: MenorEdad::class, inversedBy: 'detalleCasoDesproteccions')]
    private $menorEdad;

    #[ORM\ManyToOne(targetEntity: Tutor::class, inversedBy: 'detalleCasoDesproteccions')]
    private $tutor;

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

    public function getTutor(): ?Tutor
    {
        return $this->tutor;
    }

    public function setTutor(?Tutor $tutor): self
    {
        $this->tutor = $tutor;

        return $this;
    }
}
