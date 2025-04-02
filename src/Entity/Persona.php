<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\PersonaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonaRepository::class)]
class Persona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombres;

    #[ORM\Column(type: 'string', length: 100)]
    private $apellidos;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $numeroDocumento;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $edad;

    #[ORM\Column(type: 'string', nullable: true)]
    private $sexo;

    #[ORM\OneToMany(mappedBy: 'persona', targetEntity: Denunciante::class)]
    private $denunciantes;

    #[ORM\OneToMany(targetEntity: Agresor::class, mappedBy: 'persona')]
    private $agresors;

    #[ORM\OneToMany(targetEntity: Agraviado::class, mappedBy: 'persona')]
    private $agraviados;

    #[ORM\OneToMany(targetEntity: MenorEdad::class, mappedBy: 'persona')]
    private $menorEdads;

    #[ORM\OneToMany(targetEntity: Tutor::class, mappedBy: 'persona')]
    private $tutors;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'personas')]
    private $centroPoblado;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $tipoDocumento;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $casoViolencia;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $casoDesproteccion;

    #[ORM\OneToMany(targetEntity: Victima::class, mappedBy: 'persona')]
    private $victimas;

    #[ORM\OneToMany(targetEntity: Detenido::class, mappedBy: 'persona')]
    private $detenidos;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $casoTrata;

    #[ORM\OneToMany(mappedBy: 'persona', targetEntity: DenuncianteDesaparecido::class)]
    private Collection $denuncianteDesaparecidos;

    #[ORM\OneToMany(mappedBy: 'persona', targetEntity: Desaparecido::class)]
    private Collection $desaparecidos;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $casoDesaparecido = 1;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $casoDesaparecidoTotal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $casoDesproteccionTotal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $casoTrataTotal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $casoViolenciaTotal = 0;

    public function __construct()
    {
        $this->denunciantes = new ArrayCollection();
        $this->agresors = new ArrayCollection();
        $this->agraviados = new ArrayCollection();
        $this->menorEdads = new ArrayCollection();
        $this->tutors = new ArrayCollection();
        $this->victimas = new ArrayCollection();
        $this->detenidos = new ArrayCollection();
        $this->denuncianteDesaparecidos = new ArrayCollection();
        $this->desaparecidos = new ArrayCollection();
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

    public function getNumeroDocumento(): ?string
    {
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento(string $numeroDocumento): self
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int|string $edad): self
    {
        $this->edad = (int) $edad;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * @return Collection|Denunciante[]
     */
    public function getDenunciantes(): Collection
    {
        return $this->denunciantes;
    }

    public function addDenunciante(Denunciante $denunciante): self
    {
        if (!$this->denunciantes->contains($denunciante)) {
            $this->denunciantes[] = $denunciante;
            $denunciante->setPersona($this);
        }

        return $this;
    }

    public function removeDenunciante(Denunciante $denunciante): self
    {
        if ($this->denunciantes->contains($denunciante)) {
            $this->denunciantes->removeElement($denunciante);
            // set the owning side to null (unless already changed)
            if ($denunciante->getPersona() === $this) {
                $denunciante->setPersona(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Agresor[]
     */
    public function getAgresors(): Collection
    {
        return $this->agresors;
    }

    public function addAgresor(Agresor $agresor): self
    {
        if (!$this->agresors->contains($agresor)) {
            $this->agresors[] = $agresor;
            $agresor->setPersona($this);
        }

        return $this;
    }

    public function removeAgresor(Agresor $agresor): self
    {
        if ($this->agresors->contains($agresor)) {
            $this->agresors->removeElement($agresor);
            // set the owning side to null (unless already changed)
            if ($agresor->getPersona() === $this) {
                $agresor->setPersona(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Agraviado[]
     */
    public function getAgraviados(): Collection
    {
        return $this->agraviados;
    }

    public function addAgraviado(Agraviado $agraviado): self
    {
        if (!$this->agraviados->contains($agraviado)) {
            $this->agraviados[] = $agraviado;
            $agraviado->setPersona($this);
        }

        return $this;
    }

    public function removeAgraviado(Agraviado $agraviado): self
    {
        if ($this->agraviados->contains($agraviado)) {
            $this->agraviados->removeElement($agraviado);
            // set the owning side to null (unless already changed)
            if ($agraviado->getPersona() === $this) {
                $agraviado->setPersona(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MenorEdad[]
     */
    public function getMenorEdads(): Collection
    {
        return $this->menorEdads;
    }

    public function addMenorEdad(MenorEdad $menorEdad): self
    {
        if (!$this->menorEdads->contains($menorEdad)) {
            $this->menorEdads[] = $menorEdad;
            $menorEdad->setPersona($this);
        }

        return $this;
    }

    public function removeMenorEdad(MenorEdad $menorEdad): self
    {
        if ($this->menorEdads->contains($menorEdad)) {
            $this->menorEdads->removeElement($menorEdad);
            // set the owning side to null (unless already changed)
            if ($menorEdad->getPersona() === $this) {
                $menorEdad->setPersona(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tutor[]
     */
    public function getTutors(): Collection
    {
        return $this->tutors;
    }

    public function addTutor(Tutor $tutor): self
    {
        if (!$this->tutors->contains($tutor)) {
            $this->tutors[] = $tutor;
            $tutor->setPersona($this);
        }

        return $this;
    }

    public function removeTutor(Tutor $tutor): self
    {
        if ($this->tutors->contains($tutor)) {
            $this->tutors->removeElement($tutor);
            // set the owning side to null (unless already changed)
            if ($tutor->getPersona() === $this) {
                $tutor->setPersona(null);
            }
        }

        return $this;
    }

    public function getCentroPoblado(): ?CentroPoblado
    {
        return $this->centroPoblado;
    }

    public function setCentroPoblado(?CentroPoblado $centroPoblado): self
    {
        $this->centroPoblado = $centroPoblado;

        return $this;
    }

    public function getTipoDocumento(): ?string
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?string $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    public function getCasoViolencia(): ?int
    {
        return $this->casoViolencia;
    }

    public function setCasoViolencia(?int $casoViolencia): self
    {
        $this->casoViolencia = $casoViolencia;

        return $this;
    }

    public function getCasoDesproteccion(): ?int
    {
        return $this->casoDesproteccion;
    }

    public function setCasoDesproteccion(?int $casoDesproteccion): self
    {
        $this->casoDesproteccion = $casoDesproteccion;

        return $this;
    }

    /**
     * @return Collection|Victima[]
     */
    public function getVictimas(): Collection
    {
        return $this->victimas;
    }

    public function addVictima(Victima $victima): self
    {
        if (!$this->victimas->contains($victima)) {
            $this->victimas[] = $victima;
            $victima->setPersona($this);
        }

        return $this;
    }

    public function removeVictima(Victima $victima): self
    {
        if ($this->victimas->contains($victima)) {
            $this->victimas->removeElement($victima);
            // set the owning side to null (unless already changed)
            if ($victima->getPersona() === $this) {
                $victima->setPersona(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Detenido[]
     */
    public function getDetenidos(): Collection
    {
        return $this->detenidos;
    }

    public function addDetenido(Detenido $detenido): self
    {
        if (!$this->detenidos->contains($detenido)) {
            $this->detenidos[] = $detenido;
            $detenido->setPersona($this);
        }

        return $this;
    }

    public function removeDetenido(Detenido $detenido): self
    {
        if ($this->detenidos->contains($detenido)) {
            $this->detenidos->removeElement($detenido);
            // set the owning side to null (unless already changed)
            if ($detenido->getPersona() === $this) {
                $detenido->setPersona(null);
            }
        }

        return $this;
    }

    public function getCasoTrata(): ?int
    {
        return $this->casoTrata;
    }

    public function setCasoTrata(?int $casoTrata): self
    {
        $this->casoTrata = $casoTrata;

        return $this;
    }

    /**
     * @return Collection|DenuncianteDesaparecido[]
     */
    public function getDenuncianteDesaparecidos(): Collection
    {
        return $this->denuncianteDesaparecidos;
    }

    public function addDenuncianteDesaparecido(DenuncianteDesaparecido $denuncianteDesaparecido): self
    {
        if (!$this->denuncianteDesaparecidos->contains($denuncianteDesaparecido)) {
            $this->denuncianteDesaparecidos[] = $denuncianteDesaparecido;
            $denuncianteDesaparecido->setPersona($this);
        }

        return $this;
    }

    public function removeDenuncianteDesaparecido(DenuncianteDesaparecido $denuncianteDesaparecido): self
    {
        if ($this->denuncianteDesaparecidos->contains($denuncianteDesaparecido)) {
            $this->denuncianteDesaparecidos->removeElement($denuncianteDesaparecido);
            // set the owning side to null (unless already changed)
            if ($denuncianteDesaparecido->getPersona() === $this) {
                $denuncianteDesaparecido->setPersona(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Desaparecido[]
     */
    public function getDesaparecidos(): Collection
    {
        return $this->desaparecidos;
    }

    public function addDesaparecido(Desaparecido $desaparecido): self
    {
        if (!$this->desaparecidos->contains($desaparecido)) {
            $this->desaparecidos[] = $desaparecido;
            $desaparecido->setPersona($this);
        }

        return $this;
    }

    public function removeDesaparecido(Desaparecido $desaparecido): self
    {
        if ($this->desaparecidos->contains($desaparecido)) {
            $this->desaparecidos->removeElement($desaparecido);
            // set the owning side to null (unless already changed)
            if ($desaparecido->getPersona() === $this) {
                $desaparecido->setPersona(null);
            }
        }

        return $this;
    }

    public function getCasoDesaparecido(): ?int
    {
        return $this->casoDesaparecido;
    }

    public function setCasoDesaparecido(int $casoDesaparecido): self
    {
        $this->casoDesaparecido = $casoDesaparecido;

        return $this;
    }

    public function getCasoDesaparecidoTotal(): ?int
    {
        return $this->casoDesaparecidoTotal;
    }

    public function setCasoDesaparecidoTotal(int $casoDesaparecidoTotal): static
    {
        $this->casoDesaparecidoTotal = $casoDesaparecidoTotal;

        return $this;
    }

    public function getCasoDesproteccionTotal(): ?int
    {
        return $this->casoDesproteccionTotal;
    }

    public function setCasoDesproteccionTotal(int $casoDesproteccionTotal): static
    {
        $this->casoDesproteccionTotal = $casoDesproteccionTotal;

        return $this;
    }

    public function getCasoTrataTotal(): ?int
    {
        return $this->casoTrataTotal;
    }

    public function setCasoTrataTotal(int $casoTrataTotal): static
    {
        $this->casoTrataTotal = $casoTrataTotal;

        return $this;
    }

    public function getCasoViolenciaTotal(): ?int
    {
        return $this->casoViolenciaTotal;
    }

    public function setCasoViolenciaTotal(int $casoViolenciaTotal): static
    {
        $this->casoViolenciaTotal = $casoViolenciaTotal;

        return $this;
    }
}
