<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $room = new Room();

        $room->setSize(1);
        $room->setName('testRoom');
        $room->setAddress('testAddress');

        $manager->flush();

        $this->setReference('room', $room);
    }
}
