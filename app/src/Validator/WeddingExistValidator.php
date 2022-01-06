<?php
namespace App\Validator;

use App\Entity\Wedding;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class WeddingExistValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($wedding, Constraint $constraint)
    {
        if ($this->entityManager->getRepository(Wedding::class)->findOneByDateAndRoom($wedding))
        {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}