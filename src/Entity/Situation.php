<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SituationRepository;
use App\GameClasses\ImportanceCards\Score;


/**
 * @ORM\Entity(repositoryClass=SituationRepository::class)
 */
class Situation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $init;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $playFirst;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stage;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $trump;

    /**
     * @ORM\Column(type="array")
     */
    private $stack = [];

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $playerCardPlayed;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $computerCardPlayed;

    /**
     * @ORM\Column(type="array")
     */
    private $handPlayer = [];

    /**
     * @ORM\Column(type="array")
     */
    private $handComputer = [];

    /**
     * @ORM\Column(type="array")
     */
    private $trickPlayer = [];

    /**
     * @ORM\Column(type="array")
     */
    private $trickComputer = [];

    /**
     * @ORM\Column(type="array")
     */
    private $declarationsAvailablePlayer = [];

    /**
     * @ORM\Column(type="array")
     */
    private $declarationsAvailableComputer = [];

    /**
     * @ORM\Column(type="array")
     */
    private $score;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, inversedBy="situation", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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

    public function isInit(): ?bool
    {
        return $this->init;
    }

    public function setInit(bool $init): self
    {
        $this->init = $init;

        return $this;
    }

    public function getPlayFirst(): ?string
    {
        return $this->playFirst;
    }

    public function setPlayFirst(string $playFirst): self
    {
        $this->playFirst = $playFirst;

        return $this;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(string $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getTrump(): ?array
    {
        return $this->trump;
    }

    public function setTrump(?array $trump): self
    {
        $this->trump = $trump;

        return $this;
    }

    public function getStack(): ?array
    {
        return $this->stack;
    }

    public function setStack(array $stack): self
    {
        $this->stack = $stack;

        return $this;
    }

    public function getPlayerCardPlayed(): ?array
    {
        return $this->playerCardPlayed;
    }

    public function setPlayerCardPlayed(?array $playerCardPlayed): self
    {
        $this->playerCardPlayed = $playerCardPlayed;

        return $this;
    }

    public function getComputerCardPlayed(): ?array
    {
        return $this->computerCardPlayed;
    }

    public function setComputerCardPlayed(?array $computerCardPlayed): self
    {
        $this->computerCardPlayed = $computerCardPlayed;

        return $this;
    }

    public function getHandPlayer(): ?array
    {
        return $this->handPlayer;
    }

    public function setHandPlayer(array $handPlayer): self
    {
        $this->handPlayer = $handPlayer;

        return $this;
    }

    public function getHandComputer(): ?array
    {
        return $this->handComputer;
    }

    public function setHandComputer(array $handComputer): self
    {
        $this->handComputer = $handComputer;

        return $this;
    }

    public function getTrickPlayer(): ?array
    {
        return $this->trickPlayer;
    }

    public function setTrickPlayer(array $trickPlayer): self
    {
        $this->trickPlayer = $trickPlayer;

        return $this;
    }

    public function getTrickComputer(): ?array
    {
        return $this->trickComputer;
    }

    public function setTrickComputer(array $trickComputer): self
    {
        $this->trickComputer = $trickComputer;

        return $this;
    }

    public function getDeclarationsAvailablePlayer(): ?array
    {
        return $this->declarationsAvailablePlayer;
    }

    public function setDeclarationsAvailablePlayer(array $declarationsAvailablePlayer): self
    {
        $this->declarationsAvailablePlayer = $declarationsAvailablePlayer;

        return $this;
    }

    public function getDeclarationsAvailableComputer(): ?array
    {
        return $this->declarationsAvailableComputer;
    }

    public function setDeclarationsAvailableComputer(array $declarationsAvailableComputer): self
    {
        $this->declarationsAvailableComputer = $declarationsAvailableComputer;

        return $this;
    }

    public function getScore(): ?array
    {
        return $this->score;
    }

    public function setScore(array $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSituation(): ?array
    {
        return [
            'id' => $this->id,
            'init' => $this->init,
            'playFirst' => $this->playFirst,
            'stage' => $this->stage,
            'trump' => $this->trump,
            'stack' => $this->stack,
            'playerCardPlayed' => $this->playerCardPlayed,
            'computerCardPlayed' => $this->computerCardPlayed,
            'handPlayer' => $this->handPlayer,
            'handComputer' => $this->handComputer,
            'trickPlayer' => $this->trickPlayer,
            'trickComputer' => $this->trickComputer,
            'declarationsAvailablePlayer' => $this->declarationsAvailablePlayer,
            'declarationsAvailableComputer' => $this->declarationsAvailableComputer,
            'score' => $this->score
        ];
    }

    public function setSituation(array $situation): self
    {
        $this->init = $situation['init'];
        $this->playFirst = $situation['playFirst'];
        $this->stage = $situation['stage'];
        $this->trump = $situation['trump'];
        $this->stack = $situation['stack'];
        $this->playerCardPlayed = $situation['playerCardPlayed'];
        $this->computerCardPlayed = $situation['computerCardPlayed'];
        $this->handPlayer = $situation['handPlayer'];
        $this->handComputer = $situation['handComputer'];
        $this->trickPlayer = $situation['trickPlayer'];
        $this->trickComputer = $situation['trickComputer'];
        $this->declarationsAvailablePlayer = $situation['declarationsAvailablePlayer'];
        $this->declarationsAvailableComputer = $situation['declarationsAvailableComputer'];
        $this->score = $situation['score'];

        return $this;
    }



// --------------------- Game's methods -----------------------

    /**
     * Deal cards from the stack.
     * Deal 9 cards to each player.
     * Set the trump with the nineteenth card.
     * Add 10 points to the next player to play's score if the trump's card is a seven.
     *
     * @param array $cards
     * @return void
     */
    public function deal($cards)
    {
        $stack = $cards;
        // Set properties:

        $trump = $cards[0]; // The first one for the trump.

        $hand_player =  array_slice($stack, 1, 9);// Nine following cards of the shuffled stack for the player.
        $hand_computer = array_slice($stack, 10, 9); // Nine following cards of the shuffled stack for the computer.

        array_splice($stack, 0, 19);

        $this->trump = $trump;
        $this->handPlayer = $hand_player;
        $this->handComputer = $hand_computer;
        $this->stack = $stack; // Cards which remain are the stack.
        $this->stage = 'draw'; // Next step for the player will be to draw another card.
        $this->score['round']++;
        if ($trump['rank'] === 1) {
            if ($this->playFirst === 'player') {
                $this->score['player1'] += 10;
            } else {
                $this->score['player2'] += 10;
            }
        }
    }

    /**
     * Player draws one card in stack.
     *
     * @return void
     */
    public function playerDrawCard()
    {
        $hand_player = $this->handPlayer;
        $stack = $this->stack;
        array_push($hand_player, $stack[0]);
        array_splice($stack, 0, 1);
        $this->handPlayer = $hand_player;
        $this->stack = $stack;
    }

    /**
     * Computer draws one card in stack.
     *
     * @return void
     */
    public function computerDrawCard()
    {
        $hand_computer = $this->handComputer;
        $stack = $this->stack;
        array_push($hand_computer, $stack[0]);
        array_splice($stack, 0, 1);
        $this->handComputer = $hand_computer;
        $this->stack = $stack;
    }

    /**
     * Player plays one card in player's hand.
     *
     * @return void
     */
    public function playerPlayCard($card)
    {
        $index_card;
        if (!in_array($card, $this->handPlayer)) {
            echo 'Error card doesn\'t exist in array - playerPlayCard method (Situation).';
        } else {
            $index_card = array_search($card, $this->handPlayer);
        }
        $hand_player = $this->handPlayer;
        array_values(array_splice($hand_player, $index_card, 1))[0];
        $this->handPlayer = $hand_player;
        $this->playerCardPlayed = $card;
    }

    /**
     * Computer plays one card in player's hand.
     *
     * @return void
     */
    public function computerPlayCard($card)
    {
        $index_card_computer;
        if (!in_array($card, $this->handComputer)) {
            echo 'Error card doesn\'t exist in array - computerPlayCard method (Situation).';
        } else {
            $index_card_computer = array_search($card, $this->handComputer);
        }
        $hand_computer = $this->handComputer;
        array_splice($hand_computer, $index_card_computer, 1)[0];
        $this->handComputer = $hand_computer;
        $this->computerCardPlayed = $card;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
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