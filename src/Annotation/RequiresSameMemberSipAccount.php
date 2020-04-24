<?php

declare(strict_types=1);

namespace App\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class RequiresSameMemberSipAccount
{
    public string $memberKey = 'member';
    public string $sipAccountKey = 'sipAccount';
}
