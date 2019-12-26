<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\TaskFixtures;
use AppBundle\DataFixtures\ORM\UserFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class UserControllerTest extends WebTestCase
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

    public function testCreateUser()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();

        $crawler = $client->submit($form, [
            'user[username]' => 'usertest',
            'user[password][first]' => 'superpassword',
            'user[password][second]' => 'superpassword',
            'user[email]' => 'usertest@domain.fr',
            'user[roles][0]' => 'ROLE_USER',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Superbe ! L'utilisateur a bien été ajouté.", $crawler->filter('div.alert')->text());
    }

    public function testEditUser()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/users/2/edit');

        $form = $crawler->selectButton('Modifier')->form();

        $crawler = $client->submit($form, [
            'user[username]' => 'newusername',
            'user[password][first]' => 'passwordsimple',
            'user[password][second]' => 'passwordsimple',
            'user[email]' => 'usertest@domain.fr',
            'user[roles][0]' => 'ROLE_USER',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Superbe ! L'utilisateur a bien été modifié", $crawler->filter('div.alert')->text());
    }
}
