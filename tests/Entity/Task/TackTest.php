<?php

namespace App\Tests\Entity\Task;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class TackTest extends TestCase
{


    public function testcratedAt()
    {
        $date = new DateTime();
        $task = $this->getEntityTask();
        $task->setCreatedAt($date);
        $this->assertEquals($date, $task->getCreatedAt());
    }

    public function testTitle()
    {
        $task = $this->getEntityTask();
        $task->setTitle('Faire les courses');
        $this->assertEquals('Faire les courses', $task->getTitle());
    }

    public function testContent()
    {
        $task = $this->getEntityTask();
        $task->setContent('acheter du lait');
        $this->assertEquals('acheter du lait', $task->getContent());
    }

    public function testIsDone()
    {
        $task = $this->getEntityTask();
        $task->toggle(!$task->isDone());
        $this->assertEquals(true, $task->isDone());
    }

    public function testAuthor()
    {
        $task = $this->getEntityTask();
        $user = $this->getEntityUser();
        $user->setUsername('Pepin');
        $task->setAuthor($user);
        $this->assertEquals('Pepin', $task->getAuthor()->getUsername());
    }

    private function getEntityTask(): Task
    {
        return new Task();
    }

    private function getEntityUser(): User
    {
        return new User();
    }
}