<?php

namespace App\Tests\Entity\User;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUsername()
    {
        $task = $this->getEntityUser();
        $task->setUsername('Pepin');
        $this->assertEquals('Pepin', $task->getUsername());
    }

    public function testRole()
    {
        $user =  $this->getEntityUser();
        $user->setRoles(['ROLE_USER']);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testPassWord()
    {
        $user =  $this->getEntityUser();
        $user->setPassword('pa$$word');
        $this->assertEquals('pa$$word', $user->getPassword());
    }

    public function testEmail()
    {
        $user =  $this->getEntityUser();
        $user->setEmail('v.12344@live.fr');
        $this->assertEquals('v.12344@live.fr', $user->getEmail());
    }

    private function getEntityUser(): User
    {
        return new User();
    }
}