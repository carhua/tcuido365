<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\DetalleCasoViolenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetalleCasoViolenciaRepository::class)]
class DetalleCasoViolencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CasoViolencia::class, inversedBy: 'detalleCasoViolencias')]
    private $casoViolencia;

    #[ORM\ManyToOne(targetEntity: Denunciante::class, inversedBy: 'detalleCasoViolencias')]
    private $denunciante;

    #[ORM\ManyToOne(targetEntity: Agraviado::class, inversedBy: 'detalleCasoViolencias')]
    private $agraviado;

    #[ORM\ManyToOne(targetEntity: Agresor::class, inversedBy: 'detalleCasoViolencias')]
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

    public function getDenunciante(): ?Denunciante
    {
        return $this->denunciante;
    }

    public function setDenunciante(?Denunciante $denunciante): self
    {
        $this->denunciante = $denunciante;

        return $this;
    }

    public function getAgraviado(): ?Agraviado
    {
        return $this->agraviado;
    }

    public function setAgraviado(?Agraviado $agraviado): self
    {
        $this->agraviado = $agraviado;

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
