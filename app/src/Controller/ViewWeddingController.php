<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewWeddingController extends AbstractController
{

    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {

        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/view-wedding', name: 'view_wedding')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding())
        {
            return $this->redirectToRoute('create_wedding');
        }

        return $this->render('pages/view_wedding.html.twig', [
            'wedding' => $user->getWedding(),
            'room' => $user->getWedding()->getRoom(),
            'editable' => !($user->getWedding()->getDate() < (new \DateTime()))
        ]);

    }
}