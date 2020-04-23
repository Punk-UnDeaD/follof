<?php

declare(strict_types=1);

namespace App\Annotation;

use App\ReadModel\Billing\MemberFetcher;
use App\Security\MemberIdentity;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class RequiresSameTeamMemberSubscriber extends BaseAnnotationChecker
{
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var MemberFetcher
     */
    private MemberFetcher $members;

    public const ANNOTATION = RequiresSameTeamMember::class;

    public function __construct(
        Reader $reader,
        Security $security,
        MemberFetcher $members
    ) {
        parent::__construct($reader);
        $this->security = $security;
        $this->members = $members;
    }


    /**
     * @param ControllerArgumentsEvent $event
     * @param RequiresSameTeamMember|object $annotation
     */
    protected function checkAnnotation(ControllerArgumentsEvent $event, object $annotation)
    {
        $member_id = $event->getRequest()->attributes->get('_route_params')[$annotation->key];

        /** @var MemberIdentity $user */
        $user = $this->security->getUser();
        if ($user->getTeamId() !== $this->members->getAuthView($member_id)->team_id) {
            throw new AccessDeniedException('Not your team member.');
        }
    }
}
