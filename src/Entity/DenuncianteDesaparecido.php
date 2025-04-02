<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\DenuncianteDesaparecidoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DenuncianteDesaparecidoRepository::class)]
class DenuncianteDesaparecido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $nombres;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $apellidos;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class, inversedBy: 'denuncianteDesaparecidos')]
    private $tipoDocumento;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $numeroDocumento;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $edad;

    #[ORM\ManyToOne(targetEntity: Sexo::class, inversedBy: 'denuncianteDesaparecidos')]
    private $sexo;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $telefono;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 100)]
    private $codigoApp;

    #[ORM\ManyToOne(targetEntity: CasoDesaparecido::class, inversedBy: 'denuncianteDesaparecidos')]
    private $casoDesaparecido;

    #[ORM\ManyToOne(targetEntity: Persona::class, inversedBy: 'denuncianteDesaparecidos', cascade:['persist'])]
    private $persona;

    public function __toString(): string
    {
        return $this->getApellidos().' '.$this->getNombres();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getTipoDocumento(): ?TipoDocumento
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?TipoDocumento $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    public function getNumeroDocumento(): ?string
    {
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento(?string $numeroDocumento): self
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(?int $edad): self
    {
        $this->edad = $edad;

        return $this;
    }

    public function getSexo(): ?Sexo
    {
        return $this->sexo;
    }

    public function setSexo(?Sexo $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCodigoApp(): ?string
    {
        return $this->codigoApp;
    }

    public function setCodigoApp(string $codigoApp): self
    {
        $this->codigoApp = $codigoApp;

        return $this;
    }

    public function getCasoDesaparecido(): ?CasoDesaparecido
    {
        return $this->casoDesaparecido;
    }

    public function setCasoDesaparecido(?CasoDesaparecido $casoDesaparecido): self
    {
        $this->casoDesaparecido = $casoDesaparecido;

        return $this;
    }

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): self
    {
        $this->persona = $persona;

        return $this;
    }
}
