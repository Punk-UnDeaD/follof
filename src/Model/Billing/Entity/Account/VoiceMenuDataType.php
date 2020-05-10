<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class VoiceMenuDataType extends JsonType
{
    public const NAME = 'billing_voice_menu_data';

    public function getName()
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $data = parent::convertToPHPValue($value, $platform);
        if (!$data) {
            return $data;
        }
        $data = array_merge(self::default(), $data);
        foreach ($data['points'] as $k => $p) {
            $data['points'][$k] = new InternalNumber($p);
        }

        return $data;
    }

    public static function default()
    {
        return ['points' => [], 'label' => null];
    }
}
