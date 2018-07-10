<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetterAndSetter()
    {
        $task = new Task();

        $this->assertNull($task->getId());

        $date = new \DateTime();
        $task->setCreatedAt($date);
        $this->assertSame($date, $task->getCreatedAt());

        $title = 'test';
        $task->setTitle($title);
        $this->assertSame($title, $task->getTitle());

        $content = 'test';
        $task->setContent($content);
        $this->assertSame($content, $task->getContent());

        $task->setIsDone(true);
        $this->assertTrue($task->isDone());
        $this->assertTrue($task->getIsDone());

        $task->toggle(!$task->isDone());
        $this->assertFalse($task->isDone());

        $user = new User();
        $task->setUser($user);
        $this->assertSame($user, $task->getUser());
    }
}
