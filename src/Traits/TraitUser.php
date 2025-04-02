<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Traits;

use App\Entity\CentroPoblado;
use App\Entity\Provincia;

trait TraitUser
{
    public static function validarRoles($roles)
    {
        return \in_array('ROLE_TENIENTE', $roles, true);
    }

    public static function listCentrosByRol($rt, $user, $em)
    {
        $cid = $user->getCentroPoblado()->getId();
        $distrito = $user->getDistrito() ?: null;
        $provincia = $user->getProvincia() ?: null;

        if ($rt) {
            $centros = $em->getRepository(CentroPoblado::class)->findBy(['isActive' => true, 'id' => $cid]);
        } elseif (null !== $distrito && null !== $provincia) {
            $centros = $em->getRepository(CentroPoblado::class)->findByProvinciaDistrito($provincia, $distrito);
        } else {
            $centros = [];
        }

        return $centros;
    }

    public static function listProvinciasByRol($rt, $user, $em)
    {
        $provincia = $user->getProvincia() ?: null;

        if ($rt || 'TODOS' !== $provincia->getNombre()) {
            $provincias = $em->getRepository(Provincia::class)->findBy(['isActive' => true, 'id' => $provincia->getId()]);
        } elseif (null !== $provincia) {
            $provincias = $em->getRepository(Provincia::class)->findBy(['isActive' => true]);
        } else {
            $provincias = [];
        }

        return $provincias;
    }

    public static function findCentro($id, $em): CentroPoblado
    {
        return $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $id]);
    }

    public static function findPerson($numero, $array)
    {
        if (\count($array) > 0) {
            return array_search($numero, array_column($array, 'numeroDocumento'), true);
        }

        return false;
    }

    public static function filter_by_value($array, $index, $value)
    {
        if (\is_array($array) && [] !== $array) {
            foreach (array_keys($array) as $key) {
                $temp[$key] = $array[$key][$index];

                if ($temp[$key] === $value) {
                    $newarray[$key] = $array[$key];
                }
            }
        }

        return $newarray;
    }
}
