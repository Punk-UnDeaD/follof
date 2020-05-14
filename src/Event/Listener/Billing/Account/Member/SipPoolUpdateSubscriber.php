<?php

declare(strict_types=1);

namespace App\Event\Listener\Billing\Account\Member;

use App\Event\Dispatcher\MessengerEventDispatcher;
use App\Model\Billing\Entity\Account\Event\MemberStatusUpdated;
use App\Model\Billing\Entity\Account\Team;
use App\Model\User\Entity\User\Event\UserActivated;
use App\Model\User\Entity\User\Event\UserBlocked;
use App\Model\User\Entity\User\Event\UserEventBase;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SipPoolUpdateSubscriber implements EventSubscriberInterface
{
    private ObjectRepository $teamRepository;

    private MessengerEventDispatcher $eventDispatcher;

    private UserRepository $users;

    public function __construct(
        EntityManagerInterface $em,
        MessengerEventDispatcher $eventDispatcher,
        UserRepository $users
    ) {
        $this->teamRepository = $em->getRepository(Team::class);
        $this->eventDispatcher = $eventDispatcher;
        $this->users = $users;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserBlocked::class => 'triggerPoolUpdate',
            UserActivated::class => 'triggerPoolUpdate',
        ];
    }

    public function triggerPoolUpdate(UserEventBase $event)
    {
        $user = $this->users->get(new Id($event->userId));
        if ($user->getRole()->isUser()) {
            /** @var Team $team */
            $team = $this->teamRepository->findOneBy(['user' => $user]);
            foreach ($team->getMembers() as $member) {
                $event = new MemberStatusUpdated($member->getId()->getValue());
                $this->eventDispatcher->dispatch([$event]);
            }
        }
    }
}
