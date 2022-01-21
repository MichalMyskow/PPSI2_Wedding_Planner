<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    protected UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail("test@test.com");
        $user->setUsername("test");
        $user->setPassword($this->hasher->hashPassword($user, "test123"));

        $manager->persist($user);

        $manager->flush();

        $this->setReference('user', $user);
    }
}
