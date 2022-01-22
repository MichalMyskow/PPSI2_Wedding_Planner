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
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ControllerCostTest extends WebTestCase
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

    public function test_cost_adding_properly()
    {
        $repository = $this->client->getContainer()->get(UserRepository::class);
        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $costBeforeChanges = $this->client->request('GET', '/view-costs')->filter('div.budget-summary__value > span')->first()->text();

        $form = $this->client->request('POST', '/add-cost')->filter('form')->form();
        
        $this->assertFormValue('form', 'cost_form[name]', '');
        $this->assertFormValue('form', 'cost_form[description]', '');
        $this->assertFormValue('form', 'cost_form[cost]', '');

        $form['cost_form[name]']->setValue('TestedName');
        $form['cost_form[description]']->setValue('TestedDescription');
        $form['cost_form[cost]']->setValue('10');

        $crawler = $this->client->submit($form);
        
        $actualDescription = $crawler->filter('div.budget-list__item-details > *')->extract(['_text']);
        $actualCost = $crawler->filter('div.budget-summary__value > span')->first()->text();

        $this->assertContains('TestedDescription', $actualDescription);
        $this->assertEquals($costBeforeChanges + 10, $actualCost);
    }

    public function test_cost_removed_properly()
    {
        $repository =  $this->client->getContainer()->get(UserRepository::class);
        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $cost = $this->client->request('GET', '/view-costs')->filter('div.budget-summary__value > span')->first()->text();
        $this->assertEquals(11.11, $cost);

        $costs = $user->getWedding()->getCosts();
        $this->client->request('DELETE', '/remove-cost/' . $costs[0]->getId());
        
        $cost = $this->client->request('GET', '/view-costs')->filter('div.budget-summary__value > span')->first()->text();
        $this->assertEquals(0, $cost);
    }

    public function test_cost_edited_properly()
    {
        $repository =  $this->client->getContainer()->get(UserRepository::class);
        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $costBeforeChanges = $this->client->request('GET', '/view-costs')->filter('div.budget-summary__value > span')->first()->text();
        $this->assertEquals(11.11, $costBeforeChanges);
       
        $costs = $user->getWedding()->getCosts();
        $form = $this->client->request('PUT', '/edit-cost/' . $costs[0]->getId())->filter('form')->form();
        
        $this->assertFormValue('form', 'cost_form[name]', 'TestCost');
        $this->assertFormValue('form', 'cost_form[description]', 'Lorem Ipsum');
        $this->assertFormValue('form', 'cost_form[cost]', '11,11');

        $form['cost_form[name]']->setValue('ChangedName');
        $form['cost_form[description]']->setValue('ChangedDescription');
        $form['cost_form[cost]']->setValue('10');

        $crawler = $this->client->submit($form);
        
        $actualDescription = $crawler->filter('div.budget-list__item-details > *')->extract(['_text']);
        $actualCost = $crawler->filter('div.budget-summary__value > span')->first()->text();

        $this->assertContains('ChangedDescription', $actualDescription);
        $this->assertEquals(10, $actualCost);
    }
}