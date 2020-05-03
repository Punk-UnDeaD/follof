<?php

declare(strict_types=1);

namespace App\Event\Listener\Billing\Account\Member;

use App\Model\Billing\Entity\Account\Event\MemberSipPoolUpdated;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Service\AsteriskNotificator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AsteriskNotifySubscriber implements EventSubscriberInterface
{
    private MemberRepository $repository;

    private AsteriskNotificator $notificator;

    public function __construct(MemberRepository $repository, AsteriskNotificator $notificator)
    {
        $this->repository = $repository;
        $this->notificator = $notificator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MemberSipPoolUpdated::class => 'onPoolUpdated',
        ];
    }

    public function onPoolUpdated(MemberSipPoolUpdated $event)
    {
        $member = $this->repository->find($event->memberId);
        $accounts = [];
        if ($member && $member->isActive() && $member->getTeam()->getUser()->isActive()) {
            foreach ($member->getSipAccounts() as $account) {
                if ($account->isActive()) {
                    $accounts[] = [
                        'login' => $account->getLogin().'@'.$member->getTeam()->getBillingId(),
                        'password' => $account->getPassword(),
                    ];
                }
            }
        }
        $this->notificator->poolUpdate(
            [
                'id' => $event->memberId,
                'accounts' => $accounts,
            ]
        );
    }
}
