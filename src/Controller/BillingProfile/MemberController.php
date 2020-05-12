<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\Guid;
use App\Annotation\RequiresCsrf;
use App\Annotation\RequiresSameTeamMember;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\UseCase\Member\Activate;
use App\Model\Billing\UseCase\Member\AddSipAccount;
use App\Model\Billing\UseCase\Member\Block;
use App\Model\Billing\UseCase\Member\SetInternalNumber;
use App\Model\Billing\UseCase\Member\SetLabel;
use App\Model\Billing\UseCase\Member\UpdateCredentials;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/team/member/{member}", name="billing.team.member", requirements={"member"=Guid::PATTERN})
 * @RequiresSameTeamMember
 */
class MemberController extends AbstractController
{
    /**
     * @throws EntityNotFoundException
     * @Route("", name="")
     */
    public function show(string $member, MemberRepository $members): Response
    {
        return $this->render('app/billing/member.html.twig', ['member' => $members->get($member)]);
    }

    /**
     * @Route("/addSipAccount", name=".addSipAccount")
     */
    public function addSipAccount(string $member, AddSipAccount\Handler $handler): Response
    {
        $handler(new AddSipAccount\Command($member));

        return $this->redirectToRoute('billing.team.member', ['member' => $member]);
    }

    /**
     * @Route("/block", name=".block", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.toggleStatus")
     */
    public function block(string $member, Block\Handler $handler): JsonResponse
    {
        $handler(new Block\Command($member));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/activate", name=".activate", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.member.toggleStatus")
     */
    public function activate(string $member, Activate\Handler $handler): JsonResponse
    {
        $handler(new Activate\Command($member));

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
     * @Route("/updateCredentials", name=".updateCredentials")
     * @RequiresCsrf
     */
    public function updateCredentials(
        string $member,
        UpdateCredentials\Handler $handler,
        Request $request
    ): RedirectResponse {
        $login = $request->get('login');
        $password = $request->get('password');
        try {
            $handler(new UpdateCredentials\Command($member, $login, $password));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('billing.team.member', ['member' => $member]);
    }

    /**
     * @Route("/label", name=".label", defaults={"_format": "json"})
     * @RequiresCsrf()
     */
    public function label(string $member, SetLabel\Handler $handler, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $handler(new SetLabel\Command($member, $data['value'] ?: null));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/internalNumber", name=".internalNumber", defaults={"_format": "json"})
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function internalNumber(
        string $member,
        SetInternalNumber\Handler $handler,
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $handler(new SetInternalNumber\Command($member, $data['value'] ?: null));

        return $this->json(['status' => 'ok']);
    }
}
