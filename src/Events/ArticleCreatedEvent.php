<?php


namespace App\Events;

use App\Entity\Article;
use Symfony\Contracts\EventDispatcher\Event;

class ArticleCreatedEvent extends Event
{

    private Article $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }
}