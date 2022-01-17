<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Wedding;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();

        if (!$wedding) {
            return $this->redirectToRoute('create_wedding');
        }

        $guests = $wedding->getGuests();
        $guestsWithAcceptation = count($this->entityManager->getRepository(Guest::class)->findAllWithAcceptation($wedding));

        $costs = $wedding->getCosts();
        $sum = 0.0;

        foreach ($costs as $cost) {
            $sum += $cost->getCost();
        }

        $tasks = $wedding->getTasks();
        $completedTasks = count($this->entityManager->getRepository(Task::class)->findAllCompleted($wedding));

        $allocatedSeats = count($this->entityManager->getRepository(Guest::class)->findAllAllocatedSeats($wedding));

        $dateOfWedding = $wedding->getDate();
        $dateDiff = strtotime($dateOfWedding->format('Y-m-d')) - time();

        $daysToWedding = round($dateDiff / (60 * 60 * 24));

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'wedding' => $wedding,
            'numberOfGuests' => count($guests),
            'guestsWithAcceptation' => $guestsWithAcceptation,
            'sum' => sprintf('%.2f', round($sum, 2)),
            'completedTasks' => $completedTasks,
            'uncompletedTasks' => count($tasks) - $completedTasks,
            'allocatedSeats' => $allocatedSeats,
            'unallocatedSeats' => count($guests) - $allocatedSeats,
            'daysToWedding' => $daysToWedding + 1,
        ]);
    }
}
