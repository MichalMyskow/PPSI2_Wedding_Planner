<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\CostCalculator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerCostTest extends WebTestCase
{
    public function test_cost_adding_properly()
    {
        $client = static::createClient();
        $repository =  static::getContainer()->get(UserRepository::class);

        $user = $repository->findOneByEmail('test@test.com');
        $client->loginUser($user);

        $data = [
            'name' => 'testCost',
            'description' => 'testDesc',
            'cost' => 10
        ];

        $client->request('POST', '/add-cost', $data);
        $crawler = $client->request('GET','/view-costs');
        $value = $crawler->filter('div.budget-summary__value > span')->first();
        $this->assertEquals(10, dd($value));
    }
}