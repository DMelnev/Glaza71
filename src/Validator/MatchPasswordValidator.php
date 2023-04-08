<?php

namespace App\Validator;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MatchPasswordValidator extends ConstraintValidator
{
    private Security $security;
    private UserPasswordHasherInterface $passwordHash;

    /**
     * MatchPasswordValidator constructor.
     */
    public function __construct(Security $security, UserPasswordHasherInterface $passwordHash)
    {
        $this->security = $security;
        $this->passwordHash = $passwordHash;
    }

    public function validate($value, Constraint $constraint)
    {

        if (null === $value || '' === $value) {
            return;
        }
        $user = $this->security->getUser();

        if (!$this->passwordHash->isPasswordValid($user, $value)){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
