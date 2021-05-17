<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    /**
     * __construct
     *
     * @param  mixed $encoder
     *
     * @return void
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('demo');
        $user->setMail('massoud@yahoo.fr');
        $user->setPassword($this->encoder->encodePassword($user, 'demo'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);


        $user2 = new User();
        $user2->setUsername('admin');
        $user2->setMail('admin@yahoo.fr');
        $user2->setPassword($this->encoder->encodePassword($user2, 'admin'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user2);

        $manager->flush();
    }
}
