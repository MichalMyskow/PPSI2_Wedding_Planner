<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerLoginTest extends WebTestCase
{

    public function test_visiting_while_logged_in()
    {

        $client = static::createClient();
        $this->createUser($client);

        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $this->assertResponseIsSuccessful();

    }

    public function test_visiting_while_not_logged_in()
    {

        $client = static::createClient();

        $this->createUser($client);

        $this->assertResponseIsSuccessful();

    }

    private function createUser($client)
    {

        $data = [
            "email" => "test@test.com",
            "username" => "test",
            "password" => "test123",
            "password_confirmation" => "test123",
        ];

        $client->request('POST', '/register', $data);

    }
}