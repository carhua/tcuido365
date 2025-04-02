<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CasoViolenciaAgraviadoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasoViolenciaAgraviadoRepository::class)]
class CasoViolenciaAgraviado
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CasoViolencia::class, inversedBy: 'casoViolenciaAgraviados')]
    private $casoViolencia;

    #[ORM\ManyToOne(targetEntity: Agraviado::class, inversedBy: 'casoViolenciaAgraviados', cascade: ['persist'])]
    private $agraviado;

    #[ORM\Column(type: 'text', nullable: true)]
    private $tipoMaltratos;

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

    public function getAgraviado(): ?Agraviado
    {
        return $this->agraviado;
    }

    public function setAgraviado(?Agraviado $agraviado): self
    {
        $this->agraviado = $agraviado;

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
}
