<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Task;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // tasks anonymous user
        for ($i=0; $i < 5; $i++)
        {
            $task = new Task();
            $task->setTitle('task anonymous '.$i);
            $task->setContent('content task');
            $task->setCreatedAt(new \DateTime('now'));
            $task->setAuthor(null);
            $manager->persist($task);
        }

        // tasks admin user
        for ($i=0; $i < 5; $i++)
        {
            $task = new Task();
            $task->setTitle('task admin '.$i);
            $task->setContent('content task');
            $task->setCreatedAt(new \DateTime('now'));
            $task->setAuthor($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
            $manager->persist($task);
        }

        // tasks user
        for ($i=0; $i < 5; $i++)
        {
            $task = new Task();
            $task->setTitle('task user '.$i);
            $task->setContent('content task');
            $task->setCreatedAt(new \DateTime('now'));
            $task->setAuthor($this->getReference(UserFixtures::USER_REFERENCE));
            $manager->persist($task);
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