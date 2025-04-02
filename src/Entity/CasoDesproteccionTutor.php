<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoDesproteccionTutorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoDesproteccionTutorRepository::class)]
class CasoDesproteccionTutor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CasoDesproteccion::class, inversedBy: 'casoDesproteccionTutors')]
    private $casoDesproteccion;

    #[ORM\ManyToOne(targetEntity: Tutor::class, inversedBy: 'casoDesproteccionTutors', cascade: ['persist'])]
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
