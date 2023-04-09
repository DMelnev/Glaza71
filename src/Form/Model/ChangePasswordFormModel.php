<?php


namespace App\Form\Model;


use App\Validator\MatchPassword;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFormModel
{
    /**
     * @Assert\NotBlank(message="Enter new password!")
     * @Assert\Length(
     *     min="6",
     *     max="100",
     *     minMessage="Password must be more than 6 symbols!",
     *     maxMessage="Password must be less than 100 symbols!"
     * )
     */
    private string $plainPassword;

    /**
     * @Assert\NotBlank(message="Enter current password!")
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