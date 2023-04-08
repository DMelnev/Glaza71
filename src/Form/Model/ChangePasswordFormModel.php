<?php


namespace App\Form\Model;


use App\Validator\MatchPassword;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFormModel
{
    /**
     * @Assert\NotBlank(message="Введите Новый Пароль")
     * @Assert\Length(
     *     min="6",
     *     max="100",
     *     minMessage="Пароль должен быть не менее 6 символов",
     *     maxMessage="Пароль должен быть не более 100 символов"
     * )
     */
    private string $plainPassword;

    /**
     * @Assert\NotBlank(message="Введите старый Пароль")
     * @MatchPassword()
     */
    private string $oldPassword;

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
    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     */
    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }
}