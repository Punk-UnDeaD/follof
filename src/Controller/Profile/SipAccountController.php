<?php


namespace App\Controller\Profile;


use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SipAccountController extends AbstractController
{
    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * @Route("/profile/sipAccounts", name="profile.sipAccounts")
     * @return Response
     */
    public function sipAccounts(): Response
    {
        $user = $this->users->get($this->getUser()->getId());

        return $this->render('app/profile/sipAccounts.html.twig', ['accounts' => $user->getSipAccounts()]);
    }
}