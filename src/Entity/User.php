<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="author")
     */
    private $articles;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $patronymic;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $surname;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activationCode;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     */
    private $comments;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $phoneNumbers;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $banned;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bannedReason;

    /**
     * @return mixed
     */
    public function getBannedReason()
    {
        return $this->bannedReason;
    }

    /**
     * @param mixed $bannedReason
     */
    public function setBannedReason($bannedReason): self
    {
        $this->bannedReason = $bannedReason;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanned()
    {
        return $this->banned;
    }

    /**
     * @param mixed $banned
     */
    public function setBanned($banned): self
    {
        $this->banned = $banned;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumbers()
    {
        return $this->phoneNumbers;
    }

    /**
     * @param mixed $phoneNumbers
     */
    public function setPhoneNumbers($phoneNumbers): self
    {
        $tempArray = [];
        foreach ($phoneNumbers as $phone){
            $clearedNumber = $this->clearPhoneNumber($phone);
            if ($clearedNumber) $tempArray[] = $this->clearPhoneNumber($phone);
        }
        $this->phoneNumbers = $tempArray;
        return $this;
    }

    public function addPhoneNumber(string $number): bool
    {
        $phone = $this->clearPhoneNumber($number);
        if (strlen($phone) != 11) return false;
        $array = $this->getPhoneNumbers();
        $array = array_merge($array, [$phone]);
        $this->setPhoneNumbers($array);
        return true;
    }

    public function deletePhoneNumber(string $number): bool
    {
        $array = $this->getPhoneNumbers();
        if (($key = array_search($this->clearPhoneNumber($number), $array)) !== false) {
            unset($array[$key]);
            $this->setPhoneNumbers($array);
            return true;
        }
        return false;
    }


    public function deletePhoneNumberByKey(int $key): bool
    {
        $array = $this->getPhoneNumbers();
        if (key_exists($key, $array)) {
            unset($array[$key]);
            $this->setPhoneNumbers($array);
            return true;
        }
        return false;
    }

    public function editPhoneNumberByKey(int $key, string $number): bool
    {
        $array = $this->getPhoneNumbers();
        $clearedNumber = $this->clearPhoneNumber($number);
        if (key_exists($key, $array) && strlen($clearedNumber) == 11) {
            $array[$key] = $clearedNumber;
            $this->setPhoneNumbers($array);
            return true;
        }
        return false;
    }

    public function clearPhoneNumber(?string $number): ?string
    {
        if (!$number) return null;
        $phone = preg_replace('/[^0-9]/', '', $number);
        if ($phone[0] != '7') $phone[0] = '7';
        if (strlen($phone) == 10 && $phone[0] == '9') $phone = '7' . $phone;
        return $phone;
    }

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_merge($this->roles, $roles);

        return $this;
    }

    public function newRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): self
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getConfirmed(): ?\DateTimeInterface
    {
        return $this->confirmed;
    }

    public function setConfirmed(?\DateTimeInterface $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getActivationCode(): ?string
    {
        return $this->activationCode;
    }

    public function setActivationCode(?string $activationCode): self
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    public function activateUser(?string $code): bool
    {
        if ($code && $this->confirmed < new \DateTime('-5 min')) {
            if ($code == $this->getActivationCode()) {
                $this->setConfirmed(new \DateTime('now'))
                    ->setRoles(['ROLE_REGISTERED'])
                    ->setActivationCode(null);
                return true;
            }
        }
        $this->confirmed = new \DateTime('now');
        return false;

    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
}
