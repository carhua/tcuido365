<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\ComunidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComunidadRepository::class)]
class Comunidad extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\OneToMany(targetEntity: Caserio::class, mappedBy: 'comunidad')]
    private $caserios;

    public function __construct()
    {
        $this->caserios = new ArrayCollection();
    }

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

    /**
     * @return Collection|Caserio[]
     */
    public function getCaserios(): Collection
    {
        return $this->caserios;
    }

    public function addCaserio(Caserio $caserio): self
    {
        if (!$this->caserios->contains($caserio)) {
            $this->caserios[] = $caserio;
            $caserio->setComunidad($this);
        }

        return $this;
    }

    public function removeCaserio(Caserio $caserio): self
    {
        if ($this->caserios->contains($caserio)) {
            $this->caserios->removeElement($caserio);
            // set the owning side to null (unless already changed)
            if ($caserio->getComunidad() === $this) {
                $caserio->setComunidad(null);
            }
        }

        return $this;
    }
}
