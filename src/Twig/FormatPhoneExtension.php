<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatPhoneExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_ru_phone', [$this, 'formatPhone']),
        ];
    }

    public function formatPhone(string $value): string
    {
        if (mb_strlen($value) != 11) return '';
        return '+7('
            . substr($value, 1, 3)
            . ') '
            . substr($value, 4, 3)
            . '-'
            . substr($value, 7, 2)
            . '-'
            . substr($value, 9);
    }
}
