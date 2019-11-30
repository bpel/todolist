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

    public function checkAnonymousUserExist():bool
    {
        $userAnonymous = $this->manager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);

        if($userAnonymous == null)
        {
            return $this->createAnonymousUser();
        }

        return true;
    }

    public function createAnonymousUser():bool
    {
        $anonymous_user = new User();
        $anonymous_user->setUsername('anonymous');
        $anonymous_user->setEmail('anonymous@domain.fr');
        $anonymous_user->setPassword('anonymous');
        $anonymous_user->setRoles(array('ROLE_USER'));

        try {
            $this->manager->persist($anonymous_user);
            $this->manager->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function getAnonymousUser()
    {
        return $this->manager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
    }

}