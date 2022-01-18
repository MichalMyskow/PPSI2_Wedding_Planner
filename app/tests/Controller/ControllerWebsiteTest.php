<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerWebsiteTest extends WebTestCase
{
    public function test_if_homepage_works()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_http_not_found_when_the_page_does_not_exists(): void
    {
        $client = static::createClient();
        $client -> request('GET', "/non-existing-page-123");

        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }

    public function test_redirect_to_login(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        self::assertResponseRedirects('/login');
    }
}