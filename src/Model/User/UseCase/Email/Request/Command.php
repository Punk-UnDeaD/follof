<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $id;
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
