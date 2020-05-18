<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\SipAccount;

use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class WaitTimeTest extends TestCase
{
    const TIME_ALPHA = 50;
    const TIME_BRAVO = 80;

    public function test()
    {
        $sipAccount = (new TeamBuilder())->getSipAccount();
        $this->assertEquals(60, $sipAccount->getWaitTime());
        $sipAccount->setWaitTime(self::TIME_ALPHA);
        $this->assertEquals(self::TIME_ALPHA, $sipAccount->getWaitTime());

        $sipAccount->setWaitTime(self::TIME_BRAVO);
        $this->assertEquals(self::TIME_BRAVO, $sipAccount->getWaitTime());
        $sipAccount->setWaitTime();
        $this->assertEquals(60, $sipAccount->getWaitTime());
    }
}
