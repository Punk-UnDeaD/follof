<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\Guid;
use App\Annotation\RequiresCsrf;
use App\Annotation\RequiresSameTeamVoiceMenu;
use App\Controller\ErrorHandler;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\Service\VoiceMenuHelper;
use App\Model\Billing\UseCase\VoiceMenu\Activate;
use App\Model\Billing\UseCase\VoiceMenu\AddPoint;
use App\Model\Billing\UseCase\VoiceMenu\Block;
use App\Model\Billing\UseCase\VoiceMenu\Delete;
use App\Model\Billing\UseCase\VoiceMenu\Point\Delete as PointDelete;
use App\Model\Billing\UseCase\VoiceMenu\SetFile;
use App\Model\Billing\UseCase\VoiceMenu\SetInputAllowance;
use App\Model\Billing\UseCase\VoiceMenu\SetInternalNumber;
use App\Model\Billing\UseCase\VoiceMenu\SetLabel;
use App\Model\Billing\UseCase\VoiceMenu\SetNumber;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/team/voiceMenu/{voiceMenu}", name="billing.team.voiceMenu", requirements={"voiceMenu"=Guid::PATTERN})
 * @RequiresSameTeamVoiceMenu
 */
class VoiceMenuController extends AbstractController
{
    private ErrorHandler $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("",name="")
     */
    public function show(VoiceMenu $voiceMenu): Response
    {
        return $this->render('app/billing/voiceMenu/show.html.twig', ['voiceMenu' => $voiceMenu]);
    }

    /**
     * @Route("/block", name=".block", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.voiceMenu.toggleStatus")
     *
     * @throws EntityNotFoundException
     */
    public function block(string $voiceMenu, Block\Handler $handler): JsonResponse
    {
        $handler(new Block\Command($voiceMenu));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/activate", name=".activate", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.voiceMenu.toggleStatus")
     *
     * @throws EntityNotFoundException
     */
    public function activate(string $voiceMenu, Activate\Handler $handler): JsonResponse
    {
        $handler(new Activate\Command($voiceMenu));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/delete", name=".delete")
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function delete(string $voiceMenu, Delete\Handler $handler): RedirectResponse
    {
        $handler(new Delete\Command($voiceMenu));

        return $this->redirectToRoute('billing.team');
    }

    /**
     * @Route("/point/{key}/delete", name=".point.delete")
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function pointDelete(string $voiceMenu, string $key, PointDelete\Handler $handler): RedirectResponse
    {
        $handler(new PointDelete\Command($voiceMenu, $key));

        return $this->redirectToRoute('billing.team.voiceMenu', ['voiceMenu' => $voiceMenu]);
    }

    /**
     * @Route("/addPoint", name=".addPoint")
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function addPoint(string $voiceMenu, AddPoint\Handler $handler, Request $request): RedirectResponse
    {
        try {
            $handler(new AddPoint\Command($voiceMenu, $request->get('key'), $request->get('number')));
        } catch (\LogicException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('billing.team.voiceMenu', ['voiceMenu' => $voiceMenu]);
    }

    /**
     * @Route("/label", name=".label", defaults={"_format": "json"})
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function label(string $voiceMenu, SetLabel\Handler $handler, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $handler(new SetLabel\Command($voiceMenu, $data['value'] ?: null));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/internalNumber", name=".internalNumber", defaults={"_format": "json"})
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function internalNumber(
        string $voiceMenu,
        SetInternalNumber\Handler $handler,
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $handler(new SetInternalNumber\Command($voiceMenu, $data['value'] ?: null));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/number", name=".number", defaults={"_format": "json"})
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function number(
        string $voiceMenu,
        SetNumber\Handler $handler,
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $handler(new SetNumber\Command($voiceMenu, $data['value'] ?: null));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/file", name=".file", defaults={"_format": "json"})
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function uploadFile(
        VoiceMenu $voiceMenu,
        SetFile\Handler $handler,
        Request $request,
        VoiceMenuHelper $helper
    ): JsonResponse {
        $file = $request->files->get('file');
        $fname = $helper->proposeFileName(
            $voiceMenu,
            $file->getClientOriginalExtension()
        );

        $file->move(dirname($fname), basename($fname));
        try {
            $handler(new SetFile\Command($voiceMenu->getId()->getValue(), $fname));
        } catch (\Exception $e) {
            unlink($fname);
            throw $e;
        }

        return $this->json(['status' => 'ok', 'reload' => true]);
    }

    /**
     * @Route("/inputAllowance", name=".inputAllowance", defaults={"_format": "json"})
     * @RequiresCsrf()
     *
     * @throws EntityNotFoundException
     */
    public function inputAllowance(string $voiceMenu, SetInputAllowance\Handler $handler, Request $request)
    {
        $allowance = json_decode($request->getContent());
        $handler(new SetInputAllowance\Command($voiceMenu, $allowance));

        return $this->json(['status' => 'ok']);
    }
}
