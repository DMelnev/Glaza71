<?php


namespace App\Service;


class PhoneNumberFilter
{
    private const ALLOWED_COUNTRIES = [
        "ru" => ["length" => 11, "codes" => ["|^8|", "|^7|",]],
    ];

    public function filter(array $numbers): bool
    {
        foreach ($numbers as $number) {
            $marker = true;
            $phone = preg_replace('/[^0-9]/', '', $number);
            foreach (self::ALLOWED_COUNTRIES as $item) {
                if (strlen($phone) == $item["length"]) {
                    foreach ($item["codes"] as $code) {
                        if (preg_match($code, $phone)) {
                            $marker = false;
                            break;
                        }
                    }
                    if ($marker) break;
                }
            }
            if ($marker) return false;
        }
        return true;
    }
}