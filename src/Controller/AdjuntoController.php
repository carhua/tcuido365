<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\Adjunto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdjuntoController extends AbstractController
{
    #[Route(path: '/download/{secure}', name: 'adjunto_download', methods: 'GET')]
    public function download(Adjunto $adjunto): Response
    {
        $path = $this->getParameter('adjunto_directory').$adjunto->getRuta().$adjunto->getSecure();

        return $this->file($path, $adjunto->getNombre());
    }
}
