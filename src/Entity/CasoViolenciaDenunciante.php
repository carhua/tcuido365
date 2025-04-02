<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoViolenciaDenuncianteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoViolenciaDenuncianteRepository::class)]
class CasoViolenciaDenunciante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CasoViolencia::class, inversedBy: 'casoViolenciaDenunciantes')]
    private $casoViolencia;

    #[ORM\ManyToOne(targetEntity: Denunciante::class, inversedBy: 'casoViolenciaDenunciantes', cascade: ['persist'])]
    private $denunciante;

    #[ORM\Column(type: 'text', nullable: true)]
    private $datosGenerales;

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

    public function getDenunciante(): ?Denunciante
    {
        return $this->denunciante;
    }

    public function setDenunciante(?Denunciante $denunciante): self
    {
        $this->denunciante = $denunciante;

        return $this;
    }

    public function getDatosGenerales(): ?string
    {
        return $this->datosGenerales;
    }

    public function setDatosGenerales(?string $datosGenerales): self
    {
        $this->datosGenerales = $datosGenerales;

        return $this;
    }
}
