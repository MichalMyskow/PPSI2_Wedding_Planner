<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class WeddingExist extends Constraint
{
    public $message = 'Data wesela zajęta w danej sali! Wybierz inną datę lub salę!';

    public function validatedBy()
    {
        return static::class.'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}