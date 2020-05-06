<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\RequiresCsrf;
use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\AddMember;
use App\ReadModel\User\UserFetcher;
use App\Security\MemberIdentity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/team")
 */
class TeamController extends AbstractController
{
    private MemberRepository $members;

    private UserFetcher $users;
    private ObjectRepository $voiceMenus;

    public function __construct(UserFetcher $users, MemberRepository $memberRepository, EntityManagerInterface $em)
    {
        $this->users = $users;
        $this->members = $memberRepository;
        $this->voiceMenus = $em->getRepository(VoiceMenu::class);
    }

    /**
     * @Route("", name="billing.team")
     */
    public function index(): Response
    {
        $member_id = $this->getUser()->getId();

        /** @var Member $member */
        $member = $this->members->find($member_id);
        $team = $member->getTeam();
        $members = $this->members->findBy(['team' => $team], ['id' => 'ASC']);
        /** @var VoiceMenu[] $voiceMenus */
        $voiceMenus = $this->voiceMenus->findBy(['team' => $team], ['id' => 'ASC']);

        return $this->render(
            'app/billing/team.html.twig',
            ['member' => $member, 'team' => $team, 'members' => $members, 'voiceMenus' => $voiceMenus]
        );
    }

    /**
     * @Route("/add", name="billing.team.addMember", methods={"POST"})
     * @RequiresCsrf(tokenId="b.t.a")
     */
    public function addMember(AddMember\Handler $handler): Response
    {
        /** @var MemberIdentity $member */
        $member = $this->getUser();
        $handler(new AddMember\Command($member->getTeamId()));

        return $this->redirectToRoute('billing.team');
    }
}
