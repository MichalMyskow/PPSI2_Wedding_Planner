<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Task;
// use App\Repository\TaskRepository;
use App\Entity\Wedding;
use App\Form\TaskFormType;
use App\Service\TaskService;


class ChecklistController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/checklist', name: 'checklist', methods:"GET")]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if (!$user)
        {
            return $this->redirectToRoute('app_login');
        }elseif (!$user->getWedding())
        {
            return $this->redirectToRoute('create_wedding');
        }
        
        $tasks = $user->getWedding()->getTasks();
        $form = $this->createForm(TaskFormType::class);

        return $this->render('pages/checklist.html.twig', [
            'tasks' => $tasks,
            'taskForm' => $form->createView(),
        ]);
    }

    #[Route('/checklist', name: 'checklist_create', methods:"POST")]
    public function create(Request $request)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if (!$user)
        {
            return $this->redirectToRoute('app_login');
        }elseif (!$user->getWedding())
        {
            return $this->redirectToRoute('create_wedding');
        }
        
        $task = new Task();
        $tasks = $user->getWedding()->getTasks();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setWedding($user->getWedding());
            $taskService  = new TaskService($this->getDoctrine()->getManager());
            $taskService->saveTask($task);
        }
        return $this->redirectToRoute('checklist');

    }

}
