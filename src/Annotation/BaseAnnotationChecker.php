<?php

declare(strict_types=1);

namespace App\Annotation;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

abstract class BaseAnnotationChecker implements EventSubscriberInterface
{
    const ANNOTATION = '';

    private Reader $reader;

    public function __construct(
        Reader $reader
    ) {
        $this->reader = $reader;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerArgumentsEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $target = $event->getController();

        if (!is_array($target)) {
            return;
        }

        if ($annotation = $this->getAnnotation(...$target)) {
            $this->checkAnnotation($event, $annotation);
        }
    }

    /**
     * @param $controller
     *
     * @throws \ReflectionException
     */
    private function getAnnotation($controller, string $method): ?object
    {
        return $this->reader->getMethodAnnotation(
                (new \ReflectionObject($controller))->getMethod($method),
                static::ANNOTATION
            )
            ?? $this->reader->getClassAnnotation(new \ReflectionClass($controller), static::ANNOTATION);
    }

    abstract protected function checkAnnotation(ControllerArgumentsEvent $event, object $annotation);
}
