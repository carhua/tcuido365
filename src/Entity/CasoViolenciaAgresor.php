<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoViolenciaAgresorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoViolenciaAgresorRepository::class)]
class CasoViolenciaAgresor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CasoViolencia::class, inversedBy: 'casoViolenciaAgresors')]
    private $casoViolencia;

    #[ORM\ManyToOne(targetEntity: Agresor::class, inversedBy: 'casoViolenciaAgresors', cascade: ['persist'])]
    private $agresor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCasoViolencia(): ?CasoViolencia
    {
        return $this->casoViolencia;
    }

    public function setCasoViolencia(?CasoViolencia $casoViolencia): self
    {
        $this->casoViolencia = $casoViolencia;

        return $this;
    }

    public function getAgresor(): ?Agresor
    {
        return $this->agresor;
    }

    public function setAgresor(?Agresor $agresor): self
    {
        $this->agresor = $agresor;

        return $this;
    }
}
