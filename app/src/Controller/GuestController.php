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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GuestController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/add-guest', name: 'add_guest')]
    public function add(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        $guest = new Guest();
        $form = $this->createForm(GuestFormType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guest->setWedding($user->getWedding());
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($guest);
            $entityManager->flush();

            return $this->redirectToRoute('view_guests');
        }

        return $this->render('pages/add_guest.html.twig', [
            'guestForm' => $form->createView(),
        ]);
    }

    #[Route('/edit-guest/{id}', name: 'edit_guest')]
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

        $form = $this->createForm(GuestFormType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guest);
            $entityManager->flush();

            return $this->redirectToRoute('view_guests');
        }

        return $this->render('pages/edit_guest.html.twig', [
            'guestForm' => $form->createView(),
        ]);
    }

    #[Route('/remove-guest/{id}', name: 'remove_guest')]
    public function remove(int $id, Request $request): Response
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

        $this->entityManager->remove($guest);
        $this->entityManager->flush();

        return $this->redirectToRoute('view_guests');
    }

    #[Route('/view-guests', name: 'view_guests')]
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

        return $this->render('pages/guest-list.html.twig', [
            'guests' => $guests,
            'maxGuests' => $maxGuests,
            'numberOfGuests' => count($guests),
        ]);
    }

    #[Route('/send-invitations', name: 'send_invitations')]
    public function sendInvitations(MailerInterface $mailer): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();
        $guests = $wedding->getGuests();

        foreach ($guests as $guest) {
            $this->sendMail($guest, $wedding, $mailer);
        }

        return $this->redirectToRoute('view_guests');
    }

    #[Route('/send-invitation/{id}', name: 'send_invitation')]
    public function sendInvitation(int $id, MailerInterface $mailer): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Guest|null $guest */
        $guest = $this->entityManager->getRepository(Guest::class)->find($id);

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();

        $this->sendMail($guest, $wedding, $mailer);

        return $this->redirectToRoute('view_guests');
    }

    public function sendMail(Guest $guest, Wedding $wedding, MailerInterface $mailer): void
    {
        if (!($guest->getAcceptation()) && !($guest->getInvitationSent())) {
            $email = (new Email())
                ->from(new Address('weddingplannerppsi2@gmail.com', 'WeddingPlanner'))
                ->to($guest->getEmail())
                ->subject('Zaproszenie na wesele')
                ->text(sprintf('%s %s i %s %s mają zaszczyt zaprosić Sz.P. %s %s na swój ślub',
                    $wedding->getBrideFirstName(), $wedding->getBrideLastName(), $wedding->getGroomFirstName(), $wedding->getGroomLastName(), $guest->getFirstName(), $guest->getLastName()));

            $mailer->send($email);

            $guest->setInvitationSent(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guest);
            $entityManager->flush();
        }
    }
}
