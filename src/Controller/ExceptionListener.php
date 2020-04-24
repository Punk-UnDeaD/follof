<?php

declare(strict_types=1);

namespace App\Controller;

use App\ReadModel\NotFoundException;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        if ('json' === $event->getRequest()->getRequestFormat()) {
            $exception = $event->getThrowable();
            $response = new JsonResponse(['status' => 'error', 'message' => $exception->getMessage()]);
            switch (true) {
                case $exception instanceof AccessDeniedException:
                    $response->setStatusCode(403);
                    break;
                case $exception instanceof NotFoundException:
                case $exception instanceof EntityNotFoundException:
                    $response->setStatusCode(404);
                    break;
                case $exception instanceof LogicException:
                    $response->setStatusCode(422);
                    break;
                case $exception instanceof HttpException:
                    $response->setStatusCode($exception->getStatusCode());
                    $response->headers->add($exception->getHeaders());
                    break;
                default:
                    $response = new JsonResponse(['status' => 'error', 'message' => 'Application unavailable.']);
                    $response->setStatusCode(500);
            }
            $event->setResponse($response);
        }
    }
}
