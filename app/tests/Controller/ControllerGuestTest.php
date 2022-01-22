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

class ControllerGuestTest extends WebTestCase
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

    public function test_guests_adding_properly()
    {
        $repository = $this->client->getContainer()->get(UserRepository::class);
        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $guest = $this->client->request('GET', '/view-guests')->filter('div.guest-list__item-details > *')->first()->text();

        $form = $this->client->request('POST', '/add-guest')->filter('form')->form();

        $this->assertFormValue('form', 'guest_form[email]', '');
        $this->assertFormValue('form', 'guest_form[firstName]', '');
        $this->assertFormValue('form', 'guest_form[lastName]', '');

        $form['guest_form[email]']->setValue('test1@test.com');
        $form['guest_form[firstName]']->setValue('test');
        $form['guest_form[lastName]']->setValue('testovsky');

        $crawler = $this->client->submit($form);

        $actualDescription = $crawler->filter('div.guest-list__item-details > *')->extract(['_text']);
        $actualGuest = $crawler->filter('div.guest-list__item-details > *')->first()->text();

        $this->assertContains(' (test1@test.com) ', $actualDescription);
        $this->assertEquals($guest, $actualGuest);
    }

    public function test_guest_removed_properly()
    {
        $repository =  $this->client->getContainer()->get(UserRepository::class);
        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $guest = $this->client->request('GET', '/view-guests')->filter('div.guest-list__item-details > *')->extract(['_text']);
        $this->assertContains(' (test@guest0.com) ', $guest);

        $guests = $user->getWedding()->getGuests();
        $this->client->request('DELETE', '/remove-guest/' . $guests[0]->getId());

        $guest = $this->client->request('GET', '/view-guests')->filter('div.guest-list__item-details > *')->extract(['_text']);
        $this->assertNotContains(' (test@guest0.com) ', $guest);
    }

    public function test_guest_edited_properly()
    {
        $repository = $this->client->getContainer()->get(UserRepository::class);
        $user = $repository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);

        $guests = $user->getWedding()->getGuests();
        $guest = $guests[0];
        $form = $this->client->request('PUT', '/edit-guest/' . $guest->getId())->filter('form')->form();

        $this->assertFormValue('form', 'guest_form[email]', 'test@guest0.com');
        $this->assertFormValue('form', 'guest_form[firstName]', 'Bob0');
        $this->assertFormValue('form', 'guest_form[lastName]', 'Bobovsky0');

        $form['guest_form[email]']->setValue('test1@test.com');
        $form['guest_form[firstName]']->setValue('test');
        $form['guest_form[lastName]']->setValue('testovsky');

        $crawler = $this->client->submit($form);

        $actualDescription = $crawler->filter('div.guest-list__item-details > *')->extract(['_text']);

        $this->assertContains(' (test1@test.com) ', $actualDescription);
        $this->assertNotContains('test@guest0.com', $actualDescription);
   }
}