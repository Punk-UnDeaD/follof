<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="api.autocomplete", path="/api/autocomplete", format="json")
 */
class AutocompleteController extends AbstractController
{

    /**
     * @Route(name=".teamBillingId", path="/teamBillingId/{search}", defaults={"search"=""})
     * @IsGranted("ROLE_MANAGE_NUMBERS")
     */
    public function teamBillingId(string $search, Request $request, Connection $connection): JsonResponse
    {

        $res = $connection->createQueryBuilder()
            ->from('billing_team', 'team')
            ->select('team.billing_id as billing_id')
            ->where('billing_id LIKE :value')
            ->setParameter(':value', "%{$search}%")
            ->setMaxResults(10)
            ->execute()
            ->fetchAll(FetchMode::COLUMN);

        return $this->json(
            [
                'status' => 'ok',
                'values' => $res,
            ]
        );
    }
}
