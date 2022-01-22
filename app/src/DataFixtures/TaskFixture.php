<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $task = new Task();

        $task->setName('testTask');
        $task->setDescription('testDesc');
        $task->setCompleted(true);

        $wedding = $this->getReference('wedding');
        $wedding->addTask($task);
        $manager->persist($wedding);

        $task->setWedding($wedding);

        $manager->persist($task);
        $manager->flush();

        $this->addReference('task', $task);
    }

    public function getOrder(): int
    {
        return 6;
    }
}
