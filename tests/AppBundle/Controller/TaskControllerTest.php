<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Command\TaskAuditCommand;
use AppBundle\DataFixtures\ORM\TaskFixtures;
use AppBundle\DataFixtures\ORM\UserFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $em;
    private $encoder;
    private $commandTester;

    /**
     * {@inheritDoc}
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->initDatabase();
        $this->initCommandTaskAudit();

        $this->loadFixtures([
            TaskFixtures::class,
            UserFixtures::class,
        ]);
    }

    private function initDatabase()
    {
        $client = static::createClient();

        $container = $client->getContainer();

        $doctrine = $container->get('doctrine');

        $this->em = $doctrine->getManager();

        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->em);

        $schemaTool->dropDatabase();

        $schemaTool->createSchema($metadatas);

        $this->encoder = $container->get('security.password_encoder');
    }

    private function initCommandTaskAudit()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $application->add(new TaskAuditCommand());
        $command = $application->get('task:audit');
        $this->commandTester = new CommandTester($command);
    }

    public function testCreateTask()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $crawler = $client->submit($form, ['task[title]' => 'random task', 'task[content]' => 'content random task']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Superbe ! La tâche a été bien été ajoutée.', $crawler->filter('div.alert')->text());
    }

    public function testEditTaskWithPermission()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/tasks/11/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $crawler = $client->submit($form, ['task[title]' => 'task edited', 'task[content]' => 'content edited']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Superbe ! La tâche a bien été modifiée.', $crawler->filter('div.alert')->text());
    }

    public function testEditTaskWithoutPermission()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/tasks/8/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Oops ! Impossible de modifier cette tâche!', $crawler->filter('div.alert')->text());
    }

    public function testToggleTask()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $client->followRedirects(true);

        $client->request('GET', '/tasks/11/toggle');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskWithPermission()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $client->followRedirects(true);

        $crawler = $client->request('GET', '/tasks/8/delete');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Superbe ! La tâche a été supprimée!', $crawler->filter('div.alert')->text());
    }

    public function testDeleteTaskWithoutPermission()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $client->followRedirects(true);

        $crawler = $client->request('GET', '/tasks/8/delete');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Oops ! Impossible de supprimer cette tâche!', $crawler->filter('div.alert')->text());
    }

    public function testDeleteAnonymousTask()
    {
        $this->commandTester->execute([]);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/tasks/1/delete');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Superbe ! La tâche a été supprimée!', $crawler->filter('div.alert')->text());
    }
}
