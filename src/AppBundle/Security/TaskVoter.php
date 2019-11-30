<?php

namespace AppBundle\Security;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TaskVoter extends Voter implements VoterInterface
{
    const EDIT = 'edit';
    const REMOVE = 'remove';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT, self::REMOVE])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $task = $subject;

        if($task->getAuthor() != null && 'anonymous' == $task->getAuthor()->getUsername())
        {
            return $this->decisionManager->decide($token, ['ROLE_ADMIN']);
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($task, $user);
            case self::REMOVE:
                return $this->canRemove($task, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Task $task, User $user)
    {
        return $user === $task->getAuthor();
    }

    private function canRemove(Task $task, User $user)
    {
        return $user === $task->getAuthor();
    }
}