<?php


namespace App\Service;


class DigitWordsEnd
{
    public function format($digit, $value0, $value1, $value2): string
    {
        $digit = (int)$digit;
        if ($digit == 1 || ($digit >= 21 && $digit % 10 == 1)) return $value1;
        if (($digit >= 2 && $digit <= 4) || ($digit >= 22 && $digit % 10 >= 2 && $digit % 10 <= 4)) return $value2;
        return $value0;
    }
}