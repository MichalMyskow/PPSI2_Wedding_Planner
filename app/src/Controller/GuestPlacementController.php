<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Task;
use App\Entity\Guest;
use App\Entity\Wedding;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\GuestRepository;


class GuestPlacementController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage){
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/guest-placement', name: 'guest_placement', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('pages/guest-placement.html.twig', [
            'controller_name' => 'GuestPlacementController',
        ]);
    }

    #[Route('/guest-placement/plan', name: 'guest_placement_plan', methods: 'GET')]
    public function showPlan(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        $wedding = $user->getWedding();

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);

        $jsonContent = $serializer->serialize($wedding, 'json', [AbstractNormalizer::ATTRIBUTES => ['guests' => ['id', 'seatNumber']]]);

        return new Response ($jsonContent);
    }

    #[Route('/guest-placement/save', name: 'guest_placement_save', methods: 'POST')]
    public function savePlan(Request $request)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        $plan = json_decode($request->getContent(), true);
        

        // $encoder = new JsonEncoder();
        // $normalizer = new ObjectNormalizer();
        // $serializer = new Serializer([$normalizer], [$encoder]);

        
        // $wedding = $serializer->deserialize($plan, Wedding::class, 'json');
        
        // dd($wedding);

        $userWedding = $user->getWedding();
        
        $entityManager = $this->getDoctrine()->getManager();

        foreach($wedding->getGuests() as $guest){
            
            $guestRepository = $entityManager->getRepository(Guest::class)->find($guest->id);

            $guestRepository->setSeatNumber($guest->seatNumber);
            $entityManager->persist($guest);
            $entityManager->flush();

        }
        $jsonContent = $serializer->serialize($wedding, 'json', [AbstractNormalizer::ATTRIBUTES => ['guests' => ['id', 'seatNumber']]]);

        return new Response($plan);
    }
}
