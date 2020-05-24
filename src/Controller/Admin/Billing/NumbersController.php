<?php

declare(strict_types=1);

namespace App\Controller\Admin\Billing;

use App\Annotation\RequiresCsrf;
use App\Controller\ErrorHandler;
use App\Model\Billing\Entity\Account\Number;
use App\Model\Billing\UseCase\CreateNumber;
use App\Model\Billing\UseCase\Number\SetTeam;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route(path="/admin/numbers", name="admin.numbers")
 * @IsGranted("ROLE_MANAGE_NUMBERS")
 */
class NumbersController extends AbstractController
{
    private const PER_PAGE = 20;
    private ObjectRepository $repository;

    private ErrorHandler $errors;

    public function __construct(EntityManagerInterface $em, ErrorHandler $errors)
    {
        $this->repository = $em->getRepository(Number::class);
        $this->errors = $errors;
    }

    /**
     * @Route(path="", name="")
     */
    public function index()
    {
        /** @var Number[] $numbers */
        $numbers = $this->repository->findAll();

        return $this->render('admin/numbers/index.html.twig', ['numbers' => $numbers]);
    }

    /**
     * @Route(path="/add", name=".add")
     * @RequiresCsrf()
     */
    public function add(Request $request, CreateNumber\Handler $handler): RedirectResponse
    {
        try {
            $number = $request->get('number');
            $handler(new CreateNumber\Command($number));

            return $this->redirectToRoute('admin.numbers.number', ['number' => $number]);
        } catch (\LogicException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin.numbers');
    }

    /**
     * @Route(path="/{number}/setTeam", name=".number.setTeam", format="json")
     * @RequiresCsrf()
     */
    public function setTeam(string $number, Request $request, SetTeam\Handler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $handler(new SetTeam\Command($number, $data['value'] ?? null));

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route(path="/{number}", name=".number", requirements={"number" = "^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$"})
     */
    public function show(Number $number)
    {
        return $this->render('admin/numbers/show.html.twig', ['number' => $number]);
    }
}
