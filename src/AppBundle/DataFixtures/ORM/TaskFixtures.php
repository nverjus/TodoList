<?php
namespace AppBundle\ORM\DataFixtures;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TasksFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $task1 = new Task();
            $task1->setTitle('First task of '.$user->getUsername());
            $task1->setContent('First task of '.$user->getUsername());
            $task1->setUser($user);
            $manager->persist($task1);
        }

        foreach ($users as $user) {
            $task2 = new Task();
            $task2->setTitle('Second task of '.$user->getUsername());
            $task2->setContent('Second task of '.$user->getUsername());
            $task2->setUser($user);
            $manager->persist($task2);
        }



        $manager->flush();
    }


    public function getDependencies()
    {
        return array(
                UserFixtures::class,
            );
    }
}
