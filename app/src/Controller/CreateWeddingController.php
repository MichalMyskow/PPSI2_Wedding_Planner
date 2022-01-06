<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wedding;
use App\Form\WeddingFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateWeddingController extends AbstractController
{

    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {

        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/create-wedding', name: 'create_wedding')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && $user->getWedding())
        {
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
}