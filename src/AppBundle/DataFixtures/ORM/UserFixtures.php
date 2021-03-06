<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFixtures extends AbstractFixture implements ContainerAwareInterface
{
    private $container;

    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const USER_REFERENCE = 'user';

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $encoder = $this->container->get('security.password_encoder');

        // admin user
        $admin_user = new User();
        $admin_user->setUsername('admin');

        $password = $encoder->encodePassword($admin_user, 'admin');
        $admin_user->setPassword($password);

        $admin_user->setEmail('admin@domain.fr');
        $admin_user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($admin_user);
        $this->addReference(self::ADMIN_USER_REFERENCE, $admin_user);

        // basic user
        $user = new User();
        $user->setUsername('user');

        $password = $encoder->encodePassword($user, 'user');
        $user->setPassword($password);
        
        $user->setEmail('user@domain.fr');
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE, $user);

        $manager->flush();
    }
}
