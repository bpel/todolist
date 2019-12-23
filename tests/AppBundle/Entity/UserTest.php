<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $user;
    private $createdAt;
    private $task;

    public function setUp():void
    {
        $this->user = new User();
        $this->task = new Task();
        $this->createdAt = new \DateTime;
    }

    /**
     * @test
     * Test set username
     */
    public function testSetUsername()
    {
        $this->user->setUsername('usertest');
        $this->assertEquals('usertest', $this->user->getUsername());
    }

    /**
     * @test
     * Test set password
     */
    public function testSetPassword()
    {
        $this->user->setPassword('usertestpassword');
        $this->assertEquals('usertestpassword', $this->user->getPassword());
    }

    /**
     * @test
     * Test set email
     */
    public function testSetEmail()
    {
        $this->user->setEmail('usertest@domain.fr');
        $this->assertEquals('usertest@domain.fr', $this->user->getEmail());
    }

    /**
     * @test
     * Test get salt
     */
    public function testGetSalt()
    {
        $this->assertEquals(null, $this->user->getSalt());
    }

    /**
     * @test
     * Test set roles
     */
    public function testSetAuthor()
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertEquals(array('0' => 'ROLE_USER'), $this->user->getRoles());
    }

    /**
     * @test
     * Test erase credentials
     */
    public function testEraseCredentials()
    {
        $user = $this->user;
        $this->user->eraseCredentials();
        $this->assertEquals($user, $this->user);
    }

    /**
     * @test
     * Test user get id
     */
    public function testUserGetId()
    {
        $this->assertEquals(null, $this->user->getId());
    }
}