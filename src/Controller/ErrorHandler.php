<?php

declare(strict_types=1);

namespace App\Controller;

use LogicException;
use Psr\Log\LoggerInterface;

class ErrorHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(LogicException $e): void
    {
        $this->logger->warning($e->getMessage(), ['exception' => $e]);
    }
}
