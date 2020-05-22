<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\RequiresCsrf;
use App\Controller\ErrorHandler;
use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Repository\MemberRepository;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\Team\AddMember;
use App\Model\Billing\UseCase\Team\AddVoiceMenu;
use App\ReadModel\User\UserFetcher;
use App\Security\MemberIdentity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/team", name="billing.team")
 */
class TeamController extends AbstractController
{
    private ErrorHandler $errors;
    private MemberRepository $members;
    private UserFetcher $users;
    private ObjectRepository $voiceMenus;

    public function __construct(
        UserFetcher $users,
        MemberRepository $memberRepository,
        EntityManagerInterface $em,
        ErrorHandler $errors
    ) {
        $this->users = $users;
        $this->members = $memberRepository;
        $this->voiceMenus = $em->getRepository(VoiceMenu::class);
        $this->errors = $errors;
    }

    /**
     * @Route("", name="")
     */
    public function show(): Response
    {
        $member_id = $this->getUser()->getId();

        /** @var Member $member */
        $member = $this->members->find($member_id);
        $team = $member->getTeam();
        $members = $this->members->findBy(['team' => $team], ['id' => 'ASC']);
        /** @var VoiceMenu[] $voiceMenus */
        $voiceMenus = $this->voiceMenus->findBy(['team' => $team], ['id' => 'ASC']);

        return $this->render(
            'app/billing/team/show.html.twig',
            ['member' => $member, 'team' => $team, 'members' => $members, 'voiceMenus' => $voiceMenus]
        );
    }

    /**
     * @Route("/addMember", name=".addMember", methods={"POST"})
     * @RequiresCsrf(tokenId="b.t.a")
     */
    public function addMember(AddMember\Handler $handler): Response
    {
        /** @var MemberIdentity $member */
        $member = $this->getUser();
        $handler(new AddMember\Command($member->getTeamId()));

        return $this->redirectToRoute('billing.team');
    }

    /**
     * @Route("/addVoiceMenu", name=".addVoiceMenu")
     */
    public function addVoiceMenu(Request $request, AddVoiceMenu\Handler $handler): Response
    {
        $handler(new AddVoiceMenu\Command($this->getUser()->getTeamId()));

        return $this->redirectToRoute('billing.team');
    }
}
