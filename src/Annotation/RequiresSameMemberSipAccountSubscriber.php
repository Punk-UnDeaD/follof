<?php

declare(strict_types=1);

namespace App\Annotation;

use App\Model\Billing\Entity\Account\Repository\SipAccountRepository;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RequiresSameMemberSipAccountSubscriber extends BaseAnnotationChecker
{
    private SipAccountRepository $sipAccounts;

    public const ANNOTATION = RequiresSameMemberSipAccount::class;

    public function __construct(
        Reader $reader,
        SipAccountRepository $sipAccounts
    ) {
        parent::__construct($reader);
        $this->sipAccounts = $sipAccounts;
    }

    /**
     * @param RequiresSameMemberSipAccount|object $annotation
     */
    protected function checkAnnotation(ControllerArgumentsEvent $event, object $annotation)
    {
        [
            $annotation->memberKey => $member_id,
            $annotation->sipAccountKey => $sipAccount_id,
        ] = $event->getRequest()->attributes->get('_route_params');
        $sipAccount = $this->sipAccounts->get($sipAccount_id);
        if ($sipAccount->getMember()->getId()->getValue() !== $member_id) {
            throw new AccessDeniedException('Alien member sip account.');
        }
    }
}
