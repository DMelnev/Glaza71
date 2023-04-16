<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ShortBiteExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('shortBite', [$this, 'getShortBite']),
        ];
    }

    public function getShortBite($value): string
    {
        if (($value / 1024) < 1) {
            return $value . ' B';
        } elseif (($value / 1024 / 1024) < 1) {
            return round($value / 1024, 1) . ' K';
        } elseif (($value / 1024 / 1024 / 1024) < 1) {
            return round($value / 1024 / 1024, 1) . ' M';
        } else {
            return round($value / 1024 / 1024 / 1024, 1) . ' G';
        }
    }
}
