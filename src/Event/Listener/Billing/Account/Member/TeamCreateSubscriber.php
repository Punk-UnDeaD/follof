<?php

declare(strict_types=1);

namespace App\Event\Listener\Billing\Account\Member;

use App\Model\Billing\UseCase\CreateTeam;
use App\Model\User\Entity\User\Event\UserConfirmed;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TeamCreateSubscriber implements EventSubscriberInterface
{
    private CreateTeam\Handler $handler;
    private UserRepository $users;

    public function __construct(CreateTeam\Handler $handler, UserRepository $users)
    {
        $this->handler = $handler;
        $this->users = $users;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserConfirmed::class => 'createTeam',
        ];
    }

    public function createTeam(UserConfirmed $event)
    {
        if ($this->users->get(new Id($event->userId))->getRole()->isUser()) {
            ($this->handler)(new CreateTeam\Command($event->userId));
        }
    }
}
