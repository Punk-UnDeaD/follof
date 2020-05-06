<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

interface HasNumber
{
    public function getInternalNumber(): InternalNumber;
}
