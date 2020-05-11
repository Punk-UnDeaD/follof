<?php

declare(strict_types=1);

namespace App\Event\Listener\Billing\Account\Member;

use App\Controller\BillingProfile\VoiceMenuController;
use App\Model\Billing\Entity\Account\Event\MemberSipPoolUpdated;
use App\Model\Billing\Entity\Account\Event\VoiceMenuDataUpdated;
use App\Model\Billing\Entity\Account\Event\VoiceMenuStatusUpdated;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Service\AsteriskNotificator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AsteriskNotifySubscriber implements EventSubscriberInterface
{
    private MemberRepository $repository;

    private AsteriskNotificator $notificator;

    private EntityManagerInterface $em;

    public function __construct(
        MemberRepository $repository,
        AsteriskNotificator $notificator,
        EntityManagerInterface $em
    ) {
        $this->repository = $repository;
        $this->notificator = $notificator;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MemberSipPoolUpdated::class => 'onPoolUpdated',
            VoiceMenuStatusUpdated::class => 'onVoiceMenuStatusUpdated',
            VoiceMenuDataUpdated::class => 'onVoiceMenuDataUpdated',
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
                        'login' => $account->getLogin().'/'.$member->getTeam()->getBillingId(),
                        'password' => $account->getPassword(),
                    ];
                }
            }
        }
        $this->notificator->notify(
            [
                'id' => $event->memberId,
                'accounts' => $accounts,
            ]
        );
    }

    public function onVoiceMenuDataUpdated(VoiceMenuDataUpdated $event)
    {
        /** @var VoiceMenu $voiceMenu */
        $voiceMenu = $this->em->getRepository(VoiceMenu::class)->find($event->id);
        if ($voiceMenu && $voiceMenu->isActive()) {
            $this->menuNotify($voiceMenu);
        }
    }

    private function menuNotify(VoiceMenu $voiceMenu)
    {
        $data = [
            'type' => 'voiceMenu',
            'id' => $voiceMenu->getId()->getValue(),
            'billingId' => $voiceMenu->getTeam()->getBillingId(),
        ];
        if ($voiceMenu->isActive() && $voiceMenu->getTeam()->getUser()->isActive()) {
            $data +=
                [
                    'isInputAllowed' => $voiceMenu->isInputAllowed(),
                    'file' => $voiceMenu->getFile(),
                    'internalNumber' => $voiceMenu->getInternalNumber(),
                    'points' => $voiceMenu->getPoints(),
                ];
        }

        $this->notificator->notify($data);
    }

    public function onVoiceMenuStatusUpdated(VoiceMenuStatusUpdated $event)
    {
        /** @var VoiceMenu $voiceMenu */
        $voiceMenu = $this->em->getRepository(VoiceMenu::class)->find($event->id);
        if ($voiceMenu) {
            $this->menuNotify($voiceMenu);
        }
    }
}
