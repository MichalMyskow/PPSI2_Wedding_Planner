<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wedding;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/edit-user', name: 'edit_user')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('view_user');
        }

        return $this->render('pages/edit_user.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    #[Route('/view-user', name: 'view_user')]
    public function view(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();

        return $this->render('pages/view_user.html.twig', [
            'wedding' => $wedding,
            'user' => $user,
        ]);
    }
}
