<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    private $manager;
    private $encoder;

    public function __construct(EntityManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    public function checkAnonymousUserExist():bool
    {
        $result = true;
        $userAnonymous = $this->manager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);

        if($userAnonymous == null)
        {
            $result = $this->createAnonymousUser();
        }

        return $result;
    }

    public function createAnonymousUser():bool
    {
        $anonymous_user = new User();
        $anonymous_user->setUsername('anonymous');
        $anonymous_user->setEmail('anonymous@domain.fr');
        $anonymous_user->setPassword($this->encoder->encodePassword($anonymous_user,'anonymous'));
        $anonymous_user->setRoles(array('ROLE_USER'));

        $this->manager->persist($anonymous_user);
        $this->manager->flush();
        return true;
    }

    public function getAnonymousUser()
    {
        return $this->manager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
    }

}