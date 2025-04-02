<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Manager\Usuario;

use App\Dto\FileDto;
use App\Entity\ArchivoImportado;
use App\Entity\Usuario;
use App\Repository\CentroPobladoRepository;
use App\Repository\DistritoRepository;
use App\Repository\ProvinciaRepository;
use App\Repository\UsuarioRepository;
use App\Repository\UsuarioRolRepository;
use CarlosChininin\Spreadsheet\Reader\OpenSpout\SpreadsheetReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

final class ImportDataUsuarioManager
{
    public const IMPORT_KEY = 'USUARIO_IMPORT';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly UsuarioRepository $usuarioRepository,
        private readonly UsuarioRolRepository $usuarioRolRepository,
        private readonly ProvinciaRepository $provinciaRepository,
        private readonly DistritoRepository $distritoRepository,
        private readonly CentroPobladoRepository $centroPobladoRepository,
        private readonly UserPasswordHasherInterface $passwordEncoder,
    ) {
    }

    public function saveFile(FileDto $file): void
    {
        $import = $this->entityManager->getRepository(ArchivoImportado::class)->findOneBy(['name' => self::IMPORT_KEY]);
        if (null !== $import) {
            if (file_exists($import->getFile())) {
                unlink($import->getFile());
            }
            $this->entityManager->remove($import);
        }

        $import = new ArchivoImportado();
        $import->setName(self::IMPORT_KEY);
        $import->setFile($file->path().'/'.$file->name());

        $this->entityManager->persist($import);
        $this->entityManager->flush();
    }

    public function data(): array
    {
        $import = $this->entityManager->getRepository(ArchivoImportado::class)->findOneBy(['name' => self::IMPORT_KEY]);

        return SpreadsheetReader::create($import->getFile())->data();
    }

    public function dataImportada(): array
    {
        $data = $this->data();
        $headers = array_shift($data);

        return [
            'headers' => $headers,
            'rows' => $data,
        ];
    }

    public function execute(array $options, bool $saveData): array
    {
        $import = $this->entityManager->getRepository(ArchivoImportado::class)->findOneBy(['name' => self::IMPORT_KEY]);
        $reader = SpreadsheetReader::create($import->getFile());

        $result = [];
        $options = array_values($options);

        $reader->iterator(function (array $row, int $index) use (&$result, $options) {
            if (1 === $index) {
                return;
            }

            $usuario = new Usuario();
            foreach ($row as $key => $value) {
                $this->asigna($usuario, $options[$key] ?? '', $value);
            }

            if (!$usuario->getFullName() || !$usuario->getUsername()) {
                $result[] = 'BAD';

                return;
            }

            $usuario->setOwner($this->security->getUser());
            $itemExist = $this->usuarioRepository->findOneBy(['username' => $usuario->getUsername()]);
            if (null === $itemExist) {
                $errors = [];
                if (null === $usuario->getPassword() || '' === $usuario->getPassword()) {
                    $errors[] = 'Falta Contraseña';
                }

                if (!empty($errors)) {
                    $result[] = implode(', ', $errors);
                } else {
                    $this->entityManager->persist($usuario);
                    $result[] = null;
                }

                return;
            }

            if (null !== $usuario->getPassword() && '' !== $usuario->getPassword()) {
                $itemExist->setPassword($this->passwordEncoder->hashPassword($usuario, $usuario->getPassword()));
            }

            if ($itemExist->getUsuarioRoles() !== $usuario->getUsuarioRoles()) {
                $itemExist->removeUsuarioRole($itemExist->getUsuarioRoles()[0]);
                $itemExist->addUsuarioRole($usuario->getUsuarioRoles()[0]);
            }
            if ($itemExist->getFullName() !== $usuario->getFullName()) {
                $itemExist->setFullName($usuario->getFullName());
            }
            if ($itemExist->getEmail() !== $usuario->getEmail()) {
                $itemExist->setEmail($usuario->getEmail());
            }
            if ($itemExist->getTelefono() !== $usuario->getTelefono()) {
                $itemExist->setTelefono($usuario->getTelefono());
            }
            if ($itemExist->getProvincia() !== $usuario->getProvincia()) {
                $itemExist->setProvincia($usuario->getProvincia());
            }
            if ($itemExist->getDistrito() !== $usuario->getDistrito()) {
                $itemExist->setDistrito($usuario->getDistrito());
            }
            if ($itemExist->getCentroPoblado() !== $usuario->getCentroPoblado()) {
                $itemExist->setCentroPoblado($usuario->getCentroPoblado());
            }

            $this->usuarioRepository->save($itemExist);

            $result[] = null;
        });

        if ($saveData) {
            $this->entityManager->flush();
        }

        return $result;
    }

    public function headerOptions(): array
    {
        return [
            '' => null,
            'USUARIO' => 'Usuario',
            'NOMBRES' => 'Nombres',
            'CONTRASENIA' => 'Contrasenia',
            'EMAIL' => 'Email',
            'TELEFONO' => 'Telefono',
            'ROLES' => 'Roles',
            'PROVINCIA' => 'Provincia',
            'DISTRITO' => 'Distrito',
            'CENTRO POBLADO' => 'Centro Poblado',
        ];
    }

    private function asigna(Usuario $usuario, string $key, mixed $value): void
    {
        if ('' === $key) {
            return;
        }

        if ('USUARIO' === $key) {
            $usuario->setUsername($value);

            return;
        }

        if ('NOMBRES' === $key) {
            $usuario->setFullName($value);

            return;
        }

        if ('CONTRASENIA' === $key) {
            $usuario->setPassword($value);

            return;
        }

        if ('EMAIL' === $key) {
            $usuario->setEmail($value);

            return;
        }

        if ('TELEFONO' === $key) {
            $usuario->setTelefono((string) $value);

            return;
        }

        if ('ROLES' === $key) {
            $usuarioRoles = $this->usuarioRolRepository->findOneBy(['rol' => (string) $value]);
            $usuario->addUsuarioRole($usuarioRoles);

            return;
        }

        if ('PROVINCIA' === $key) {
            $provincia = $this->provinciaRepository->findOneBy(['nombre' => (string) $value]);
            $usuario->setProvincia($provincia);

            return;
        }
        if ('DISTRITO' === $key) {
            $distrito = $this->distritoRepository->findOneBy(['nombre' => (string) $value]);
            $usuario->setDistrito($distrito);

            return;
        }
        if ('CENTRO POBLADO' === $key) {
            $centroPoblado = $this->centroPobladoRepository->findOneBy(['nombre' => (string) $value]);
            $usuario->setCentroPoblado($centroPoblado);

            return;
        }

        throw new \RuntimeException('Key no valid');
    }
}
