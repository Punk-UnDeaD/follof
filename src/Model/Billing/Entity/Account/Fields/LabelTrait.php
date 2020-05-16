<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Fields;

trait LabelTrait
{
    use DataTrait;

    protected static function defaultData(): array
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
