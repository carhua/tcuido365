<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity;

use App\Repository\UsuarioRepository;
use App\Utils\Generator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[UniqueEntity('username')]
class Usuario extends Base implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $fullName;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4, max: 50)]
    #[Groups('main')]
    private ?string $username;

    #[ORM\Column(type: 'string', length: 100, unique: true, nullable: true)]
    #[Assert\Email]
    #[Groups('main')]
    private ?string $email;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\Length(min: 8, groups: ['Update'])]
    #[Assert\Regex(pattern: '/(?=.*?[A-Z])/', message: 'Debe contener al menos una letra mayuscula')]
    #[Assert\Regex(pattern: '/(?=.*?[a-z])/', message: 'Debe contener al menos una letra minuscula')]
    #[Assert\Regex(pattern: '/(?=.*?\d)/', message: 'Debe contener al menos un numero')]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: UsuarioRol::class, inversedBy: 'usuarios')]
    private Collection $usuarioRoles;

    #[ORM\OneToOne(targetEntity: Adjunto::class, cascade: ['persist', 'remove'])]
    private ?Adjunto $foto = null;

    #[ORM\ManyToOne(targetEntity: CentroPoblado::class, inversedBy: 'usuarios')]
    private ?CentroPoblado $centroPoblado;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $telefono = null;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'usuarios')]
    private ?Region $region = null;

    #[ORM\ManyToOne(targetEntity: Provincia::class, inversedBy: 'usuarios')]
    private ?Provincia $provincia = null;

    #[ORM\ManyToOne(targetEntity: Institucion::class, inversedBy: 'usuarios')]
    private ?Institucion $institucion = null;

    #[ORM\ManyToOne(targetEntity: Distrito::class, inversedBy: 'usuarios')]
    private ?Distrito $distrito = null;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = Generator::withoutWhiteSpaces($username);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = Generator::withoutWhiteSpaces($email);

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = Generator::withoutWhiteSpaces($password);

        return $this;
    }

    public function __serialize(): array
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return [$this->id, $this->username, $this->password];
    }

    public function __unserialize(array $data): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    public function addUsuarioRole(UsuarioRol $role): self
    {
        if (!$this->usuarioRoles->contains($role)) {
            $this->usuarioRoles[] = $role;
        }

        return $this;
    }

    public function removeUsuarioRole(UsuarioRol $role): self
    {
        if ($this->usuarioRoles->contains($role)) {
            $this->usuarioRoles->removeElement($role);
        }

        return $this;
    }

    /**
     * @return UsuarioRol [] | Collection
     */
    public function getUsuarioRoles(): array|Collection
    {
        // return $this->usuarioRoles;
        $roles = [];
        foreach ($this->usuarioRoles as $role) {
            $roles[] = $role;
        }

        return $roles;
    }

    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->usuarioRoles as $role) {
            $roles[] = $role->getRol();
        }

        return $roles;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    public function getFoto(): ?Adjunto
    {
        return $this->foto;
    }

    public function setFoto(?Adjunto $foto): self
    {
        $this->foto = $foto;

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

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

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

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getInstitucion(): ?Institucion
    {
        return $this->institucion;
    }

    public function setInstitucion(?Institucion $institucion): self
    {
        $this->institucion = $institucion;

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

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }
}
