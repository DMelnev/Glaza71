<?php

namespace App\Entity;

use App\Repository\MainPageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MainPageRepository::class)
 */
class MainPage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $headTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $keywords;


    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     */
    private $showComments;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     */
    private $showArticles;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getHeadTitle(): ?string
    {
        return $this->headTitle;
    }

    public function setHeadTitle(string $headTitle): self
    {
        $this->headTitle = $headTitle;

        return $this;
    }

    public function getShowComments(): ?int
    {
        return $this->showComments;
    }

    public function setShowComments(int $showComments): self
    {
        $this->showComments = $showComments;

        return $this;
    }

    public function getShowArticles(): ?int
    {
        return $this->showArticles;
    }

    public function setShowArticles(int $showArticles): self
    {
        $this->showArticles = $showArticles;

        return $this;
    }
}