<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\TaskFixtures;
use AppBundle\DataFixtures\ORM\UserFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $em;
    private $encoder;

    /**
     * {@inheritDoc}
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->initDatabase();

        $this->loadFixtures([
            TaskFixtures::class,
            UserFixtures::class,
        ]);
    }

    public function initDatabase()
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

    public function testLogin()
    {
        $client = static::createClient([], []);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $crawler = $client->submit($form, ['_username' => 'user', '_password' => 'user']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !", $crawler->filter('h1')->text());
    }

    public function testLogout()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $client->followRedirects(true);

        $client->request('GET', '/logout');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAccessPageWithoutPermission()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/users/create');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Oops ! Vous n'avez pas la permission d'accedez à cette ressource", $crawler->filter('div.alert')->text());
    }
}
