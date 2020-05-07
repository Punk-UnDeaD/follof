<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Team;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractCommand
{
    /**
     * @Assert\NotBlank()
     */
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
