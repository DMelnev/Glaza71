<?php


namespace App\Form\Model;


use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditFormModel
{
    /**
     * @Assert\NotBlank(message="Введите Имя")
     */
    private string $firstName;

    /**
     * @Assert\NotBlank(message="Введите Фамилию")
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
    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    /**
     * @param string $patronymic
     */
    public function setPatronymic(string $patronymic): void
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

    public function fillModel(User $user){
        $this->setFirstName($user->getFirstName());
        $this->setPatronymic($user->getPatronymic());
        $this->setSurname($user->getSurname());
        $this->setBirthDate($user->getBirthDate());
    }
}