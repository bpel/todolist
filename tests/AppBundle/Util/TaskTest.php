<?php

namespace Tests\AppBundle\Util;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
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
     * Test title task
     */
    public function testTaskTitle()
    {
        $this->task->setTitle('task 1');
        $this->assertEquals($this->task->getTitle(), 'task 1');
    }

    /**
     * @test
     * Test content task
     */
    public function testTaskContent()
    {
        $this->task->setContent('content task 1');
        $this->assertEquals($this->task->getContent(), 'content task 1');
    }
}