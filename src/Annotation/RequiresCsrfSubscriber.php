<?php

declare(strict_types=1);

namespace App\Annotation;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\ReadModel\User\UserFetcher;
use App\Security\MemberIdentity;
use App\Security\UserIdentity;
use Doctrine\Common\Annotations\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RequiresCsrfSubscriber implements EventSubscriberInterface
{
    private $reader;
    /**
     * @var CsrfTokenManagerInterface
     */
    private CsrfTokenManagerInterface $csrfTokenManager;

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelController',
        ];
    }

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        Reader $reader
    ) {
        $this->reader = $reader;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function onKernelController($event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $target = $event->getController();

        if (!is_array($target)) {
            return;
        }

        [$controller, $method] = $target;
        $request = $event->getRequest();
        if ($ann = $this->hasAnnotation($controller, $method, RequiresCsrf::class)) {
            $token = $request->headers->get('csrf-token') ?? $request->get('_csrf_token');
            $tokenId = $ann->tokenId ?? $request->get('_route');
            if ($this->csrfTokenManager->getToken($tokenId)->getValue() !== $token) {
                throw new AccessDeniedHttpException('Invalid CSRF token.');
            }
        }
    }


    private function hasAnnotation($controller, string $method, string $class)
    {
        if ($annotation = $this->reader->getMethodAnnotation(
            (new \ReflectionObject($controller))->getMethod($method),
            $class
        )) {
            return $annotation;
        }
        if ($annotation = $this->reader->getClassAnnotation(new \ReflectionClass($controller), $class)) {
            return $annotation;
        }

        return false;
    }
}
