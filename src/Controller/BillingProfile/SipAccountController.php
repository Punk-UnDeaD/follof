<?php


namespace App\Controller\BillingProfile;


use App\Annotation\RequiresCsrf;
use App\Annotation\RequiresSameMemberSipAccount;
use App\Annotation\RequiresSameTeamMember;
use App\Model\Billing\Entity\Account\SipAccount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Billing\UseCase\ActivateSipAccount;
use App\Model\Billing\UseCase\BlockSipAccount;

/**
 * @Route("/profile/team/{member}/{sipAccount}")
 * @RequiresSameTeamMember(key="member")
 * @RequiresSameMemberSipAccount
 */

class SipAccountController extends AbstractController
{
    /**
     * @Route("/block", name="billing.team.member.sipAccount.block", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.sipAccount.toggleStatus")
     * @param string $sipAccount
     * @param BlockSipAccount\Handler $handler
     * @return JsonResponse
     */
    public function block(SipAccount $sipAccount, BlockSipAccount\Handler $handler): JsonResponse
    {
        $handler(new BlockSipAccount\Command($sipAccount->getId()->getValue()));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/activate", name="billing.team.member.sipAccount.activate", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.sipAccount.toggleStatus")
     * @param string $sipAccount
     * @param ActivateSipAccount\Handler $handler
     * @return JsonResponse
     */
    public function activate(string $sipAccount, ActivateSipAccount\Handler $handler): JsonResponse
    {
        $handler(new ActivateSipAccount\Command($sipAccount));

        return $this->json(['status' => 'ok']);
    }
}
