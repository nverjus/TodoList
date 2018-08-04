<?php
namespace AppBundle\ORM\DataFixtures;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $users = [
          [
            'username' => 'admin',
            'password' => '$2y$13$EGelSPuZJ2UUlhbIFEdTN.dzM/UXloYiNOkBEKBm0/S15pX5rwhRi',
            'email' => 'admin@mail.com',
            'role' => 'ROLE_ADMIN',
          ],
          [
            'username' => 'user1',
            'password' => '$2y$13$gI.ipXTXGgnX9d/wa.caEeEBkTCCcUhf65dasJYtnZKrnpHZwU7h6',
            'email' => 'user1@mail.com',
            'role' => 'ROLE_USER',
          ],
          [
            'username' => 'user2',
            'password' => '$2y$13$kCKQQchqeQER1lxdip/aTO3L3rczjY1s4u7TVn2hilAbw3Zo1FxMG',
            'email' => 'user2@mail.com',
            'role' => 'ROLE_USER',
          ],
          [
            'username' => 'anonymous',
            'password' => '$2y$13$HmzvrgMDQuYdvY3loBG/wODvVFwMlBf9GGIWWmPpl9DlRYLzIzJtW',
            'email' => 'no mail',
            'role' => 'ROLE_USER',
          ],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword($data['password']);
            $user->setEmail($data['email']);
            $user->setRole($data['role']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
