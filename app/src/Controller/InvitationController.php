<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\User;
use App\Entity\Wedding;
use App\Form\AcceptationFormType;
use App\Service\GeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\SnappyResponse;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use ZipArchive;

class InvitationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;
    private GeneratorService $generatorService;

    public function __construct(EntityManagerInterface $entityManager,
                                TokenStorageInterface $tokenStorage,
                                GeneratorService $generatorService)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->generatorService = $generatorService;
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

        $counter = 0;
        foreach ($guests as $guest) {
            $image = $this->generatorService->generateImage($wedding, $guest);
            $this->sendMail($guest, $wedding, $mailer, $image);
            ++$counter;
        }

        return $this->redirectToRoute('view_guests', ['sent_invitations_counter' => $counter]);
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

        $image = $this->generatorService->generateImage($wedding, $guest);

        $this->sendMail($guest, $wedding, $mailer, $image);

        return $this->redirectToRoute('view_guests', ['sent_invitations_counter' => 1]);
    }

    #[Route('/download-invitation/{id}', name: 'download_invitation')]
    public function downloadInvitation(int $id): Response
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

        $image = $this->generatorService->generateImage($wedding, $guest);

        return new SnappyResponse(
            $image,
            sprintf('Zaproszenie-%s.png', $guest->getUuid()->toRfc4122()),
            'image/png'
        );
    }

    #[Route('/download-all-invitation', name: 'download_all_invitation')]
    public function downloadAllInvitation(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        };

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();
        $guests = $wedding->getGuests();
        $files = array();
        foreach ($guests as $guest) {
            $image = $this->generatorService->generateImage($wedding, $guest);
            array_push($files, $image);
        }

        $zipName = $wedding->GetId().'.zip';
        $zip = new ZipArchive;
        $zip->open($zipName, ZipArchive::CREATE);
        //foreach ($files as $file) {
       //     $zip->addFile($file);
       // }
        $zip->close();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-disposition','attachment; filename="' . $zipName . '"');
       // $response->headers->set('Content-Length: ' . filesize($zipName));
        //$response->sendHeaders();
       // $response->headers->set(readfile($zipName));

        return $response;
    }
//        header("Content-type: application/zip");
//        header('Content-Disposition: attachment; filename='.$guest->getUuid()->toRfc4122());
//        header("Pragma: no-cache");
//        header("Expires: 0");
//        readfile("$archive_file_name");
//
//        return new SnappyResponse(
//            $files[0],
//            sprintf('Zaproszenie-%s.png', $guest->getUuid()->toRfc4122()),
//            'image/png'
//
//        );

    public function sendMail(Guest $guest, Wedding $wedding, MailerInterface $mailer, $image): void
    {
        if (!($guest->getAcceptation()) && !($guest->getInvitationSent())) {
            $email = (new TemplatedEmail())
                ->from(new Address('weddingplannerppsi2@gmail.com', 'WeddingPlanner'))
                ->to($guest->getEmail())
                ->subject('Zaproszenie na wesele')
                ->htmlTemplate('emails/acceptation.html.twig')
                ->context([
                    'wedding' => $wedding,
                    'guest' => $guest,
                    'uuid' => $guest->getUuid()->toRfc4122(),
                ])
                ->attach($image, sprintf('Zaproszenie-%s.png', $guest->getUuid()->toRfc4122()))
            ;

            $mailer->send($email);

            $guest->setInvitationSent(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guest);
            $entityManager->flush();
        }
    }

    #[Route('/accept-invitation/{uuid}', name: 'accept_invitation')]
    public function acceptInvitation(Request $request): Response
    {
        $uuid = $request->get('uuid');

        /** @var Guest $guest */
        $guest = $this->entityManager->getRepository(Guest::class)->findOneBy(['uuid' => $uuid]);

        if (!$guest) {
            return $this->redirectToRoute('home');
        }

        if ($guest->getAcceptation()) {
            return $this->render('pages/acceptation_confirmed.html.twig', []);
        }

        $wedding = $guest->getWedding();

        $form = $this->createForm(AcceptationFormType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guest);
            $entityManager->flush();

            return $this->render('pages/acceptation_confirmed.html.twig', [
                'uuid' => $guest->getUuid()->toRfc4122(),
            ]);
        }

        return $this->render('pages/acceptation.html.twig', [
            'acceptationForm' => $form->createView(),
            'wedding' => $guest->getWedding(),
        ]);

//      TODO: podziękować za akceptację? wysłać maila (link do zobaczenia miejsc)?
    }
}
