<?php

namespace App\Controller\Test;

use App\Model\Billing\Entity\Account\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
        $manager = $this->getDoctrine()->getManager();
        $repo = $manager->getRepository(Team::class);
        $repo->findAll()[0]->getMembers();
        return $this->render('test/index.html.twig', [
            'teams' => $repo->findAll()
        ]);
    }
}
