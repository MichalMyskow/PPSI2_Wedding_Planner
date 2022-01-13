<?php

namespace App\Validator;

use App\Entity\User;
use App\Entity\Wedding;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RoomTooSmallValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function validate($wedding, Constraint $constraint)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser() ?: null;

        /** @var Wedding $wedding */
        if ($wedding->getRoom()->getSize() < count($wedding->getGuests())) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
