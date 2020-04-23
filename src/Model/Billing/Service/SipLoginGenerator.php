<?php

declare(strict_types=1);

namespace App\Model\Billing\Service;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Team;

class SipLoginGenerator
{

    public function generate(Member $member): string
    {
        do {
            $login = sprintf("%'.06d", rand(0, 999999));
        } while (!$this->isLoginNotUsed($login, $member->getTeam()));

        return $login;
    }

    private function isLoginNotUsed($login, Team $team)
    {
        foreach ($team->getMembers() as $member) {
            foreach ($member->getSipAccounts() as $sipAccount) {
                if ($sipAccount->isSameLogin($login)) {
                    return false;
                }
            }
        }

        return true;
    }
}

