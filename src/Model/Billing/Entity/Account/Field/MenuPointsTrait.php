<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Field;

use App\Model\Billing\Entity\Account\DataType\InternalNumber;
use Webmozart\Assert\Assert;

trait MenuPointsTrait
{
    use DataTrait;

    public static function defaultData(): array
    {
        return ['points' => []];
    }

    /**
     * @return InternalNumber[][]
     */
    public function getPoints(): array
    {
        return $this->data['points'];
    }

    public function removePoint(string $key): self
    {
        Assert::keyExists($this->data['points'], $key, 'Key already empty.');
        unset($this->data['points'][$key]);

        return $this->onUpdateMenuPoints();
    }

    abstract protected function onUpdateMenuPoints(): self;

    public function addPoint(string $key, InternalNumber ...$numbers): self
    {
        Assert::Regex($key, '/^\d{1,3}$/', 'Wrong key.');
        Assert::keyNotExists($this->data['points'], $key, 'Key already used.');
        $this->data['points'][$key] = $numbers;

        return $this->onUpdateMenuPoints();
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkData(): void
    {
        foreach ($this->data['points'] as $k => $points) {
            $this->data['points'][$k] = array_map(fn ($point) => new InternalNumber($point), $points);
        }
    }
}
