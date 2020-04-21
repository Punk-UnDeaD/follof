<?php

namespace App\Controller\BillingProfile;

use App\Annotation\RequiresCsrf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Billing\UseCase\BlockMember;
use App\Model\Billing\UseCase\ActivateMember;

/**
 * @Route("/profile/team/{member}")
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/block", name="billing.team.member.block")
     * @RequiresCsrf(tokenId="billing.team.member.toggle")
     * @param string $member
     * @param BlockMember\Handler $handler
     * @return JsonResponse
     */
    public function block(string $member, BlockMember\Handler $handler): JsonResponse
    {
        $handler(new BlockMember\Command($member));

        return $this->json(['status'=>'ok']);
    }

    /**
     * @Route("/activate", name="billing.team.member.activate")
     * @RequiresCsrf(tokenId="billing.team.member.toggle")
     * @param string $member
     * @param ActivateMember\Handler $handler
     * @return JsonResponse
     */
    public function activate(string $member, ActivateMember\Handler $handler): JsonResponse
    {
        $handler(new ActivateMember\Command($member));

        return $this->json(['status'=>'ok']);
    }
}
