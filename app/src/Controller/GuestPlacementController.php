<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\User;
use App\Entity\Wedding;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GuestPlacementController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager,
                                TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/guest-placement', name: 'guest_placement', methods: 'GET')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();
        $guests = $wedding->getGuests();
   
        $roomSize = $wedding->getRoom()->getSize();
        $roomView = "rooms/_room-$roomSize.html.twig";

        return $this->render('pages/guest-placement.html.twig', [
            'roomView' => $roomView,
            'guests' => $guests,
        ]);
    }

    #[Route('/view-guest-placement/{uuid}', name: 'guest_placement_view', methods: 'GET')]
    public function showView(Request $request): Response
    {
        $uuid = $request->get('uuid');

        /** @var Wedding $wedding */
        $guest = $this->entityManager->getRepository(Guest::class)->findOneBy(['uuid' => $uuid]);
        $wedding = $guest->getWedding();
        $room = $wedding->getRoom();
        $roomSize = $room->getSize();
        $roomView = "rooms/_room-$roomSize.html.twig";

        if (!$wedding) {
            return $this->redirectToRoute('home');
        }

        return $this->render('pages/guest-placement-view.html.twig', [
            'guest' => $guest,
            'roomView' => $roomView,
        ]);
    }

    #[Route('/guest-placement/plan', name: 'guest_placement_plan', methods: 'GET')]
    public function showPlan(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if (!$user) {
            $session = new Session();

            /** @var Wedding $wedding */
            $wedding = $this->entityManager->getRepository(Wedding::class)->findOneBy(['uuid' => $session->get('uuid')]);
        } else {
            $wedding = $user->getWedding();
        }

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);

        $jsonContent = $serializer->serialize($wedding, 'json', [AbstractNormalizer::ATTRIBUTES => ['guests' => ['id', 'seatNumber']]]);

        return new Response($jsonContent);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/guest-placement/save', name: 'guest_placement_save', methods: 'POST')]
    public function savePlan(Request $request)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if ($user && !$user->getWedding()) {
            return $this->redirectToRoute('create_wedding');
        }

        $plan = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        /** @var Wedding $wedding */
        $wedding = $user->getWedding();

        $guestRepo = $this->entityManager->getRepository(Guest::class);

        foreach ($plan as $key => $val) {
            $guest = $guestRepo->find($val['id']);
            if ($guest && $guest->getWedding() === $wedding) {
                $guest->setSeatNumber($val['seatNumber']);
                $this->entityManager->persist($guest);
            }
        }
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}
