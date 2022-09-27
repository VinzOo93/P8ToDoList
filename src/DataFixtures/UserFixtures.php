<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    const USERNAME = ['helloUser', 'Admin'];
    const ROLE = ['ROLE_USER','ROLE_ADMIN'];
    const EMAIL = ['user@gmail.com','admin@gmail.com'];
    const PASSWORD = ['user93','admin93'];


    private UserPasswordHasherInterface $hasher;

    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setUsername(self::USERNAME[$i]);
            $user->setRoles([self::ROLE[$i]]);
            $user->setPassword($this->hasher->hashPassword($user, self::PASSWORD[$i]));
            $user->setEmail(self::EMAIL[$i]);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
