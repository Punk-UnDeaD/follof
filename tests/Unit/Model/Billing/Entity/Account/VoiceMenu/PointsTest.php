<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\VoiceMenu;

use App\Model\Billing\Entity\Account\DataType\InternalNumber;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class PointsTest extends TestCase
{
    private const NUMBER_ALPHA = '123';

    public function test()
    {
        $voiceMenu = (new TeamBuilder())->getVoiceMenu();
        for ($i = 9; $i >= 0; --$i) {
            $voiceMenu->addPoint((string) $i, new InternalNumber(self::NUMBER_ALPHA.'-'.$i));
            $this->assertCount(10 - $i, $voiceMenu->getPoints());
            $this->assertEquals(self::NUMBER_ALPHA.'-'.$i, $voiceMenu->getPoints()[$i][0]->getNumber());
        }
        for ($i = 0; $i <= 9; ++$i) {
            $voiceMenu->removePoint((string) $i);
            $this->assertCount(9 - $i, $voiceMenu->getPoints());
            $this->assertArrayNotHasKey($i, $voiceMenu->getPoints());
        }
    }

    public function testAddPointTwice()
    {
        $this->expectExceptionMessage('Key already used.');
        (new TeamBuilder())->getVoiceMenu()
            ->addPoint('1', new InternalNumber(self::NUMBER_ALPHA))
            ->addPoint('1', new InternalNumber(self::NUMBER_ALPHA));
    }

    /**
     * @dataProvider getWrongKeys
     */
    public function testPointWrongKey(...$params)
    {
        $this->expectExceptionMessage('Wrong key.');
        (new TeamBuilder())->getVoiceMenu()
            ->addPoint(
                ...$params
            );
    }

    public function testRemovePointTwice()
    {
        $this->expectExceptionMessage('Key already empty.');
        (new TeamBuilder())->getVoiceMenu()
            ->addPoint('1', new InternalNumber(self::NUMBER_ALPHA))
            ->removePoint('1')
            ->removePoint('1');
    }

    public function getWrongKeys(): iterable
    {
        yield ['a', new InternalNumber(self::NUMBER_ALPHA)];
        yield ['-', new InternalNumber(self::NUMBER_ALPHA)];
        yield ['9999', new InternalNumber(self::NUMBER_ALPHA)];
    }
}
