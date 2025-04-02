<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Security\Security;
use App\Service\Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function denyAccess(string $attribute, string $subject, object $object = null, string $message = 'Acceso denegado...'): void
    {
        $this->security->denyAccessUnlessGranted($attribute, $subject, $object, $message);
    }

    protected function hasAccess(string $attribute, string $subject, object $object = null): bool
    {
        return $this->security->hasAccess($attribute, $subject, $object);
    }

    protected function isSuperAdmin(): bool
    {
        return $this->security->isSuperAdmin();
    }

    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters = array_merge($parameters, ['access' => $this->security]);

        return parent::render($view, $parameters, $response);
    }

    /**
     * @param Error[] $errors
     */
    protected function addErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->addFlash('danger', $error->message());
        }
    }
}
