<?php


namespace App\Form\Model;


use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserEditFormModel
{
    /**
     * @Assert\NotBlank(message="Введите Имя")
     * @Assert\Length(
     *     min="2",
     *     max="50",
     *     minMessage="Имя не может быть короче 2х символов",
     *     maxMessage="Имя не может быть длиннее 50 символов"
     * )
     */
    private string $firstName;

    /**
     * @Assert\NotBlank(message="Введите Фамилию")\
     * @Assert\Length(
     *     min="2",
     *     max="50",
     *     minMessage="Фамилия не может быть короче 2х символов",
     *     maxMessage="Фамилия не может быть длиннее 50 символов"
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
            $context->buildViolation('Вы уверены, что ещё не родились?')
                ->atPath('birthDate')
                ->addViolation();
        } elseif (null !== $this->getBirthDate() && $this->getBirthDate() > new \DateTime('-16 years')) {
            $context->buildViolation('Вам должно быть больше 16 лет')
                ->atPath('birthDate')
                ->addViolation();
        }
    }
}