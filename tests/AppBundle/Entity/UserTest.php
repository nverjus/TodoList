<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetterAndSetter()
    {
        $user = new User();
        $this->assertNull($user->getId());
        $this->assertNull($user->getSalt());

        $username = 'name';
        $user->setUsername($username);
        $this->assertSame($username, $user->getUsername());

        $password = 'pass';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());

        $email = 'mail';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());

        $role = 'ROLE';
        $user->setRole($role);
        $this->assertSame($role, $user->getRole());
        $this->assertSame(array($role), $user->getRoles());

        $this->assertSame(0, $user->getTasks()->count());

        $task1 = new Task();
        $task2 = new Task();
        $user->addTask($task1);
        $user->addTask($task2);

        $this->assertSame(2, $user->getTasks()->count());

        $user->removeTask($task1);
        $this->assertSame(1, $user->getTasks()->count());
    }
}
