<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\User\Entity\User\Role;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email = '';
    /**
     * @Assert\NotBlank()
     */
    public string $firstName = '';
    /**
     * @Assert\NotBlank()
     */
    public string $lastName = '';

    public bool $isNotify = true;

    public string $role = Role::USER;
}
