<?php

namespace App\DataFixtures;

use App\Entity\Guest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GuestFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < 10; $i++)
        {
            $guest = new Guest();
            $guest->setEmail('test@guest' . $i . '.com');
            $guest->setFirstName('Bob' . $i);
            $guest->setLastName('Bobovsky' . $i);
            $guest->setAcceptation(($i%2) === 0);
            $guest->setSeatNumber($i);

            $wedding = $this->getReference('wedding');
            $wedding->addGuest($guest);
            $manager->persist($wedding);

            $guest->setWedding($wedding);

            $guest->setInvitationSent(($i%2) === 0);

            if (($i%2) === 0) {
                $guest->addConflictedGuest($guest);
            }

            $this->addReference('guest' . $i, $guest);
            
            $manager->persist($guest);
        }
        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
