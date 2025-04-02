<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class LinkGenerator
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function links(string $route): array
    {
        return [
            'list' => $this->checker('list', $route),
            'view' => $this->checker('view', $route),
            'new' => $this->checker('new', $route),
            'edit' => $this->checker('edit', $route),
            'delete' => $this->checker('delete', $route),
            'print' => $this->checker('print', $route),
            'export' => $this->checker('export', $route),
            'master' => $this->checker('master', $route),
        ];
    }

    private function checker(string $attribute, $subject)
    {
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return $this->authorizationChecker->isGranted($attribute, $subject);
    }
}
