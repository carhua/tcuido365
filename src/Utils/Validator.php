<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Utils;

use Symfony\Component\Console\Exception\InvalidArgumentException;

class Validator
{
    public function validateUsername(?string $username): string
    {
        if (null === $username || '' === $username || '0' === $username) {
            throw new InvalidArgumentException('The username can not be empty.');
        }

        if (1 !== preg_match('/^[a-z_]+$/', $username)) {
            throw new InvalidArgumentException('The username must contain only lowercase latin characters and underscores.');
        }

        return $username;
    }

    public function validatePassword(?string $plainPassword): string
    {
        if (null === $plainPassword || '' === $plainPassword || '0' === $plainPassword) {
            throw new InvalidArgumentException('The password can not be empty.');
        }

        if (mb_strlen(trim($plainPassword)) < 6) {
            throw new InvalidArgumentException('The password must be at least 6 characters long.');
        }

        return $plainPassword;
    }

    public function validateEmail(?string $email): string
    {
        if (null === $email || '' === $email || '0' === $email) {
            throw new InvalidArgumentException('The email can not be empty.');
        }

        if (false === mb_strpos($email, '@')) {
            throw new InvalidArgumentException('The email should look like a real email.');
        }

        return $email;
    }

    public function validateFullName(?string $fullName): string
    {
        if (null === $fullName || '' === $fullName || '0' === $fullName) {
            throw new InvalidArgumentException('The full name can not be empty.');
        }

        return $fullName;
    }
}
