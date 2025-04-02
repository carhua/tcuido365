<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Table(name: 'adjunto')]
#[ORM\Entity(repositoryClass: 'App\Repository\AdjuntoRepository')]
class Adjunto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private $secure;

    #[ORM\Column(type: 'string', length: 255)]
    private $ruta = '/';

    private $file;

    private $tmpNombre;

    #[ORM\OneToMany(targetEntity: Blog::class, mappedBy: 'adjunto')]
    private $blogs;

    public function __construct()
    {
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

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSecure(): ?string
    {
        return $this->secure;
    }

    public function setSecure(string $secure): self
    {
        $this->secure = $secure;

        return $this;
    }

    public function getRuta(): ?string
    {
        return $this->ruta;
    }

    public function setRuta(string $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): self
    {
        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $this->tmpNombre = $this->getSecure();
            $this->setNombre($file->getClientOriginalName());
            $this->file = $file;
        }

        return $this;
    }

    protected function getExtension(): string
    {
        return pathinfo($this->getSecure(), \PATHINFO_EXTENSION) ?: 'error';
    }

    public function getTmpNombre(): string
    {
        return $this->tmpNombre ?: '';
    }

    public function path(): string
    {
        return $this->getRuta().$this->getSecure();
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
            $blog->setAdjunto($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        // set the owning side to null (unless already changed)
        if ($this->blogs->removeElement($blog) && $blog->getAdjunto() === $this) {
            $blog->setAdjunto(null);
        }

        return $this;
    }
}
