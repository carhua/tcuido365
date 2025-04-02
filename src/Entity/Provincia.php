<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\ProvinciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProvinciaRepository::class)]
class Provincia extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private $ubigeo;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'provincias')]
    private $region;

    #[ORM\OneToMany(targetEntity: Distrito::class, mappedBy: 'provincia')]
    private $distritos;

    #[ORM\OneToMany(targetEntity: Usuario::class, mappedBy: 'provincia')]
    private $usuarios;

    #[ORM\OneToMany(targetEntity: Blog::class, mappedBy: 'provincia')]
    private $blogs;

    public function __construct()
    {
        parent::__construct();
        $this->distritos = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
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

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function setUbigeo(?string $ubigeo): self
    {
        $this->ubigeo = $ubigeo;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|Distrito[]
     */
    public function getDistritos(): Collection
    {
        return $this->distritos;
    }

    public function addDistrito(Distrito $distrito): self
    {
        if (!$this->distritos->contains($distrito)) {
            $this->distritos[] = $distrito;
            $distrito->setProvincia($this);
        }

        return $this;
    }

    public function removeDistrito(Distrito $distrito): self
    {
        // set the owning side to null (unless already changed)
        if ($this->distritos->removeElement($distrito) && $distrito->getProvincia() === $this) {
            $distrito->setProvincia(null);
        }

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
            $usuario->setProvincia($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        // set the owning side to null (unless already changed)
        if ($this->usuarios->removeElement($usuario) && $usuario->getProvincia() === $this) {
            $usuario->setProvincia(null);
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
