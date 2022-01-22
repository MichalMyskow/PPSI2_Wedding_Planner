<?php

namespace App\DataFixtures;


use App\Entity\Wedding;
use App\Repository\RoomRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WeddingFixture extends Fixture implements OrderedFixtureInterface
{
    protected RoomRepository $repository;

    public function __construct(RoomRepository $repository) 
    {
        $this->repository = $repository;
    }

    public function load(ObjectManager $manager): void
    {
        $wedding = new Wedding();

        $wedding->setDate(DateTime::createFromFormat('d-m-Y', "03-12-2022"));
        $wedding->setBrideFirstName('Panna');
        $wedding->setBrideLastName('Marianna');
        $wedding->setGroomFirstName('Pan');
        $wedding->setGroomLastName('Marian');
        $wedding->setOwner($this->getReference('user'));
        $wedding->setRoom($this->repository->findOneBy(['name' => 'mała']));

        $manager->persist($wedding);
        $manager->flush();

        $this->addReference('wedding', $wedding);
    }

    public function getOrder(): int
    {
        return 3;
    }
}
