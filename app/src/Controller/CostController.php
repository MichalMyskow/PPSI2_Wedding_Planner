<?php

namespace App\Controller;

use App\Entity\Cost;
use App\Entity\User;
use App\Entity\Wedding;
use App\Form\CostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CostController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/add-cost', name: 'add_cost')]
    public function add(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        $cost = new Cost();
        $form = $this->createForm(CostFormType::class, $cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cost->setWedding($user->getWedding());
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($cost);
            $entityManager->flush();

            return $this->redirectToRoute('view_costs');
        }

        return $this->render('pages/add_cost.html.twig', [
            'costForm' => $form->createView(),
        ]);
    }

    #[Route('/edit-cost/{id}', name: 'edit_cost')]
    public function edit(int $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Cost|null $cost */
        $cost = $this->entityManager->getRepository(Cost::class)->find($id);
        if (!$cost || $cost->getWedding() != $user->getWedding()) {
            return $this->redirectToRoute('view_cost');
        }

        $form = $this->createForm(CostFormType::class, $cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cost);
            $entityManager->flush();

            return $this->redirectToRoute('view_costs');
        }

        return $this->render('pages/edit_cost.html.twig', [
            'costForm' => $form->createView(),
        ]);
    }

    #[Route('/remove-cost/{id}', name: 'remove_cost')]
    public function remove(int $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Cost|null $cost */
        $cost = $this->entityManager->getRepository(Cost::class)->find($id);
        if (!$cost || $cost->getWedding() != $user->getWedding()) {
            return $this->redirectToRoute('view_costs');
        }

        $this->entityManager->remove($cost);
        $this->entityManager->flush();

        return $this->redirectToRoute('view_costs');
    }

    #[Route('/view-costs', name: 'view_costs')]
    public function view(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();
        $costs = $wedding->getCosts();
        $sum = 0.0;

        foreach ($costs as $cost) {
            $sum += $cost->getCost();
        }

        return $this->render('pages/cost.html.twig', [
            'costs' => $costs,
            'sum' => sprintf('%.2f', round($sum, 2)),
        ]);
    }
}
