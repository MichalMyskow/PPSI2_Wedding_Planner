<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Task;
use App\Entity\Wedding;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

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

        $guests = $user->getWedding();

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);

        $jsonContent = $serializer->serialize($guests, 'json', [AbstractNormalizer::ATTRIBUTES => ['guests' => ['id', 'seatNumber']]]);

        return new Response ($jsonContent);
    }
}
