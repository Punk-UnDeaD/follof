<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Contracts\Service\ResetInterface;

class MyTokenStorage extends TokenStorage implements TokenStorageInterface, ResetInterface
{
    public function setToken(TokenInterface $token = null)
    {
        if ($token instanceof PostAuthenticationGuardToken
            && $token->getRoleNames() !== $token->getUser()->getRoles()) {
            $token = new PostAuthenticationGuardToken(
                $token->getUser(),
                $token->getProviderKey(),
                $token->getUser()->getRoles()
            );
        }
        parent::setToken($token);
    }
}
