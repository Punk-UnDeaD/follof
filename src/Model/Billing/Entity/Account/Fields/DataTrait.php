<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Fields;

trait DataTrait
{
    /**
     * @ORM\Column(type="json", options={"default" : "{}"})
     */
    protected array $data;

    /**
     * @ORM\PostLoad()
     */
    public function checkData(): void
    {
        $this->data = array_merge(static::defaultData(), $this->data);
    }

    protected static function defaultData(): array
    {
        return [];
    }
}
