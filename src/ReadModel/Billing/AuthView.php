<?php

declare(strict_types=1);

namespace App\ReadModel\Billing;

class AuthView
{
    public $id;
    public $user_id;
    public $team_id;
    public $login;
    public $password_hash;
    public $role;
    public $user_status;
    public $member_status;
}
