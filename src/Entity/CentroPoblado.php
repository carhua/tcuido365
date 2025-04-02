<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\CentroPobladoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CentroPobladoRepository::class)]
class CentroPoblado extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $codigo;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $categoria;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $tipo;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $longitud;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $latitud;

    #[ORM\OneToMany(targetEntity: Usuario::class, mappedBy: 'centroPoblado')]
    private $usuarios;

    #[ORM\ManyToOne(targetEntity: Distrito::class, inversedBy: 'centroPoblados')]
    private $distrito;

    #[ORM\OneToMany(targetEntity: Denunciante::class, mappedBy: 'centroPoblado')]
    private $denunciantes;

    #[ORM\OneToMany(targetEntity: Agraviado::class, mappedBy: 'centroPoblado')]
    private $agraviados;

    #[ORM\OneToMany(targetEntity: Agresor::class, mappedBy: 'centroPoblado')]
    private $agresors;

    #[ORM\OneToMany(targetEntity: Tutor::class, mappedBy: 'centroPoblado')]
    private $tutors;

    #[ORM\OneToMany(targetEntity: MenorEdad::class, mappedBy: 'centroPoblado')]
    private $menorEdads;

    #[ORM\OneToMany(targetEntity: Persona::class, mappedBy: 'centroPoblado')]
    private $personas;

    #[ORM\OneToMany(targetEntity: CasoTrata::class, mappedBy: 'centroPoblado')]
    private $casoTratas;

    #[ORM\OneToMany(targetEntity: CasoDesaparecido::class, mappedBy: 'centroPoblado')]
    private $casoDesaparecidos;

    #[ORM\OneToMany(targetEntity: Blog::class, mappedBy: 'centroPoblado')]
    private $blogs;

    public function __construct()
    {
        parent::__construct();
        $this->usuarios = new ArrayCollection();
        $this->denunciantes = new ArrayCollection();
        $this->agraviados = new ArrayCollection();
        $this->agresors = new ArrayCollection();
        $this->tutors = new ArrayCollection();
        $this->menorEdads = new ArrayCollection();
        $this->personas = new ArrayCollection();
        $this->casoTratas = new ArrayCollection();
        $this->casoDesaparecidos = new ArrayCollection();
        $this->blogs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getNombre();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
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

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(?string $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getLongitud(): ?string
    {
        return $this->longitud;
    }

    public function setLongitud(?string $longitud): self
    {
        $this->longitud = $longitud;

        return $this;
    }

    public function getLatitud(): ?string
    {
        return $this->latitud;
    }

    public function setLatitud(?string $latitud): self
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->contains($usuario)) {
            $this->usuarios->removeElement($usuario);
            // set the owning side to null (unless already changed)
            if ($usuario->getCentroPoblado() === $this) {
                $usuario->setCentroPoblado(null);
            }
        }

        return $this;
    }

    public function getDistrito(): ?Distrito
    {
        return $this->distrito;
    }

    public function setDistrito(?Distrito $distrito): self
    {
        $this->distrito = $distrito;

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
            $denunciante->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeDenunciante(Denunciante $denunciante): self
    {
        if ($this->denunciantes->contains($denunciante)) {
            $this->denunciantes->removeElement($denunciante);
            // set the owning side to null (unless already changed)
            if ($denunciante->getCentroPoblado() === $this) {
                $denunciante->setCentroPoblado(null);
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
            $agraviado->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeAgraviado(Agraviado $agraviado): self
    {
        if ($this->agraviados->contains($agraviado)) {
            $this->agraviados->removeElement($agraviado);
            // set the owning side to null (unless already changed)
            if ($agraviado->getCentroPoblado() === $this) {
                $agraviado->setCentroPoblado(null);
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
            $agresor->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeAgresor(Agresor $agresor): self
    {
        if ($this->agresors->contains($agresor)) {
            $this->agresors->removeElement($agresor);
            // set the owning side to null (unless already changed)
            if ($agresor->getCentroPoblado() === $this) {
                $agresor->setCentroPoblado(null);
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
            $tutor->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeTutor(Tutor $tutor): self
    {
        if ($this->tutors->contains($tutor)) {
            $this->tutors->removeElement($tutor);
            // set the owning side to null (unless already changed)
            if ($tutor->getCentroPoblado() === $this) {
                $tutor->setCentroPoblado(null);
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
            $menorEdad->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeMenorEdad(MenorEdad $menorEdad): self
    {
        if ($this->menorEdads->contains($menorEdad)) {
            $this->menorEdads->removeElement($menorEdad);
            // set the owning side to null (unless already changed)
            if ($menorEdad->getCentroPoblado() === $this) {
                $menorEdad->setCentroPoblado(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Persona[]
     */
    public function getPersonas(): Collection
    {
        return $this->personas;
    }

    public function addPersona(Persona $persona): self
    {
        if (!$this->personas->contains($persona)) {
            $this->personas[] = $persona;
            $persona->setCentroPoblado($this);
        }

        return $this;
    }

    public function removePersona(Persona $persona): self
    {
        if ($this->personas->contains($persona)) {
            $this->personas->removeElement($persona);
            // set the owning side to null (unless already changed)
            if ($persona->getCentroPoblado() === $this) {
                $persona->setCentroPoblado(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CasoTrata[]
     */
    public function getCasoTratas(): Collection
    {
        return $this->casoTratas;
    }

    public function addCasoTrata(CasoTrata $casoTrata): self
    {
        if (!$this->casoTratas->contains($casoTrata)) {
            $this->casoTratas[] = $casoTrata;
            $casoTrata->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeCasoTrata(CasoTrata $casoTrata): self
    {
        if ($this->casoTratas->contains($casoTrata)) {
            $this->casoTratas->removeElement($casoTrata);
            // set the owning side to null (unless already changed)
            if ($casoTrata->getCentroPoblado() === $this) {
                $casoTrata->setCentroPoblado(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CasoDesaparecido[]
     */
    public function getCasoDesaparecidos(): Collection
    {
        return $this->casoDesaparecidos;
    }

    public function addCasoDesaparecido(CasoDesaparecido $casoDesaparecido): self
    {
        if (!$this->casoDesaparecidos->contains($casoDesaparecido)) {
            $this->casoDesaparecidos[] = $casoDesaparecido;
            $casoDesaparecido->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeCasoDesaparecido(CasoDesaparecido $casoDesaparecido): self
    {
        if ($this->casoDesaparecidos->contains($casoDesaparecido)) {
            $this->casoDesaparecidos->removeElement($casoDesaparecido);
            // set the owning side to null (unless already changed)
            if ($casoDesaparecido->getCentroPoblado() === $this) {
                $casoDesaparecido->setCentroPoblado(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Blog[]
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): self
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs[] = $blog;
            $blog->setCentroPoblado($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        // set the owning side to null (unless already changed)
        if ($this->blogs->removeElement($blog) && $blog->getCentroPoblado() === $this) {
            $blog->setCentroPoblado(null);
        }

        return $this;
    }
}
