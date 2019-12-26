<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private $user;
    private $createdAt;
    private $task;
    private $author;

    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();
        $this->createdAt = new \DateTime();
        $this->author = new User();
    }

    /**
     * @test
     * Test set title
     */
    public function testSetTitle()
    {
        $this->task->setTitle('task 1');
        $this->assertEquals('task 1', $this->task->getTitle());
    }

    /**
     * @test
     * Test set content
     */
    public function testSetContent()
    {
        $this->task->setContent('content task 1');
        $this->assertEquals('content task 1', $this->task->getContent());
    }

    /**
     * @test
     * Test set createdAt
     */
    public function testSetCreatedAt()
    {
        $this->task->setCreatedAt($this->createdAt);
        $this->assertEquals($this->createdAt, $this->task->getCreatedAt());
    }

    /**
     * @test
     * Test set isDone
     */
    public function testSetIsDone()
    {
        $this->task->toggle(true);
        $this->assertEquals(true, $this->task->isDone());
    }

    /**
     * @test
     * Test set author
     */
    public function testSetAuthor()
    {
        $this->task->setAuthor($this->author);
        $this->assertEquals($this->author, $this->task->getAuthor());
    }

    /**
     * @test
     * Test author get id
     */
    public function testAuthorGetId()
    {
        $this->assertEquals(null, $this->task->getId());
    }
}
