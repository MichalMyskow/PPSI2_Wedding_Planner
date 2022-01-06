<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wedding;
use App\Form\CreateWeddingFormType;
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
        $form = $this->createForm(CreateWeddingFormType::class, $wedding);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wedding->setOwner($user);
            $entityManager = $this->getDoctrine()->getManager();
            if ($entityManager->getRepository(Wedding::class)->findOneByDateAndRoom($wedding))
            {
                throw new \LogicException('Data zajęta');
            }
            $entityManager->persist($wedding);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }


        return $this->render('feature/create_wedding.html.twig', [
            'createWeddingForm' => $form->createView(),
        ]);



    }
}