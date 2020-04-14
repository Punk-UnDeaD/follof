<?php


namespace App\Tests\Builder\User;


use App\Model\User\Entity\User\SipAccount;
use App\Model\User\Entity\User\User;

class SipAccountBuilder
{
    static function create(?User $user = null, string $login = '', string $password = '')
    {
        $user ??= (new UserBuilder())->viaEmail()->confirmed()->build();

        return new SipAccount($user, $login, $password);
    }
}