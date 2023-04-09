<?php


namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserEditFormModel
{
    /**
     * @Assert\NotBlank(message="Enter your name!")
     * @Assert\Length(
     *     min="2",
     *     max="50",
     *     minMessage="Name must be more than 1 symbol!",
     *     maxMessage="Name must be less than 50 symbols!"
     * )
     */
    private string $firstName;

    /**
     * @Assert\NotBlank(message="Enter your surname!")\
     * @Assert\Length(
     *     min="2",
     *     max="50",
     *     minMessage="Surname must be more than 1 symbol!",
     *     maxMessage="Surname must be less than 50 symbols!"
     * )
     */
    private string $surname;

    private $patronymic;

    private $birthDate;

    public function getBirthDate()
    {
        return $this->birthDate;
    }


    public function setBirthDate($birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return string
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    public function setPatronymic($patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null !== $this->getBirthDate() && $this->getBirthDate() > new \DateTime()) {
            $context->buildViolation("Are you sure? Do you wasn't born yet?")
                ->atPath('birthDate')
                ->addViolation();
        } elseif (null !== $this->getBirthDate() && $this->getBirthDate() > new \DateTime('-16 years')) {
            $context->buildViolation('You must be more than 16 years!')
                ->atPath('birthDate')
                ->addViolation();
        }
    }
}