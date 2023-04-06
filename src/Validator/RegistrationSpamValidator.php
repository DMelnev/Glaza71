<?php

namespace App\Validator;

use App\Service\RegistrationSpamFilter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegistrationSpamValidator extends ConstraintValidator
{
    private RegistrationSpamFilter $spamFilter;

    /**
     * RegistrationSpamValidator constructor.
     */
    public function __construct(RegistrationSpamFilter $spamFilter)
    {
        $this->spamFilter = $spamFilter;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var RegistrationSpam $constraint */

        if (null === $value || '' === $value) {
            return;
        }
        if ($this->spamFilter->filter($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
