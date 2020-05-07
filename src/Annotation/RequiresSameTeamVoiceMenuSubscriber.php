<?php

declare(strict_types=1);

namespace App\Annotation;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Security\MemberIdentity;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class RequiresSameTeamVoiceMenuSubscriber extends BaseAnnotationChecker
{
    private Security $security;

    public const ANNOTATION = RequiresSameTeamVoiceMenu::class;
    private ObjectRepository $repository;

    public function __construct(
        Reader $reader,
        Security $security,
        EntityManagerInterface $em
    ) {
        parent::__construct($reader);
        $this->security = $security;
        $this->repository = $em->getRepository(VoiceMenu::class);
    }

    /**
     * @param RequiresSameTeamVoiceMenu|object $annotation
     */
    protected function checkAnnotation(ControllerArgumentsEvent $event, object $annotation)
    {
        $id = $event->getRequest()->attributes->get('_route_params')[$annotation->key];
        /** @var MemberIdentity $user */
        $user = $this->security->getUser();
        /** @var VoiceMenu $voiceMenu */
        $voiceMenu = $this->repository->find($id);
        if (!$voiceMenu || $user->getTeamId() !== $voiceMenu->getTeam()->getId()->getValue()) {
            throw new AccessDeniedException('Not your team member.');
        }
    }
}
