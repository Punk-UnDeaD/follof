<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Fields;

use Webmozart\Assert\Assert;

trait WaitTimeTrait
{
    use DataTrait;

    public static function defaultData(): array
    {
        return ['waitTime' => 60];
    }

    public function getWaitTime(): int
    {
        return $this->data['waitTime'];
    }

    public function setWaitTime(int $waitTime = 60): self
    {
        Assert::greaterThanEq($waitTime, 15);
        Assert::lessThanEq($waitTime, 90);

        $this->data['waitTime'] = $waitTime;
        $this->onUpdateWaitTime();

        return $this;
    }

    protected function onUpdateWaitTime()
    {

    }

}
