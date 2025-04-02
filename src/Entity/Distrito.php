<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\DistritoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DistritoRepository::class)]
class Distrito extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private $ubigeo;

    #[ORM\OneToMany(targetEntity: CentroPoblado::class, mappedBy: 'distrito')]
    private $centroPoblados;

    #[ORM\ManyToOne(targetEntity: Provincia::class, inversedBy: 'distritos')]
    private $provincia;

    #[ORM\OneToMany(targetEntity: Usuario::class, mappedBy: 'distrito')]
    private $usuarios;

    #[ORM\OneToMany(targetEntity: Blog::class, mappedBy: 'distrito')]
    private $blogs;

    public function __construct()
    {
        parent::__construct();
        $this->centroPoblados = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
        $this->blogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): self
    {
        $this->ubigeo = $ubigeo;

        return $this;
    }

    /**
     * @return Collection|CentroPoblado[]
     */
    public function getCentroPoblados(): Collection
    {
        return $this->centroPoblados;
    }

    public function addCentroPoblado(CentroPoblado $centroPoblado): self
    {
        if (!$this->centroPoblados->contains($centroPoblado)) {
            $this->centroPoblados[] = $centroPoblado;
            $centroPoblado->setDistrito($this);
        }

        return $this;
    }

    public function removeCentroPoblado(CentroPoblado $centroPoblado): self
    {
        if ($this->centroPoblados->contains($centroPoblado)) {
            $this->centroPoblados->removeElement($centroPoblado);
            // set the owning side to null (unless already changed)
            if ($centroPoblado->getDistrito() === $this) {
                $centroPoblado->setDistrito(null);
            }
        }

        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNombre();
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
            $usuario->setDistrito($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        // set the owning side to null (unless already changed)
        if ($this->usuarios->removeElement($usuario) && $usuario->getDistrito() === $this) {
            $usuario->setDistrito(null);
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
            $blog->setDistrito($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        // set the owning side to null (unless already changed)
        if ($this->blogs->removeElement($blog) && $blog->getDistrito() === $this) {
            $blog->setDistrito(null);
        }

        return $this;
    }
}
