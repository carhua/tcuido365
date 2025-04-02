<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    #[Route(path: '/login', name: 'security_login')]
    public function login(Request $request, Security $security, AuthenticationUtils $helper): Response
    {
        // if user is already logged in, don't display the login page again
        if ($security->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return $this->redirectToRoute('homepage');
        }

        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('homepage'));

        return $this->render('security/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    #[Route(path: '/recovery', name: 'security_recovery', methods: ['GET'])]
    public function recovery(Request $request): Response
    {
        dd('777777777777777777');
        // if user is already logged in, don't display the login page again

    }

    #[Route(path: '/logout', name: 'security_logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
