<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\User\Service\HumanStrongPasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    /**
     * @Route(name="api.password.generate", path="/api/password/generate", format="json")
     */
    public function generatePassword(HumanStrongPasswordGenerator $generator): JsonResponse
    {
        return $this->json(
            [
                'status' => 'ok',
                'password' => $generator->generate(),
            ]
        );
    }
}
