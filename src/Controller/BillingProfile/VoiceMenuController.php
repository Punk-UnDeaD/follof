<?php

declare(strict_types=1);

namespace App\Controller\BillingProfile;

use App\Annotation\Guid;
use App\Annotation\RequiresCsrf;
use App\Annotation\RequiresSameTeamVoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\Activate;
use App\Model\Billing\UseCase\VoiceMenu\Block;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/team/voiceMenu/{voiceMenu}", name="billing.team.voiceMenu", requirements={"voiceMenu"=Guid::PATTERN})
 * @RequiresSameTeamVoiceMenu
 */
class VoiceMenuController extends AbstractController
{
    /**
     * @Route("/block", name=".block", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.voiceMenu.toggleStatus")
     */
    public function block(string $voiceMenu, Block\Handler $handler): JsonResponse
    {
        $handler(new Block\Command($voiceMenu));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/activate", name=".activate", defaults={"_format": "json"})
     * @RequiresCsrf(tokenId="billing.team.voiceMenu.toggleStatus")
     */
    public function activate(string $voiceMenu, Activate\Handler $handler): JsonResponse
    {
        $handler(new Activate\Command($voiceMenu));

        return $this->json(['status' => 'ok']);
    }
}
