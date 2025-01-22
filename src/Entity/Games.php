<?php

namespace App\Entity;

use App\Repository\GamesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GamesRepository::class)
 */
class Games
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
    private $winner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $scorePlayer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $scoreComputer;

    /**
     * @ORM\Column(type="array")
     */
    private $declarationsMadePlayer = [];

    /**
     * @ORM\Column(type="array")
     */
    private $declarationsMadeComputer = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $valuablesCardsInTricksPlayer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $valuablesCardsInTricksComputer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roundsNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="gameID")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userID;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $difficulty;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(string $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getScorePlayer(): ?string
    {
        return $this->scorePlayer;
    }

    public function setScorePlayer(string $scorePlayer): self
    {
        $this->scorePlayer = $scorePlayer;

        return $this;
    }

    public function getScoreComputer(): ?string
    {
        return $this->scoreComputer;
    }

    public function setScoreComputer(string $scoreComputer): self
    {
        $this->scoreComputer = $scoreComputer;

        return $this;
    }

    public function getDeclarationsMadePlayer(): ?array
    {
        return $this->declarationsMadePlayer;
    }

    public function setDeclarationsMadePlayer(array $declarationsMadePlayer): self
    {
        $this->declarationsMadePlayer = $declarationsMadePlayer;

        return $this;
    }

    public function getDeclarationsMadeComputer(): ?array
    {
        return $this->declarationsMadeComputer;
    }

    public function setDeclarationsMadeComputer(array $declarationsMadeComputer): self
    {
        $this->declarationsMadeComputer = $declarationsMadeComputer;

        return $this;
    }

    public function getValuablesCardsInTricksPlayer(): ?string
    {
        return $this->valuablesCardsInTricksPlayer;
    }

    public function setValuablesCardsInTricksPlayer(string $valuablesCardsInTricksPlayer): self
    {
        $this->valuablesCardsInTricksPlayer = $valuablesCardsInTricksPlayer;

        return $this;
    }

    public function getValuablesCardsInTricksComputer(): ?string
    {
        return $this->valuablesCardsInTricksComputer;
    }

    public function setValuablesCardsInTricksComputer(string $valuablesCardsInTricksComputer): self
    {
        $this->valuablesCardsInTricksComputer = $valuablesCardsInTricksComputer;

        return $this;
    }

    public function getRoundsNumber(): ?string
    {
        return $this->roundsNumber;
    }

    public function setRoundsNumber(string $roundsNumber): self
    {
        $this->roundsNumber = $roundsNumber;

        return $this;
    }

    public function getUserID(): ?Users
    {
        return $this->userID;
    }

    public function setUserID(?Users $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(?string $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }
}
