<?php

namespace App\Entity;

use Assert\NotBlank;
use App\Entity\Users;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @UniqueEntity(fields={"email"}, message="Un compte utilisant cet email a déjà été créé.")
 */
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
    * @use Assert\NotBlank;()
    * @Assert\Length(
    *      min = 2,
    *      max = 255,
    *      minMessage = "Ce champ ne peut pas être inférieur à 2 caractères",
    *      maxMessage = "Ce champ ne peut pas excéder 255 caractères",
    *      allowEmptyString = false
    * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank()
    * @Assert\Length(
    *      min = 2,
    *      max = 255,
    *      minMessage = "Ce champ ne peut pas être inférieur à 2 caractères",
    *      maxMessage = "Ce champ ne peut pas excéder 255 caractères",
    *      allowEmptyString = false
    * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank()
    * @Assert\Length(
    *      min = 10,
    *      max = 255,
    *      minMessage = "Ce champ ne peut pas être inférieur à 10 caractères",
    *      maxMessage = "Ce champ ne peut pas excéder 255 caractères",
    *      allowEmptyString = false
    * )
     */
    private $introduction;

    /**
     * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank()
    * @Assert\Length(
    *      min = 3,
    *      max = 255,
    *      minMessage = "Ce champ ne peut pas être inférieur à 3 caractères",
    *      maxMessage = "Ce champ ne peut pas excéder 255 caractères",
    *      allowEmptyString = false
    * )
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity=Games::class, mappedBy="userID", orphanRemoval=true)
     */
    private $gameID;

    /**
     * @ORM\OneToOne(targetEntity=Situation::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $situation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ranking;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grade;

    public function __construct()
    {
        $this->gameID = new ArrayCollection();
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
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Games>
     */
    public function getGameID(): Collection
    {
        return $this->gameID;
    }

    public function addGameID(Games $gameID): self
    {
        if (!$this->gameID->contains($gameID)) {
            $this->gameID[] = $gameID;
            $gameID->setUserID($this);
        }

        return $this;
    }

    public function removeGameID(Games $gameID): self
    {
        if ($this->gameID->removeElement($gameID)) {
            // set the owning side to null (unless already changed)
            if ($gameID->getUserID() === $this) {
                $gameID->setUserID(null);
            }
        }

        return $this;
    }

    public function getSituation(): ?Situation
    {
        return $this->situation;
    }

    public function setSituation(?Situation $situation): self
    {
        // unset the owning side of the relation if necessary
        if ($situation === null && $this->situation !== null) {
            $this->situation->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($situation !== null && $situation->getUser() !== $this) {
            $situation->setUser($this);
        }

        $this->situation = $situation;

        return $this;
    }

    public function getRanking(): ?string
    {
        return $this->ranking;
    }

    public function setRanking(?string $ranking): self
    {
        $this->ranking = $ranking;

        return $this;
    }

    public function __constructGrade()
    {
        $grade = 0;
        foreach ($this->gameID as $Game) {
            if ($Game->getDifficulty() === 'expert' and $Game->getWinner() === 'player') {
                $grade += 1000;
            } elseif ($Game->getDifficulty() === 'hard' and $Game->getWinner() === 'player') {
                $grade += 100;
            } elseif ($Game->getDifficulty() === 'normal' and $Game->getWinner() === 'player') {
                $grade += 10;
            } elseif ($Game->getDifficulty() === 'easy' and $Game->getWinner() === 'player') {
                $grade += 1;
            }
        }
        $this->grade = $grade;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(?string $grade): self
    {
        $this->grade = $grade;

        return $this;
    }
}
