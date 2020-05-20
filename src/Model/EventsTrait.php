<?php

declare(strict_types=1);

namespace App\Model;

trait EventsTrait
{
    private $recordedEvents = [];

    protected function recordEvent(object $event): self
    {
        $this->recordedEvents[] = $event;

        return $this;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }
}
