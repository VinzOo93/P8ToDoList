<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    const TITLE = ['faire de la pizza', 'manger une glâce', 'faire les course'];
    const CONTENT = ['préparer la pizza', 'préparer la glace', 'prendre une orange'];

    public function load(ObjectManager $manager)
    {
        $userAdmin = $manager->getRepository(User::class)->findOneBy(['username' => "helloUser"]);
        $user = $manager->getRepository(User::class)->findOneBy(['username' => "Admin"]);
        $date = new \DateTime('now');

        for ($i = 0; $i < 3; $i++) {
            $task = new Task();
            if ($i === 0) {
                $task->setAuthor($userAdmin);
            } else if ($i === 1){
                $task->setAuthor($user);
            }
            $task->setCreatedAt($date);
            $task->setTitle(self::TITLE[$i]);
            $task->setContent(self::CONTENT[$i]);
            $manager->persist($task);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}