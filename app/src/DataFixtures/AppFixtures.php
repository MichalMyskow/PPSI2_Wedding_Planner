<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $rooms = [
            ['size' => 50, 'name' => 'mała', 'address' => 'Legnica', 'street' => 'Weselna', 'houseNumber' => '23B', 'postcode' => '59-220'],
            ['size' => 100, 'name' => 'średnia', 'address' => 'Wrocław', 'street' => 'Spokojna', 'houseNumber' => '11A', 'postcode' => '50-102'],
            ['size' => 150, 'name' => 'duża', 'address' => 'Lubin', 'street' => 'Słoneczna', 'houseNumber' => '5', 'postcode' => '59-300'],
        ];

        foreach ($rooms as $roomInfo) {
            $room = (new Room())
                ->setSize($roomInfo['size'])
                ->setName($roomInfo['name'])
                ->setAddress($roomInfo['address'])
                ->setStreet($roomInfo['street'])
                ->setHouseNumber($roomInfo['houseNumber'])
                ->setPostcode($roomInfo['postcode']);
            $manager->persist($room);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
