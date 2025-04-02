<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\DataFixtures;

use App\Entity\Usuario;
use App\Entity\UsuarioRol;
use App\Security\Security;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $rolSuperAdmin = $this->createRol('Super Administrador', Security::ROLE_SUPER_ADMIN);
        $manager->persist($rolSuperAdmin);

        $user = new Usuario();
        $user->setFullName('CIO PIDIA');
        $user->setUsername(Security::USER_SUPER_ADMIN);
        $user->setEmail('cio@pidia.pe');
        $user->addUsuarioRole($rolSuperAdmin);
        $encodedPassword = $this->passwordEncoder->encodePassword($user, '123456');
        $user->setPassword($encodedPassword);
        $manager->persist($user);

        $manager->flush();
    }

    private function createRol(string $name, string $rolName): UsuarioRol
    {
        $rol = new UsuarioRol();
        $rol->setNombre($name);
        $rol->setRol($rolName);

        return $rol;
    }
}
