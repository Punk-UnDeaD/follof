<?php

declare(strict_types=1);

namespace App\Event\Dispatcher\Message;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DebounceHandler implements MessageHandlerInterface
{
    private array $log = [];

    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DebounceMessage $message)
    {
        $id = "{$message->getName()}:{$message->getId()}";
        $last = $this->log[$id] ?? 0;
        if ($last < $message->getTime()) {
            $this->dispatcher->dispatch($message->getEvent(), $message->getName());
        }
        $this->log[$id] = time();
    }
}
