<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\User;
use App\Entity\Wedding;
use App\Form\GuestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager,
                                TokenStorageInterface $tokenStorage,
                                ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/admin/remove_user/{id}', name: 'admin_remove_user')]
    public function removeUser(int $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find($id);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_view');
    }


    #[Route('/admin', name: 'admin_view')]
    public function view(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;


        $users = $this->entityManager->getRepository(User::class)->getAllUsers();

        return $this->render('admin/admin.html.twig', [
            'users' => $users,
        ]);
    }
}
