<?php


namespace App\Form\Model;


use App\Validator\RegistrationSpam;
use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormModel
{
    /**
     * @Assert\NotBlank(message="Enter your name!")
     */
    private string $firstName;

    /**
     * @Assert\NotBlank(message="Enter E-mail!")
     * @Assert\Email(message="Wrong E-mail!")
     * @RegistrationSpam()
     * @UniqueUser()
     */
    private string $email;

    /**
     * @Assert\NotBlank(message="Enter password!")
     * @Assert\Length(
     *     min="6",
     *     max="100",
     *     minMessage="Password must be more than 6 symbols!",
     *     maxMessage="Password must be less than 100 symbols!"
     * )
     */
    private string $plainPassword;

    /**
     * @Assert\IsTrue(message="You cannot register if you don't agree to the processing of personal data!")
     */
    private string $agreeTerms;

    /**
     * @Assert\NotBlank(message="Enter your surname!")
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

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getAgreeTerms(): string
    {
        return $this->agreeTerms;
    }

    /**
     * @param string $agreeTerms
     */
    public function setAgreeTerms(string $agreeTerms): void
    {
        $this->agreeTerms = $agreeTerms;
    }


}