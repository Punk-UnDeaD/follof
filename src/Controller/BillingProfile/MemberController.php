<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\Guid;
use App\Annotation\RequiresCsrf;
use App\Annotation\RequiresSameTeamMember;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\UseCase\ActivateMember;
use App\Model\Billing\UseCase\AddSipAccount;
use App\Model\Billing\UseCase\BlockMember;
use App\Model\Billing\UseCase\UpdateMemberCredentials;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/team/member/{member}", requirements={"member"=Guid::PATTERN})
 * @RequiresSameTeamMember
 */
class MemberController extends AbstractController
{
    /**
     * @throws EntityNotFoundException
     * @Route("", name="billing.team.member.show")
     */
    public function show(string $member, MemberRepository $members): Response
    {
        return $this->render('app/billing/member.html.twig', ['member' => $members->get($member)]);
    }

    /**
     * @Route("/addSipAccount", name="billing.team.member.addSipAccount")
     */
    public function addSipAccount(string $member, AddSipAccount\Handler $handler): Response
    {
        $handler(new AddSipAccount\Command($member));

        return $this->redirectToRoute('billing.team.member.show', ['member' => $member]);
    }

    /**
     * @Route("/block", name="billing.team.member.block", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.toggleStatus")
     */
    public function block(string $member, BlockMember\Handler $handler): JsonResponse
    {
        $handler(new BlockMember\Command($member));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/activate", name="billing.team.member.activate", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.toggleStatus")
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

    /**
     * @Route("/updateCredentials", name="billing.team.member.updateCredentials")
     * @RequiresCsrf
     */
    public function updateCredentials(
        string $member,
        UpdateMemberCredentials\Handler $handler,
        Request $request
    ): RedirectResponse {
        $login = $request->get('login');
        $password = $request->get('password');
        try {
            $handler(new UpdateMemberCredentials\Command($member, $login, $password));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('billing.team.member.show', ['member' => $member]);
    }
}
