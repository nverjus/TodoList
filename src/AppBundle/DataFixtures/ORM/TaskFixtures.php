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
        $admin = $manager->getRepository(User::class)->findOneByUsername('admin');

        foreach ($users as $user) {
            $task1 = new Task();
            $task1->setTitle('First task of '.$user->getUsername());
            $task1->setContent('First task of '.$user->getUsername());
            $task1->setUser($user);
            $manager->persist($task1);
        }
        $anonTask1 = new Task();
        $anonTask1->setTitle('First task of anonymous user');
        $anonTask1->setContent('First task of anonymous user');
        $anonTask1->setUser($admin);
        $manager->persist($anonTask1);

        foreach ($users as $user) {
            $task2 = new Task();
            $task2->setTitle('Second task of '.$user->getUsername());
            $task2->setContent('Second task of '.$user->getUsername());
            $task2->setUser($user);
            $manager->persist($task2);
        }
        $anonTask2 = new Task();
        $anonTask2->setTitle('Second task of anonymous user');
        $anonTask2->setContent('Second task of anonymous user');
        $anonTask2->setUser($admin);
        $manager->persist($anonTask2);


        $manager->flush();
    }


    public function getDependencies()
    {
        return array(
                UserFixtures::class,
            );
    }
}
