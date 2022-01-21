<?php

namespace App\DataFixtures;

use App\Entity\Cost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CostFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cost = new Cost();

        $cost->setName("TestCost");
        $cost->setDescription('Lorem Ipsum');
        $cost->setCost(11.11);
        $cost->setWedding($this->getReference('wedding'));

        $manager->flush();

        $this->setReference('cost', $cost);
    }
}
