<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $task = new Task();

        $task->setName('testTask');
        $task->setDescription('testDesc');
        $task->setCompleted(true);
        $task->setWedding($this->getReference('wedding'));

        $manager->flush();

        $this->setReference('task', $task);
    }
}
