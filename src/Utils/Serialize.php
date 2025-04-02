<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

final class Serialize
{
    public static function json($data) // http://jmsyst.com/bundles/JMSSerializerBundle // Ampliar la funcionalidad
    {
        $normalizer = new GetSetMethodNormalizer();
        $encoder = new JsonEncoder();

        $normalizer->supportsNormalization(function ($object) {
            return $object->getId();
        });

        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [$encoder]);
        $json = $serializer->serialize($data, 'json');

        return self::jsonContent($json);
    }

    public static function jsonContent($json)
    {
        $response = new Response();
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
