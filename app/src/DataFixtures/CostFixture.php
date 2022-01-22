<?php

namespace App\DataFixtures;

use App\Entity\Cost;
use App\Entity\Wedding;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CostFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cost = new Cost();

        $cost->setName("TestCost");
        $cost->setDescription('Lorem Ipsum');
        $cost->setCost(11.11);

        $wedding = $this->getReference('wedding');
        $wedding->addCost($cost);
        $manager->persist($wedding);

        $cost->setWedding($wedding);

        $manager->persist($cost);
        $manager->flush();

        $this->addReference('cost', $cost);
    }

    public function getOrder(): int
    {
        return 4;
    }
}
