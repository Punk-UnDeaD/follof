<?php

namespace App\Controller\BillingProfile;

use App\Annotation\RequiresCsrf;
use App\Annotation\RequiresSameTeamMember;
use App\Model\Billing\Entity\Account\MemberRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Billing\UseCase\BlockMember;
use App\Model\Billing\UseCase\ActivateMember;
use App\Model\Billing\UseCase\AddSipAccount;

/**
 * @Route("/profile/team/{member}")
 * @RequiresSameTeamMember(key="member")
 */
class MemberController extends AbstractController
{
    /**
     * @param string $member
     * @param MemberRepository $members
     * @return Response
     * @throws EntityNotFoundException
     * @Route("", name="billing.team.member.show")
     */
    public function show(string $member, MemberRepository $members): Response
    {
        return $this->render('app/billing/member.html.twig', ['member' => $members->get($member)]);
    }


    /**
     * @param string $member
     * @param AddSipAccount\Handler $handler
     * @return Response
     * @Route("/addSip", name="billing.team.member.addSipAccount")
     */
    public function addSipAccount(string $member, AddSipAccount\Handler $handler): Response
    {
        $handler(new AddSipAccount\Command($member));

        return $this->redirectToRoute('billing.team.member.show', ['member' => $member]);
    }


    /**
     * @Route("/block", name="billing.team.member.block", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.toggleStatus")
     * @param string $member
     * @param BlockMember\Handler $handler
     * @return JsonResponse
     */
    public function block(string $member, BlockMember\Handler $handler): JsonResponse
    {
        $handler(new BlockMember\Command($member));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/activate", name="billing.team.member.activate", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.toggleStatus")
     * @param string $member
     * @param ActivateMember\Handler $handler
     * @return JsonResponse
     */
    public function activate(string $member, ActivateMember\Handler $handler): JsonResponse
    {
        $handler(new ActivateMember\Command($member));

        return $this->json(['status' => 'ok']);
    }

//    /**
//     * @Route("/status", name="billing.team.member.status", defaults={"_format": "json"})
//     * @param string $member
//     * @param ActivateMember\Handler $handler
//     * @return JsonResponse
//     */
//    public function status(string $member, ActivateMember\Handler $handler): JsonResponse
//    {
//        $handler(new ActivateMember\Command($member));
//
//        return $this->json(['status' => 'ok']);
//    }

}
