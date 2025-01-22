<?php

namespace App\GameClasses\DrawInStack;

use App\Entity\Situation;
use App\GameClasses\ToolsGame\ToolsGame;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;

class Draw extends AbstractType
{

    /**
     * Method to be called when there is only 1 card left in the stack.
     * It deals the last card to the first player to draw and give the trump card to the other player.
     * This function replace then the trump card by a simple image of the old trump card's suit.
     * It also delete the "declared" element inside the player's card. There is no need anymore for this as the cards can't be declared anymore at this stage. 
     * Furthermore, by doing this the declared cards, which are put down on the table to be visible, go back to the hand as it is supposed to be.
     * Then the function register the situation.
     *
     * @param Situation $Situation
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function drawLastTurn(Situation $Situation, EntityManagerInterface $Manager)
    {
        $data = $Situation->getSituation();
        $suit_trump = '/images/svg/' . $data['trump']['suit'] . '.svg';
    
    
        // Draw cards.
        if ($data['playFirst'] === 'player') {
            array_push($data['handPlayer'], $data['stack'][0]); // Draw a the last card for player.
            array_push($data['handComputer'], $data['trump']); // Add trump card to the computer hand.
        } else {
            array_push($data['handComputer'], $data['stack'][0]); // Draw a the last card for computer.
            array_push($data['handPlayer'], $data['trump']); // Add trump card to the player hand.
        }
        $data['stack'] = [];

        $data['handPlayer'] = $this->undeclareCards($data['handPlayer']);
        $data['handComputer'] = $this->undeclareCards($data['handComputer']);
        $data['trump'] = ['img' => $suit_trump, 'suit' => $data['trump']['suit']];

        $Situation->setSituation($data);
        
        $Manager->persist($Situation);
        $Manager->flush();
        
        $this->playerSortCardsEndGame($Situation, $Manager);
    }

    /**
     * Add a new element to the player's card to indicate if he has the right to play them or not.
     * At the end of the game of the game, the player who plays second don't have the right to play every cards anymore.
     * This function checks if the cards in his hand are in the same suit of the one played by the computer.
     * If the card played has the same suit that the trump card, then it checks that the card the player plays is stronger.
     * Therefore the function save the $Situation.
     *
     * @param Situation $Situation
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function playableCards(Situation $Situation, EntityManagerInterface $Manager)
    {
        $data = $Situation->getSituation();
    
        $same_suit_cards = array_values(array_filter($data['handPlayer'], function($card) use ($data) {
            return $card['suit'] === $data['computerCardPlayed']['suit'];
        }));
        $best_cards_same_suit = array_values(array_filter($data['handPlayer'], function($card) use ($data) {
            return $card['suit'] === $data['computerCardPlayed']['suit'] and $card['score'] > $data['computerCardPlayed']['score'];
        }));
        $cards_trump = array_values(array_filter($data['handPlayer'], function($card) use ($data) {
            return $card['suit'] === $data['trump']['suit'];
        }));
        $playable_cards = [];
    
        // Check if a card can be played or not.
        if ($data['computerCardPlayed']['suit'] === $data['trump']['suit']) {
            if (sizeof($best_cards_same_suit) > 0) {
                $playable_cards = $best_cards_same_suit;
            } else if (sizeof($same_suit_cards) > 0) {
                $playable_cards = $same_suit_cards;
            } else {
                $playable_cards = $data['handPlayer'];
            }
        } else {
            if (sizeof($same_suit_cards) > 0) {
                $playable_cards = $same_suit_cards;
            } else if (sizeof($cards_trump) > 0) {
                $playable_cards = $cards_trump;
            } else {
                $playable_cards = $data['handPlayer'];
            }
        }
    
        $new_hand = [];
        foreach ($data['handPlayer'] as $card) { // Take back all the declared cards in hand and show which ones can be played.
            if (in_array($card, $playable_cards)) {
                $card['unPlayable'] = null;
            } else {
                $card['unPlayable'] = 1;
            }
            array_push($new_hand, $card);
        }
    
        
        $Situation->setHandPlayer($new_hand);
        $Manager->persist($Situation);
        $Manager->flush();
    }

    /**
     * Delete the "declared" element for each card.
     *
     * @param array $hand Array of cards.
     * @return array Modified array of cards.
     */
    public function undeclareCards($hand)
    {
        $new_hand = [];
        foreach ($hand as $card) {
            $card['declared'] = null;
            unset($card['declared']);
            array_push($new_hand, $card);
        }
        return $new_hand;
    }

    /**
     * Sort the player's cards in order to:
     * Place the jacks of spades and the queens of diamonds first on the left.
     * Then place the trump cards in an ascending order.
     * Thene place the other cards in a decreasing order.
     * Therefore the function save the $Situation.
     *
     * @param Situation $Situation
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function playerSortCards(Situation $Situation, EntityManagerInterface $Manager)
    {
        $data = $Situation->getSituation();
        $Tools = new ToolsGame;
    
        $new_hand = $Tools->array_sort($data['handPlayer'], 'score', -1);
    
        $cards_trump = array_values(array_filter($new_hand, function($card) use($data) {
            return $card['suit'] === $data['trump']['suit'];
        }));
        $cards_bresin = array_values(array_filter($new_hand, function($card) use($data) {
            return $card['suit'] !== $data['trump']['suit'] and $card['suit'] === 'spade' and $card['rank'] === 4 or $card['suit'] !== $data['trump']['suit'] and $card['suit'] === 'diamond' and $card['rank'] === 5;
        }));
        $cards_not_trump = array_values(array_filter($new_hand, function($card) use($data, $cards_bresin) {
            return $card['suit'] !== $data['trump']['suit'] and !in_array($card, $cards_bresin);
        }));
    
        $sorted_cards_trump = $Tools->array_sort($cards_trump, 'score', 1);
        $sorted_cards_not_trump = $Tools->array_sort($cards_not_trump, 'score', -1);
        $new_hand = array_merge($cards_bresin, $sorted_cards_trump, $sorted_cards_not_trump);
        $Situation->setHandPlayer($new_hand);
        $Manager->persist($Situation);
        $Manager->flush();
    }

    /**
     * Sort the player's cards at the end of the game.
     * Gather all the cards of a same suit togather and sort them in an ascending order.
     *
     * @param Situation $Situation
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function playerSortCardsEndGame(Situation $Situation, EntityManagerInterface $Manager)
    {
        $data = $Situation->getSituation();
        $Tools = new ToolsGame;
    
        $sorted_hand = $Tools->array_sort($data['handPlayer'], 'score', 1);

        $trump_suit = $data['trump']['suit'];
        $second_suit;
        $third_suit;
        $fourth_suit;

        if ($trump_suit === 'heart' or $trump_suit === 'diamond') {
            $second_suit = 'spade';
        } else {
            $second_suit = 'heart';
        }

        if ($second_suit === 'heart') {
            if ($trump_suit = 'spade') {
                $third_suit = 'club';
                $fourth_suit = 'diamond';
            } else {
                $third_suit = 'spade';
                $fourth_suit = 'diamond';
            }
        } else {
            if ($trump_suit = 'heart') {
                $third_suit = 'diamond';
                $fourth_suit = 'club';
            } else {
                $third_suit = 'heart';
                $fourth_suit = 'club';
            }
        }
    
        $trump_deck = array_values(array_filter($sorted_hand, function($card) use($trump_suit) {
            return $card['suit'] === $trump_suit;
        }));

        $second_deck = array_values(array_filter($sorted_hand, function($card) use($second_suit) {
            return $card['suit'] === $second_suit;
        }));
        $third_deck = array_values(array_filter($sorted_hand, function($card) use($third_suit) {
            return $card['suit'] === $third_suit;
        }));
        $fourth_deck = array_values(array_filter($sorted_hand, function($card) use($fourth_suit) {
            return $card['suit'] === $fourth_suit;
        }));
        
        $new_hand = array_values([...$trump_deck, ...$second_deck, ...$third_deck, ...$fourth_deck]);
        $Situation->setHandPlayer($new_hand);
        $Manager->persist($Situation);
        $Manager->flush();
    }
}