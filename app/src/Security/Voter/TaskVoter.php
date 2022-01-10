<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
// use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Entity\Task;

class TaskVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser() ?: null;
        if (!$user) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
                break;
            case self::EDIT:
                return $this->canEdit($subject, $user);
                break;
        }

        return false;
    }

    private function canView(Task $task, User $user): bool
    {

        return $this->canEdit($task, $user);
        
    }

    private function canEdit(Task $task, User $user): bool
    {
        return $user->getWedding() === $task->getWedding();
    }
}
