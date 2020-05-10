<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\SetFile;

use App\Model\Billing\UseCase\VoiceMenu\BaseCommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    use BaseCommandTrait;
    /**
     * @Assert\File(mimeTypes={"audio/mpeg"})
     */
    public string $file;

    public function __construct(string $id, string $file)
    {
        $this->id = $id;
        $this->file = $file;
    }
}
