<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\Member;

use App\Model\Billing\Entity\Account\InternalNumber;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class FallbackNumberTest extends TestCase
{
    private const NUMBER_ALPHA = '123-1';
    private const NUMBER_BRAVO = '123-2';
    private const NUMBER_LIKE_BRAVO = '1232';

    public function test()
    {
        $owner = (new TeamBuilder())->getOwner();
        $owner->setFallbackNumber(new InternalNumber(self::NUMBER_ALPHA));
        $this->assertEquals(self::NUMBER_ALPHA, $owner->getFallbackNumber()->getNumber());
        $owner->setFallbackNumber(new InternalNumber(self::NUMBER_BRAVO));
        $this->assertEquals(self::NUMBER_BRAVO, $owner->getFallbackNumber()->getNumber());
        $owner->setFallbackNumber(new InternalNumber(self::NUMBER_LIKE_BRAVO));
        $this->assertEquals(self::NUMBER_LIKE_BRAVO, $owner->getFallbackNumber()->getNumber());
        $this->assertTrue($owner->getFallbackNumber()->isSame(new InternalNumber(self::NUMBER_BRAVO)));
    }
}
