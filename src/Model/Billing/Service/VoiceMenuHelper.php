<?php

declare(strict_types=1);

namespace App\Model\Billing\Service;

use App\Model\Billing\Entity\Account\VoiceMenu;

class VoiceMenuHelper
{
    public function proposeFileName(VoiceMenu $voiceMenu, $ext): string
    {
        $ext = $ext ? ".$ext" : '';
        do {
            $fname = 'files/billing/'.$voiceMenu->getTeam()->getBillingId().'/vm/'.md5(random_bytes(20)).$ext;
        } while (is_file($fname));

        return $fname;
    }
}
