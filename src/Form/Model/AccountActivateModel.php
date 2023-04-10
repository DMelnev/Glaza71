<?php


namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class AccountActivateModel
{
    /**
     * @Assert\NotBlank(message="Enter activation code!")
     */
    private string $activationCode;

    /**
     * @return string
     */
    public function getActivationCode(): string
    {
        return $this->activationCode;
    }

    /**
     * @param string $activationCode
     */
    public function setActivationCode(string $activationCode): void
    {
        $this->activationCode = $activationCode;
    }


}