<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerChecklistTest extends WebTestCase
{
    public function test_user_can_create_checklist(): void
    {
        $client = static::createClient();
        $repository =  static::getContainer()->get(UserRepository::class);

        $user = $repository->findOneByEmail('test@test.com');
        $client->loginUser($user);

        $data = [
            'name' => 'TEST NAME',
        ];

        $client->request('POST', '/checklist', $data);

        $this->assertResponseRedirects('/create-wedding', 302);
    }

    public function test_user_can_get_checklist(): void
    {
        $client = static::createClient();
        $repository =  static::getContainer()->get(UserRepository::class);

        $user = $repository->findOneByEmail('test@test.com');
        $client->loginUser($user);

        $client->request('GET', '/checklist');
        $this->assertResponseIsSuccessful();
    }
}