<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\CostFixture;
use App\DataFixtures\GuestFixture;
use App\DataFixtures\TaskFixture;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\WeddingFixture;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ControllerChecklistTest extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->followRedirects();

        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($manager);

        $executor = new ORMExecutor($manager, $purger);
        $executor->execute([
            new AppFixtures(),
            new UserFixtures($this->client->getContainer()->get(UserPasswordHasherInterface::class)),
            new WeddingFixture($this->client->getContainer()->get(RoomRepository::class)),
            new CostFixture(),
            new GuestFixture(),
            new TaskFixture(),
        ]);
    }

    public function test_user_can_get_checklist(): void
    {
        $repository =  static::getContainer()->get(UserRepository::class);

        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/checklist');
        $this->assertResponseIsSuccessful();
    }
}