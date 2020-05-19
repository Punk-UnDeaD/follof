<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AsteriskController extends AbstractController
{

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route(name="api.asterisk.beforeCall", path="/api/asterisk/beforeCall", format="json")
     */
    public function beforeCall(Request $request): JsonResponse
    {
        $this->log($request);

        return $this->json(
            [
                'status' => 'ok',
            ]
        );
    }

    private function log(Request $request)
    {
        $this->logger->info(
            'Matched route "{route}".',
            [
                'request_uri' => $request->getUri(),
                'method' => $request->getMethod(),
                'headers' => $request->headers->all(),
                'request' => $request->request->all(),
                'content' => $request->getContent(),
            ]
        );
    }

    /**
     * @Route(name="api.asterisk.afterCall", path="/api/asterisk/afterCall", format="json")
     */
    public function afterCall(Request $request): JsonResponse
    {
        $this->log($request);

        return $this->json(
            [
                'status' => 'ok',
            ]
        );
    }
}
