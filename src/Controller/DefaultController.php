<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CasoDesaparecido;
use App\Entity\CasoDesproteccion;
use App\Entity\CasoTrata;
use App\Entity\CasoViolencia;
use App\Entity\Usuario;
use App\Traits\TraitDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    use TraitDate;

    #[Route(path: '/', name: 'homepage', methods: ['GET'])]
    public function home(EntityManagerInterface $em): Response
    {
        /** @var Usuario $user */
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $centro = null;
        $distrito = $user->getDistrito();
        $provincia = $user->getProvincia();
        $centroNombre = '';
        if ('ROLE_TENIENTE' === $role) {
            $centro = $user->getCentroPoblado()->getId();
            $centroNombre = $user->getCentroPoblado()->getNombre();
        }

        if ('ROLE_DEMUNA' === $role || 'ROLE_COMISARIA' === $role || 'ROLE_SUPREFECTO' === $role) {
            $provincia = $user->getProvincia();
            $distrito = $user->getDistrito();
        }

        $anioActual = date('Y');

        $casoscv = $em->getRepository(CasoViolencia::class)->filterChart($centro, $provincia, $distrito);
        $datacv = self::exportMesesCv($casoscv);

        $casosd = $em->getRepository(CasoDesproteccion::class)->filterChart($centro, $provincia, $distrito);
        $datacd = self::exportMesesCv($casosd);

        $casostp = $em->getRepository(CasoTrata::class)->filterChart($centro, $provincia, $distrito);
        $datatp = self::exportMesesCv($casostp);

        $casospd = $em->getRepository(CasoDesaparecido::class)->filterChart($centro, $provincia, $distrito);
        $datapd = self::exportMesesCv($casospd);

        $view = 'default/homepage.html.twig';

        switch ($role) {
            case 'ROLE_DEMUNA':
                $view = 'default/homeDemuna.html.twig';
                break;
            case 'ROLE_COMISARIA':
                $view = 'default/homeComisaria.html.twig';
                break;
            case 'ROLE_SUPREFECTO':
                $view = 'default/homeSuprefectura.html.twig';
                break;
            case 'ROLE_TENIENTE':
                $view = 'default/homeTeniente.html.twig';
                break;
        }

        return $this->render($view,
            [
                'anioActual' => $anioActual,
                'c1' => array_sum($datacv),
                'c2' => array_sum($datacd),
                'c3' => array_sum($datatp),
                'c4' => array_sum($datapd),
                'datacv' => implode(',', $datacv),
                'datacd' => implode(',', $datacd),
                'datapd' => implode(',', $datapd),
                'datatp' => implode(',', $datatp),
                'centroPoblado' => $centroNombre,
            ]
        );
    }
}
