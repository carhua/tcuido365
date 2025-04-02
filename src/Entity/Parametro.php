<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Utils\Generator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\ParametroRepository')]
class Parametro extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 16, nullable: true)]
    #[Assert\Length(max: 16, maxMessage: 'Debe tener un máximo de 16 carácteres')]
    private $alias;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $valor;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Parametro')]
    private $padre;

    public function __construct()
    {
        parent::__construct();
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

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = Generator::withoutWhiteSpaces($alias);

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getPadre(): ?self
    {
        return $this->padre;
    }

    public function setPadre(?self $padre): self
    {
        $this->padre = $padre;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNombre();
    }
}
