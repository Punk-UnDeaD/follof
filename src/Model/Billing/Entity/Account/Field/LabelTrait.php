<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Field;

trait LabelTrait
{
    use DataTrait;

    public static function defaultData(): array
    {
        return ['label' => null];
    }

    public function getLabel(): ?string
    {
        return $this->data['label'];
    }

    public function setLabel(?string $label = null): self
    {
        $this->data['label'] = $label;

        return $this;
    }
}
