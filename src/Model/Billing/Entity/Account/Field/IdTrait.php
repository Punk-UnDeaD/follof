<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Field;

use App\Model\Billing\Entity\Account\DataType\Id;

trait IdTrait
{
    /**
     * @ORM\Column(type="billing_guid")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

}