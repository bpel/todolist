<?php

namespace AppBundle\Service;

use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class TaskManager
{
    private $manager;
    private $container;
    private $userManager;

    public function __construct(EntityManager $manager, Container $container)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->userManager = $this->container->get('service.user_manager');
    }

    public function getTaskNoAuthor()
    {
        return $this->manager->getRepository(Task::class)->findBy(['author' => null]);
    }

    public function linkTaskAnonymousUser(): bool
    {
        $anonymous_user = $this->userManager->getAnonymousUser();

        foreach ($this->getTaskNoAuthor() as $task) {
            $task->setAuthor($anonymous_user);
            $this->manager->persist($task);
            $this->manager->flush();
        }
        return true;
    }
}
