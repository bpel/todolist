<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserManager
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function anonymousUserExist()
    {
        $userAnonymous = $this->manager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
        return $userAnonymous !== null;
    }

    public function createAnonymousUser()
    {
        $anonymous_user = new User();
        $anonymous_user->setUsername('anonymous');
        $anonymous_user->setEmail('anonymous@domain.fr');
        $anonymous_user->setPassword('anonymous');
        $anonymous_user->setRoles(array('ROLE_USER'));

        $this->manager->persist($anonymous_user);
        $this->manager->flush();
    }

    public function getAnonymousUser()
    {
        return $this->manager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
    }

}