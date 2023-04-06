<?php


namespace App\Service;


class RegistrationSpamFilter
{
    private const ALLOWED_DOMAINS = ['ru', 'com', 'org'];

    public function filter(string $email): bool
    {
        $result = explode('.', strtolower($email));
        return !in_array($result[count($result) - 1], self::ALLOWED_DOMAINS);
    }
}