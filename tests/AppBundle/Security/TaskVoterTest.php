<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Security\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class TaskVoterTest extends TestCase
{
    private $decisionManager;

    public function setUp():void
    {
        $this->decisionManager = $this->CreateMock(AccessDecisionManagerInterface::class);
    }

    /**
     * @test
     * Test invalid attributes
     */
    public function testVoteInvalidAttributes()
    {
        $task = new Task();

        $this->decisionManager->method('decide')->willReturn(true);
        $voter = new TaskVoter($this->decisionManager);
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($task);

        $result = $voter->vote($tokenMock, $task, array('REGISTER'));

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     * Test invalid subject
     */
    public function testVoteInvalidSubject()
    {
        $task = new Task();

        $this->decisionManager->method('decide')->willReturn(true);
        $voter = new TaskVoter($this->decisionManager);
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($task);

        $result = $voter->vote($tokenMock, $task, array('remove'));

        $this->assertEquals('-1', $result);
    }

    /**
     * @test
     * Test invalid token
     */
    public function testVoteInvalidToken()
    {
        $user = new User();

        $this->decisionManager->method('decide')->willReturn(true);
        $voter = new TaskVoter($this->decisionManager);
        $tokenMock = $this->CreateMock(TokenInterface::class);
        $tokenMock->method('getUser')->willReturn($user);

        $result = $voter->vote($tokenMock, $user, array('remove'));

        $this->assertEquals(false, $result);
    }
}