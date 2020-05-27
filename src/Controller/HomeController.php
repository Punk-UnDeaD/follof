<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\RequiresUserCredits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RequiresUserCredits()
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        if (in_array('ROLE_BILLING_TEAM_OWNER', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('billing.team');
        }

        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin');
        }

        return $this->render('app/home.html.twig');
    }
}
