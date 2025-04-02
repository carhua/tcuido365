<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\DetenidoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetenidoRepository::class)]
class Detenido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 200)]
    private $nombres;

    #[ORM\Column(type: 'string', length: 200)]
    private $apellidos;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class, inversedBy: 'detenidos')]
    private $tipoDocumento;

    #[ORM\Column(type: 'string', length: 100)]
    private $numeroDocumento;

    #[ORM\Column(type: 'integer')]
    private $edad;

    #[ORM\ManyToOne(targetEntity: Sexo::class, inversedBy: 'detenidos')]
    private $sexo;

    #[ORM\ManyToOne(targetEntity: Nacionalidad::class, inversedBy: 'detenidos')]
    private $nacionalidad;

    #[ORM\ManyToOne(targetEntity: FormaCaptacion::class, inversedBy: 'detenidos')]
    private $formaCaptacion;

    #[ORM\Column(type: 'string', length: 100)]
    private $codigoApp;

    #[ORM\ManyToOne(targetEntity: Persona::class, inversedBy: 'detenidos', cascade: ['persist'])]
    private $persona;

    #[ORM\ManyToOne(targetEntity: CasoTrata::class, inversedBy: 'detenidos')]
    private $casoTrata;

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

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): self
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

    public function getNacionalidad(): ?Nacionalidad
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(?Nacionalidad $nacionalidad): self
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    public function getFormaCaptacion(): ?FormaCaptacion
    {
        return $this->formaCaptacion;
    }

    public function setFormaCaptacion(?FormaCaptacion $formaCaptacion): self
    {
        $this->formaCaptacion = $formaCaptacion;

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

    public function setNumeroDocumento(string $numeroDocumento): self
    {
        $this->numeroDocumento = $numeroDocumento;

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

    public function getCasoTrata(): ?CasoTrata
    {
        return $this->casoTrata;
    }

    public function setCasoTrata(?CasoTrata $casoTrata): self
    {
        $this->casoTrata = $casoTrata;

        return $this;
    }
}
