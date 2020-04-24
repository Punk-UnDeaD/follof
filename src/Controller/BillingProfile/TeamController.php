<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\RequiresCsrf;
use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\UseCase\AddMember;
use App\ReadModel\User\UserFetcher;
use App\Security\MemberIdentity;
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

    public function __construct(UserFetcher $users, MemberRepository $em)
    {
        $this->users = $users;
        $this->members = $em;
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

        return $this->render(
            'app/billing/team.html.twig',
            ['member' => $member, 'team' => $team, 'members' => $members]
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
