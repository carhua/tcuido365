<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Api;

use App\Entity\Agraviado;
use App\Entity\Agresor;
use App\Entity\CasoDesaparecido;
use App\Entity\CasoDesproteccion;
use App\Entity\CasoTrata;
use App\Entity\CasoViolencia;
use App\Entity\CentroPoblado;
use App\Entity\Denunciante;
use App\Entity\DenuncianteDesaparecido;
use App\Entity\Desaparecido;
use App\Entity\Detenido;
use App\Entity\Distrito;
use App\Entity\EstadoCivil;
use App\Entity\FormaCaptacion;
use App\Entity\MenorEdad;
use App\Entity\Nacionalidad;
use App\Entity\Persona;
use App\Entity\Sexo;
use App\Entity\SituacionEncontrada;
use App\Entity\TipoDocumento;
use App\Entity\TipoExplotacion;
use App\Entity\TipoMaltrato;
use App\Entity\Tutor;
use App\Entity\Victima;
use App\Entity\VinculoFamiliar;
use Doctrine\Persistence\ObjectManager;

trait TraitParameter
{
    public static function findCentroPoblado($id, ObjectManager $em): ?CentroPoblado
    {
        return $em->getRepository(CentroPoblado::class)->find($id);
    }

    public static function findSituacion($obj, ObjectManager $em): ?SituacionEncontrada
    {
        $id = $obj['key'];

        return $em->getRepository(SituacionEncontrada::class)->find($id);
    }

    public static function findTipoDocumento($obj, ObjectManager $em): ?TipoDocumento
    {
        $id = $obj['key'];

        return $em->getRepository(TipoDocumento::class)->find($id);
    }

    public static function findSexo($obj, ObjectManager $em): ?Sexo
    {
        if (null !== $obj) {
            $id = $obj['key'];

            return $em->getRepository(Sexo::class)->find($id);
        }

        return null;
    }

    public static function findNacionalidad($obj, ObjectManager $em): ?Nacionalidad
    {
        $id = $obj['key'];

        return $em->getRepository(Nacionalidad::class)->find($id);
    }

    public static function findVinculo($obj, ObjectManager $em): ?VinculoFamiliar
    {
        $cp = null;
        $id = $obj['key'];
        if (null !== $id) {
            $cp = $em->getRepository(VinculoFamiliar::class)->find($id);
        }

        return $cp;
    }

    public static function findTipoMaltrato($obj, ObjectManager $em): ?TipoMaltrato
    {
        $id = $obj['key'];

        return $em->getRepository(TipoMaltrato::class)->find($id);
    }

    public static function findEstadoCivil($obj, ObjectManager $em): ?EstadoCivil
    {
        $id = $obj['key'];

        return $em->getRepository(EstadoCivil::class)->find($id);
    }

    public static function findDistrito($id, ObjectManager $em): ?Distrito
    {
        return $em->getRepository(Distrito::class)->find($id);
    }

    public static function findCasoViolencia($id, ObjectManager $em): ?CasoViolencia
    {
        return $em->getRepository(CasoViolencia::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findDenunciante($id, ObjectManager $em): ?Denunciante
    {
        return $em->getRepository(Denunciante::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findAgraviado($id, ObjectManager $em): ?Agraviado
    {
        return $em->getRepository(Agraviado::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findAgresor($id, ObjectManager $em): ?Agresor
    {
        return $em->getRepository(Agresor::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findCasoDesproteccion($id, ObjectManager $em): ?CasoDesproteccion
    {
        return $em->getRepository(CasoDesproteccion::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findMenor($id, ObjectManager $em): ?MenorEdad
    {
        return $em->getRepository(MenorEdad::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findTutor($id, ObjectManager $em): ?Tutor
    {
        return $em->getRepository(Tutor::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findCasoTrata($id, ObjectManager $em): ?CasoTrata
    {
        return $em->getRepository(CasoTrata::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findCasoDesaparecido($id, ObjectManager $em): ?CasoDesaparecido
    {
        return $em->getRepository(CasoDesaparecido::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findDetenido($id, ObjectManager $em): ?Detenido
    {
        return $em->getRepository(Detenido::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findVictima($id, ObjectManager $em): ?Victima
    {
        return $em->getRepository(Victima::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findDenuncianteDes($id, ObjectManager $em): ?DenuncianteDesaparecido
    {
        return $em->getRepository(DenuncianteDesaparecido::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findDesaparecido($id, ObjectManager $em): ?Desaparecido
    {
        return $em->getRepository(Desaparecido::class)->findOneBy(['codigoApp' => $id]);
    }

    public static function findFormaCaptacion($obj, ObjectManager $em): ?FormaCaptacion
    {
        $id = $obj['key'];

        return $em->getRepository(FormaCaptacion::class)->find($id);
    }

    public static function findPerson($data, ObjectManager $em): ?Persona
    {
        $numeroDocumento = $data['numero_documento'];
        $cp = null;
        if (null !== $numeroDocumento) {
            $cp = $em->getRepository(Persona::class)->findOneBy(['numeroDocumento' => $numeroDocumento]);
        }
        if (null !== $cp) {
            if (isset($data['casoDesproteccion'])) {
                $cp->setCasoDesproteccion(1);
            }
            if (isset($data['casoViolencia'])) {
                $cp->setCasoViolencia(1);
            }

            if (isset($data['casoTrata'])) {
                $cp->setCasoTrata(1);
            }

            if (isset($data['casoDesaparecido'])) {
                $cp->setCasoDesaparecido(1);
            }

            return $cp;
        }
        $persona = new Persona();
        $tipoDocumento = $data['tipo_documento_id'];
        $sexo = $data['sexo'] ?: null;
        $persona->setTipoDocumento($tipoDocumento['label']);
        $persona->setNumeroDocumento($data['numero_documento']);
        $persona->setNombres($data['nombres']);
        $persona->setApellidos($data['apellidos']);
        $persona->setCentroPoblado($data['centroPoblado']);
        $persona->setSexo($sexo ? $sexo['label'] : '');

        if (isset($data['casoDesproteccion'])) {
            $persona->setCasoDesproteccion(1);
        }
        if (isset($data['casoViolencia'])) {
            $persona->setCasoViolencia(1);
        }
        if (isset($data['casoTrata'])) {
            $persona->setCasoTrata(1);
        }
        if (isset($data['casoDesaparecido'])) {
            $persona->setCasoDesaparecido(1);
        }

        if (isset($data['edad']) && $data['edad']) {
            $persona->setEdad($data['edad']);
        }

        return $persona;
    }

    public static function getParameterApi($n): ?array
    {
        if (null !== $n) {
            return ['key' => $n->getId(), 'label' => $n->getNombre()];
        }

        return null;
    }

    public static function findExplotacion($id, ObjectManager $em): ?string
    {
        $cp = $em->getRepository(TipoExplotacion::class)->find($id);
        if (null !== $cp) {
            return $cp->getNombre();
        }

        return null;
    }

    public static function findSituacionEncontrada($id, ObjectManager $em): ?string
    {
        $cp = $em->getRepository(SituacionEncontrada::class)->find($id);
        if (null !== $cp) {
            return $cp->getNombre();
        }

        return null;
    }

    public static function findTipoMaltratos($id, ObjectManager $em): ?string
    {
        $cp = $em->getRepository(TipoMaltrato::class)->find($id);
        if (null !== $cp) {
            return $cp->getNombre();
        }

        return null;
    }
}
