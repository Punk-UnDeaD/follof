<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\SipAccount;

use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    const LABEL_ALPHA = 'label alpha';
    const LABEL_BRAVO = 'label bravo';

    public function test()
    {
        $sipAccount = (new TeamBuilder())->getSipAccount();
        $this->assertRegExp('/sip-\d+/', $sipAccount->getLabel());
        $sipAccount->setLabel(self::LABEL_ALPHA);
        $this->assertEquals(self::LABEL_ALPHA, $sipAccount->getLabel());
        $sipAccount->setLabel(self::LABEL_BRAVO);
        $this->assertEquals(self::LABEL_BRAVO, $sipAccount->getLabel());
        $sipAccount->setLabel();
        $this->assertNull($sipAccount->getLabel());
    }
}
