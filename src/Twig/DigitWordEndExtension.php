<?php

namespace App\Twig;

use App\Service\DigitWordsEnd;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DigitWordEndExtension extends AbstractExtension
{
    private DigitWordsEnd $wordsEnd;

    public function __construct(DigitWordsEnd $wordsEnd)
    {
        $this->wordsEnd = $wordsEnd;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('format_ru_articles_end', [$this, 'articles']),
            new TwigFilter('format_ru_comments_end', [$this, 'comments']),
        ];
    }

    public function articles($value): string
    {
        return $this->wordsEnd->format($value, 'статей', 'статья', 'статьи');
    }
    public function comments($value): string
    {
        return $this->wordsEnd->format($value, 'комментариев', 'комментарий', 'комментария');
    }
}
