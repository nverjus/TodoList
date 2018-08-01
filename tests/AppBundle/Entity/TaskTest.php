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
        $date = self::createMock(\DateTime::class);
        $title = 'test';
        $content = 'test';
        $user = self::createMock(User::class);

        $task->setCreatedAt($date);
        $task->setTitle($title);
        $task->setContent($content);
        $task->setIsDone(true);
        $task->setUser($user);

        self::assertNull($task->getId());
        self::assertSame($date, $task->getCreatedAt());
        self::assertSame($title, $task->getTitle());

        self::assertSame($content, $task->getContent());
        self::assertTrue($task->isDone());

        $task->toggle(!$task->isDone());
        self::assertFalse($task->isDone());

        self::assertSame($user, $task->getUser());
    }
}
