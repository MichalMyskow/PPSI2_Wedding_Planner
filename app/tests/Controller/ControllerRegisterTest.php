<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerRegisterTest extends WebTestCase
{

    public function test_a_user_who_wants_to_register_with_valid_data(): void
    {
        $data = [
            "email" => "test@test.com",
            "username" => "test",
            "password" => "test123",
            "password_confirmation" => "test123",
        ];

        $client = static::createClient();
        $client->request('POST', '/register', $data);
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

}