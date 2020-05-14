<?php

declare(strict_types=1);

namespace App\Event\Dispatcher\Message;

class DebounceMessage
{
    private object $event;

    private int $time;
    private string $id;
    private string $name;

    public function __construct(object $event, string $id, ?string $name = null)
    {
        $this->event = $event;
        $this->time = time();
        $this->id = $id;
        $this->name = $name ?? get_class($event);
    }

    public function getEvent(): object
    {
        return $this->event;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
