<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use Hackzilla\PasswordGenerator\Generator\RequirementPasswordGenerator;

class HumanStrongPasswordGenerator
{
    public function generate(): string
    {
        return (new RequirementPasswordGenerator())
            ->setLength(16)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_SYMBOLS, true)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_UPPER_CASE, 2)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_LOWER_CASE, 2)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_NUMBERS, 2)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_SYMBOLS, 2)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_UPPER_CASE, 8)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_LOWER_CASE, 8)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_NUMBERS, 8)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_SYMBOLS, 2)
            ->generatePassword();
    }
}