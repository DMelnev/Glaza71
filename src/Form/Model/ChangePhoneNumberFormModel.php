<?php

namespace App\Form\Model;

use App\Validator\CheckPhoneNumber;

class ChangePhoneNumberFormModel
{

    private array $phoneNumbers;

    /**
     * @return array
     */
    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    /**
     * @param array $phoneNumbers
     */
    public function setPhoneNumbers(array $phoneNumbers): void
    {
        $this->phoneNumbers = $phoneNumbers;
    }
}