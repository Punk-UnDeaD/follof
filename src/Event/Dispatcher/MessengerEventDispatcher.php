<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

use App\Event\Dispatcher\Message\DebounceMessage;
use App\Event\Dispatcher\Message\Message;
use App\Model\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class MessengerEventDispatcher implements EventDispatcher
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->bus->dispatch(new Message($event));
        }
    }

    public function dispatchDebounce(object $event, $id, int $delay = 1, $name = null): void
    {
        $this->bus->dispatch(
            new DebounceMessage($event, $id, $name ?? get_class($event)),
            [new DelayStamp($delay * 1000)]
        );
    }
}
