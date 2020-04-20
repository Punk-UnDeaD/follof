<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\Billing\Entity\Account\Member;
use App\ReadModel\Billing\MemberFetcher;
use App\ReadModel\User\UserFetcher;
use App\Security\MemberIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    private $users;
    /**
     * @var MemberFetcher
     */
    private MemberFetcher $members;

    public function __construct(UserFetcher $users, MemberFetcher $members)
    {
        $this->users = $users;
        $this->members = $members;
    }

    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function show(): Response
    {
        if($this->getUser() instanceof MemberIdentity){
            $member = $this->members->get($this->getUser()->getId());
            return $this->render('app/member_profile/show.html.twig', [
                'member'=> $member,
                'team' => $member->getTeam()
            ]);
        }

        $user = $this->users->get($this->getUser()->getId());

        return $this->render('app/profile/show.html.twig', compact('user'));
    }
}
