<?php

namespace App\DataFixtures;


use App\Entity\Wedding;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WeddingFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $wedding = new Wedding();

        $wedding->setDate(DateTime::createFromFormat('d-m-Y', "03-12-2022"));
        $wedding->setBrideFirstName('Panna');
        $wedding->setBrideLastName('Marianna');
        $wedding->setGroomFirstName('Pan');
        $wedding->setGroomLastName('Marian');
        $wedding->setOwner($this->getReference('user'));
        $wedding->addCost($this->getReference('cost'));
        $wedding->addTask($this->getReference('task'));
        $wedding->addGuest($this->getReference('guest'));
        $wedding->setRoom($this->getReference('room'));

        $manager->flush();

        $this->setReference('wedding', $wedding);
    }
}
