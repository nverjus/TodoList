<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetterAndSetter()
    {
        $username = 'name';
        $password = 'pass';
        $email = 'mail';
        $role = 'ROLE';
        $task1 = static::createMock(Task::class);
        $task2 = static::createMock(Task::class);

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setRole($role);

        static::assertNull($user->getId());
        static::assertNull($user->getSalt());

        static::assertSame($username, $user->getUsername());

        static::assertSame($password, $user->getPassword());

        static::assertSame($email, $user->getEmail());

        static::assertSame($role, $user->getRole());
        static::assertSame(array($role), $user->getRoles());

        static::assertSame(0, $user->getTasks()->count());
        $user->addTask($task1);
        $user->addTask($task2);
        static::assertSame(2, $user->getTasks()->count());
        $user->removeTask($task1);
        static::assertSame(1, $user->getTasks()->count());
    }
}
