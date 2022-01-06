<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rooms = [
            ['size' => 50, 'name' => 'mała', 'address' => 'Legnica'],
            ['size' => 100, 'name' => 'średnia', 'address' => 'Wrocław'],
            ['size' => 150, 'name' => 'duża', 'address' => 'Lubin'],
        ];

        foreach ($rooms as $roomInfo){
            $room = (new Room())
                ->setSize($roomInfo['size'])
                ->setName($roomInfo['name'])
                ->setAddress($roomInfo['address']);
            $manager->persist($room);
        }

        $manager->flush();
    }
}
