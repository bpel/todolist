<?php

namespace tests\AppBundle\Command;

use AppBundle\Command\TaskAuditCommand;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Service\TaskManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Tester\CommandTester;
use Faker;

class TaskAuditCommandTest extends WebTestCase
{
    private $em;
    private $faker;
    private $commandTester;

    /**
     * {@inheritDoc}
     */
    protected function setUp():void
    {
        parent::setUp();

        $client = static::createClient();
        $kernel = static::createKernel();

        $container = $client->getContainer();
        $doctrine = $container->get('doctrine');
        $this->em = $doctrine->getManager();

        $application = new Application($kernel);
        $application->add(new TaskAuditCommand());
        $command = $application->get('task:audit');
        $this->commandTester = new CommandTester($command);

        $this->faker = Faker\Factory::create();

        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->em);

        $schemaTool->dropDatabase();

        $schemaTool->createSchema($metadatas);
    }

    private function createAnonymousTasks():void
    {
        for ($i = 0; $i < 3; $i++) {
            $task = new Task();
            $task->setAuthor(null);
            $task->setTitle('task'.$i);
            $task->setContent('content');
            $task->setCreatedAt($this->faker->dateTime);
            $this->em->persist($task);
            $this->em->flush();
        }
    }

    /**
     * @test
     */
    public function testAuditCommand()
    {
        $this->createAnonymousTasks();

        // anonymous user does not exist
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
        $this->assertEquals($user, null);

        // update tasks
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('3 task(s) has been successfully updated', $output);

        // author exist
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
        $this->assertNotEquals($user, null);

        // no tasks to update
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('No task without an author', $output);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown():void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }

}
