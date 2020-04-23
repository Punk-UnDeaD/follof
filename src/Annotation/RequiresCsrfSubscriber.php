<?php

declare(strict_types=1);

namespace App\Annotation;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RequiresCsrfSubscriber extends BaseAnnotationChecker
{
    public const ANNOTATION = RequiresCsrf::class;

    /**
     * @var CsrfTokenManagerInterface
     */
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        Reader $reader
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        parent::__construct($reader);
    }

    /**
     * @param ControllerArgumentsEvent $event
     * @param object|RequiresCsrf $annotation
     */
    protected function checkAnnotation(ControllerArgumentsEvent $event, object $annotation)
    {
        $request = $event->getRequest();
        $token = $request->headers->get('csrf-token') ?? $request->get('_csrf_token');
        $tokenId = $annotation->tokenId ?? $request->get('_route');
        if ($this->csrfTokenManager->getToken($tokenId)->getValue() !== $token) {
            throw new AccessDeniedException('Invalid CSRF token.');
        }
    }
}
