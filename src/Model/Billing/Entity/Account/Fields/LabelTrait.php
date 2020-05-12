<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Fields;

trait LabelTrait
{
    protected array $data;

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
