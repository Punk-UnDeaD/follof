<?php

declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Annotation\Guid;
use App\Annotation\RequiresCsrf;
use App\Controller\ErrorHandler;
use App\Model\User\UseCase\Activate;
use App\Model\User\UseCase\Block;
use App\Model\User\UseCase\Create;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route(path="/admin/users", name="admin.users")
 * @IsGranted("ROLE_MANAGE_USERS")
 */
class UsersController extends AbstractController
{
    private const PER_PAGE = 20;
    private ErrorHandler $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route(path="", name="")
     */
    public function index(Request $request, UserFetcher $userFetcher)
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $userFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
        );

        return $this->render(
            'admin/users/index.html.twig',
            [
                'pagination' => $pagination,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('admin.users');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render(
            'admin/users/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{user}/activate", name=".user.activate", requirements={"user"=Guid::PATTERN})
     * @RequiresCsrf(tokenId="admin.users.user.toggleStatus")
     */
    public function activate(string $user, Activate\Handler $handler): JsonResponse
    {
        $handler->handle(new Activate\Command($user));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/{user}/block", name=".user.block", requirements={"user"=Guid::PATTERN})
     * @RequiresCsrf(tokenId="admin.users.user.toggleStatus")
     */
    public function block(string $user, Block\Handler $handler): JsonResponse
    {
        $handler->handle(new Block\Command($user));

        return $this->json(['status' => 'ok']);
    }
}
