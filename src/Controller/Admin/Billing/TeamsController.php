<?php

declare(strict_types=1);

namespace App\Controller\Admin\Billing;

use App\ReadModel\Billing\Team\Filter;
use App\ReadModel\Billing\Team\TeamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="admin.teams", path="/admin/teams")
 * @IsGranted("ROLE_ADMIN")
 */
class TeamsController extends AbstractController
{
    const PER_PAGE = 20;

    /**
     * @Route(name="", path="")
     */
    public function index(Request $request, TeamFetcher $teamFetcher)
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $teamFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'billing_id'),
            $request->query->get('direction', 'desc')
        );

        return $this->render(
            'admin/teams/index.html.twig',
            [
                'pagination' => $pagination,
                'form' => $form->createView(),
            ]
        );
    }
}
