<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\User;
use App\Form\GuestConflictFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GuestConflictController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager,
                                TokenStorageInterface $tokenStorage,
                                ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/edit-guest-conflicts/{id}', name: 'edit_guest_conflicts')]
    public function edit(int $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Guest|null $guest */
        $guest = $this->entityManager->getRepository(Guest::class)->find($id);
        if (!$guest || $guest->getWedding() != $user->getWedding()) {
            return $this->redirectToRoute('view_guests');
        }

        if ($user->getWedding()->getDate() < (new \DateTime())) {
            return $this->redirectToRoute('view_guests');
        }

        $form = $this->createForm(GuestConflictFormType::class, $guest, [
            'actualGuest' => $guest,
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guest);
            $entityManager->flush();

            return $this->redirectToRoute('view_guests');
        }

        return $this->render('pages/edit_conflicted_guests.html.twig', [
            'guestConflictForm' => $form->createView(),
        ]);
    }
}
