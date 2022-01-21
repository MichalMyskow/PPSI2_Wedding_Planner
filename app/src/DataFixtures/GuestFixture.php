<?php

namespace App\DataFixtures;

use App\Entity\Guest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GuestFixture extends Fixture
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
            $guest->setWedding($this->getReference('wedding'));
            $guest->setInvitationSent(($i%2) === 0);

            if (($i%2) === 0) {
                $guest->addConflictedGuest($this->getReference('guest' . $i));
            }

            $this->setReference('guest' . $i, $guest);
        }
        $manager->flush();
    }
}
