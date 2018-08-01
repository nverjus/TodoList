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
        $date = static::createMock(\DateTime::class);
        $title = 'test';
        $content = 'test';
        $user = static::createMock(User::class);

        $task->setCreatedAt($date);
        $task->setTitle($title);
        $task->setContent($content);
        $task->setIsDone(true);
        $task->setUser($user);

        static::assertNull($task->getId());
        static::assertSame($date, $task->getCreatedAt());
        static::assertSame($title, $task->getTitle());

        static::assertSame($content, $task->getContent());
        static::assertTrue($task->isDone());

        $task->toggle(!$task->isDone());
        static::assertFalse($task->isDone());

        static::assertSame($user, $task->getUser());
    }
}
