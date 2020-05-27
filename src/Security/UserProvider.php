<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\Billing;
use App\ReadModel\Billing\MemberFetcher;
use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserFetcher $users;

    private MemberFetcher $members;

    public function __construct(UserFetcher $users, MemberFetcher $members)
    {
        $this->users = $users;
        $this->members = $members;
    }

    public function loadUserByUsername($username): UserInterface
    {
        if ($member = $this->members->loadByLogin($username)) {
            return self::identityByMember($member);
        }
        $user = $this->loadUser($username);

        return self::identityByUser($user, $username);
    }

    public function refreshUser(UserInterface $identity): UserInterface
    {
        if ($identity instanceof MemberIdentity) {
            if ($member = $this->members->getAuthView($identity->getId())) {
                return self::identityByMember($member);
            }
            throw new BadCredentialsException('');
        }

        if ($identity instanceof UserIdentity) {
            if ($member = $this->members->loadByLogin($identity->getUsername())) {
                return self::identityByMember($member);
            }
        }

        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class '.\get_class($identity));
        }

        $user = $this->loadUser($identity->getUsername());

        return self::identityByUser($user, $identity->getUsername());
    }

    public function supportsClass($class): bool
    {
        return UserIdentity::class === $class || MemberIdentity::class === $class;
    }

    private function loadUser($username): AuthView
    {
        $chunks = explode(':', $username);

        if (2 === \count($chunks) && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($username)) {
            return $user;
        }

        throw new UsernameNotFoundException('');
    }

    private static function identityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email ?: $username,
            $user->password_hash ?: '',
            $user->name ?: $username,
            $user->role,
            $user->status
        );
    }

    private static function identityByMember(Billing\AuthView $member)
    {
        return new MemberIdentity($member);
    }
}
