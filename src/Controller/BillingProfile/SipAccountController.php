<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\RequiresCsrf;
use App\Annotation\RequiresSameMemberSipAccount;
use App\Annotation\RequiresSameTeamMember;
use App\Model\Billing\UseCase\ActivateSipAccount;
use App\Model\Billing\UseCase\BlockSipAccount;
use App\Model\Billing\UseCase\SipAccount\UpdateLogin;
use App\Model\Billing\UseCase\SipAccount\UpdatePassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/team/member/{member}/{sipAccount}")
 * @RequiresSameTeamMember
 * @RequiresSameMemberSipAccount
 */
class SipAccountController extends AbstractController
{
    /**
     * @Route("/updatePassword", name="billing.team.member.sipAccount.updatePassword", format="json")
     * @RequiresCsrf()
     */
    public function updatePassword(string $sipAccount, UpdatePassword\Handler $handler, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $handler(new UpdatePassword\Command($sipAccount, $data['value']));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/updateLogin", name="billing.team.member.sipAccount.updateLogin", format="json")
     * @RequiresCsrf()
     */
    public function updateLogin(string $sipAccount, UpdateLogin\Handler $handler, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $handler(new UpdateLogin\Command($sipAccount, $data['value']));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/block", name="billing.team.member.sipAccount.block", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.sipAccount.toggleStatus")
     */
    public function block(string $sipAccount, BlockSipAccount\Handler $handler): JsonResponse
    {
        $handler(new BlockSipAccount\Command($sipAccount));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/activate", name="billing.team.member.sipAccount.activate", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.sipAccount.toggleStatus")
     */
    public function activate(string $sipAccount, ActivateSipAccount\Handler $handler): JsonResponse
    {
        $handler(new ActivateSipAccount\Command($sipAccount));

        return $this->json(['status' => 'ok']);
    }
}
