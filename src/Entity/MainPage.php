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
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $keywords;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $headTitle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showComments;

    /**
     * @ORM\Column(type="boolean")
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function isShowComments(): ?bool
    {
        return $this->showComments;
    }

    public function setShowComments(bool $showComments): self
    {
        $this->showComments = $showComments;

        return $this;
    }

    public function isShowArticles(): ?bool
    {
        return $this->showArticles;
    }

    public function setShowArticles(bool $showArticles): self
    {
        $this->showArticles = $showArticles;

        return $this;
    }
}
