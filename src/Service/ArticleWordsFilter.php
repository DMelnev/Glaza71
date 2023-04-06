<?php


namespace App\Service;


class ArticleWordsFilter
{
    public function filter($string, $words = []): string
    {
        $array = explode(' ', $string);
        foreach ($words as $word) {
            foreach ($array as $key => $item) {
                if (stristr($item, $word) !== false) {
                    unset($array[$key]);
                }
            }
        }
        return implode(' ', $array);
    }
}