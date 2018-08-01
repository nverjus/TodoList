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
        $task1 = self::createMock(Task::class);
        $task2 = self::createMock(Task::class);

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setRole($role);

        self::assertNull($user->getId());
        self::assertNull($user->getSalt());

        self::assertSame($username, $user->getUsername());

        self::assertSame($password, $user->getPassword());

        self::assertSame($email, $user->getEmail());

        self::assertSame($role, $user->getRole());
        self::assertSame(array($role), $user->getRoles());

        self::assertSame(0, $user->getTasks()->count());
        $user->addTask($task1);
        $user->addTask($task2);
        self::assertSame(2, $user->getTasks()->count());
        $user->removeTask($task1);
        self::assertSame(1, $user->getTasks()->count());
    }
}
