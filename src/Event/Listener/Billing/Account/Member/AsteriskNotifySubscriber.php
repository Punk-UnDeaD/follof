<?php

declare(strict_types=1);

namespace App\Event\Listener\Billing\Account\Member;

use App\Model\Billing\Entity\Account\Event\MemberDataUpdated;
use App\Model\Billing\Entity\Account\Event\MemberStatusUpdated;
use App\Model\Billing\Entity\Account\Event\VoiceMenuDataUpdated;
use App\Model\Billing\Entity\Account\Event\VoiceMenuStatusUpdated;
use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Service\AsteriskNotificator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AsteriskNotifySubscriber implements EventSubscriberInterface
{
    private MemberRepository $memberRepository;

    private AsteriskNotificator $notificator;

    private EntityManagerInterface $em;

    public function __construct(
        MemberRepository $memberRepository,
        AsteriskNotificator $notificator,
        EntityManagerInterface $em
    ) {
        $this->memberRepository = $memberRepository;
        $this->notificator = $notificator;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MemberStatusUpdated::class => 'onMemberStatusUpdated',
            MemberDataUpdated::class => 'onMemberDataUpdated',
            VoiceMenuStatusUpdated::class => 'onVoiceMenuStatusUpdated',
            VoiceMenuDataUpdated::class => 'onVoiceMenuDataUpdated',
        ];
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
            'id' => $voiceMenu->getId(),
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

    public function onMemberDataUpdated(MemberDataUpdated $event)
    {
        $member = $this->memberRepository->find($event->id);
        if ($member && $member->isActive()) {
            $this->memberNotify($member);
        }
    }

    public function memberNotify(Member $member)
    {
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
                'type' => 'member',
                'id' => $member->getId(),
                'accounts' => $accounts,
                'billingId' => $member->getTeam()->getBillingId(),
                'internalNumber' => $member->getInternalNumber(),
            ]
        );
    }

    public function onMemberStatusUpdated(MemberStatusUpdated $event)
    {
        $member = $this->memberRepository->find($event->id);
        if ($member) {
            $this->memberNotify($member);
        }
    }
}
