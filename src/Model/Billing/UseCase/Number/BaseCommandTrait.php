<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Number;

trait BaseCommandTrait
{
    /**
     * @Assert\NotBlank()
     */
    public string $number;

    public function __construct(string $number)
    {
        $this->number = $number;
    }
}
