<?php


namespace App\Controller\Api;


use App\Model\User\Service\HumanStrongPasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    /**
     * @param HumanStrongPasswordGenerator $generator
     * @return JsonResponse
     * @Route(name="api.password.generate", path="/api/password/generate", format="json")
     */
    public function generatePassword(HumanStrongPasswordGenerator $generator)
    {
        return $this->json(
            [
                'status' => 'ok',
                'password' => $generator->generate(),
            ]
        );
    }
}