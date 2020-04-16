<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Role;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthFixture extends Fixture
{
    public const REFERENCE_ADMIN = 'test_auth_admin';
    public const REFERENCE_USER = 'test_auth_user';

    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public static function userCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'auth-user@app.test',
            'PHP_AUTH_PW' => 'password',
        ];
    }

    public static function adminCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'auth-admin@app.test',
            'PHP_AUTH_PW' => 'password',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash(static::adminCredentials()['PHP_AUTH_PW']);

        $user = (new UserBuilder())
            ->viaEmail(new Email(static::adminCredentials()['PHP_AUTH_USER']), $hash)
            ->confirmed()
            ->build();

        $manager->persist($user);
        $this->setReference(self::REFERENCE_USER, $user);

        $hash = $this->hasher->hash(static::userCredentials()['PHP_AUTH_PW']);

        $admin = (new UserBuilder())
            ->viaEmail(new Email(static::userCredentials()['PHP_AUTH_USER']), $hash)
            ->confirmed()
            ->withRole(Role::admin())
            ->build();

        $manager->persist($admin);
        $this->setReference(self::REFERENCE_ADMIN, $admin);

        $manager->flush();
    }
}
