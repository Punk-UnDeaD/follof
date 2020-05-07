<?php

declare(strict_types=1);

namespace App\Event\Listener\Billing\Account\Member;

use App\Model\Billing\UseCase\Team\Create;
use App\Model\User\Entity\User\Event\UserCreated;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TeamCreateSubscriber implements EventSubscriberInterface
{
    private Create\Handler $handler;
    private UserRepository $users;

    public function __construct(Create\Handler $handler, UserRepository $users)
    {
        $this->handler = $handler;
        $this->users = $users;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserCreated::class => 'createTeam',
        ];
    }

    public function createTeam(UserCreated $event)
    {
        if ($this->users->get(new Id($event->userId))->getRole()->isUser()) {
            ($this->handler)(new Create\Command($event->userId));
        }
    }
}
