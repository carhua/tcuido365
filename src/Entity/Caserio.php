<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CaserioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaserioRepository::class)]
class Caserio extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\ManyToOne(targetEntity: Comunidad::class, inversedBy: 'caserios')]
    #[ORM\JoinColumn(nullable: false)]
    private $comunidad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getComunidad(): ?Comunidad
    {
        return $this->comunidad;
    }

    public function setComunidad(?Comunidad $comunidad): self
    {
        $this->comunidad = $comunidad;

        return $this;
    }
}
