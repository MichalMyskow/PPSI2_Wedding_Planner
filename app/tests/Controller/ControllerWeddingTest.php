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
use DateTime;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ControllerWeddingTest extends WebTestCase
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

    public function test_wedding_edited_properly()
    {
        $repository = $this->client->getContainer()->get(UserRepository::class);
        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $weddings = $user->getWedding()->getGuests();
        $wedding = $weddings[0];
        $form = $this->client->request('PUT', '/edit-wedding')->filter('form')->form();

        $this->assertFormValue('form', 'wedding_form[brideFirstName]', 'Panna');
        $this->assertFormValue('form', 'wedding_form[brideLastName]', 'Marianna');
        $this->assertFormValue('form', 'wedding_form[groomFirstName]', 'Pan');
        $this->assertFormValue('form', 'wedding_form[groomLastName]', 'Marian');

        $form['wedding_form[brideFirstName]']->setValue('testowniczka');
        $form['wedding_form[brideLastName]']->setValue('testovsky');
        $form['wedding_form[groomFirstName]']->setValue('testownik');
        $form['wedding_form[groomLastName]']->setValue('testovsky');

        $crawler = $this->client->submit($form);

        $actualDate = $crawler->filter('div.details__item:nth-of-type(1)')->text();
        $actualbride = $crawler->filter('div.details__item:nth-of-type(2)')->text();
        $actualgroom = $crawler->filter('div.details__item:nth-of-type(3)')->text();

        $this->assertEquals('Data wesela: 03.12.2022', $actualDate);
        $this->assertEquals('Panna młoda: testowniczka testovsky', $actualbride);
        $this->assertEquals('Pan młody: testownik testovsky', $actualgroom);
    }
}