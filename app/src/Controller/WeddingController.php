<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wedding;
use App\Form\WeddingFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class WeddingController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/create-wedding', name: 'create_wedding')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && $user->getWedding()) {
            return $this->redirectToRoute('home');
        }

        $wedding = new Wedding();
        $form = $this->createForm(WeddingFormType::class, $wedding);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wedding->setOwner($user);
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($wedding);
            $entityManager->flush();

            return $this->redirectToRoute('view_wedding');
        }

        return $this->render('pages/create_wedding.html.twig', [
            'weddingForm' => $form->createView(),
        ]);
    }

    #[Route('/edit-wedding', name: 'edit_wedding')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();

        if ($wedding->getDate() < (new \DateTime())) {
            return $this->redirectToRoute('view_wedding');
        }

        $form = $this->createForm(WeddingFormType::class, $wedding);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wedding);
            $entityManager->flush();

            return $this->redirectToRoute('view_wedding');
        }

        return $this->render('pages/edit_wedding.html.twig', [
            'weddingForm' => $form->createView(),
        ]);
    }

    #[Route('/view-wedding', name: 'view_wedding')]
    public function view(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();
        $guests = $wedding->getGuests();
        $maxGuests = (count($guests) >= $wedding->getRoom()->getSize());

        return $this->render('pages/view_wedding.html.twig', [
            'wedding' => $wedding,
            'room' => $wedding->getRoom(),
            'editable' => !($wedding->getDate() < (new \DateTime())),
            'guests' => $guests,
            'maxGuests' => $maxGuests,
        ]);
    }
}
