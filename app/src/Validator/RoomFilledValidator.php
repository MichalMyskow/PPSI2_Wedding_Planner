<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RoomFilledValidator extends ConstraintValidator
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function validate($guest, Constraint $constraint)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        if (
            $user
            && $user->getWedding()
            && count($user->getWedding()->getGuests()) >= $user->getWedding()->getRoom()->getSize()
        ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
