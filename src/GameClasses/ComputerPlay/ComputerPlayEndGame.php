<?php

namespace App\GameClasses\ComputerPlay;

use App\Entity\Situation;
use App\GameClasses\ToolsGame\ToolsGame;
use Symfony\Component\Form\AbstractType;

class ComputerPlayEndGame extends AbstractType
{

    /**
     * Filter the computer's hand to select the best card to play.
     * The goal of this part of the game is to pick up ten and aces cards by winning the trick's game.
     * Therefore, there are 4 possibilities for the computer to play efficiently:
     * If it has more trump cards than the player, it must play it first in order to prevent him to cut his cards with trump if he hasn't got the requested suit.
     * Else, if it has aces cards, it must play them first.
     * Else, if it has 10 cards, it must play them after playing its aces.
     * Then it plays the other cards.
     *
     * @param Situation $Situation
     * @return array The selected card.
     */
    public function computerPlayFirstEndGame(Situation $Situation)
    {
        $Tools = new ToolsGame;
        $data = $Situation->getSituation();
        $aces_not_trump = array_values(array_filter($data['handComputer'], function($card) use ($data) {
            return $card['rank'] === 8 and $card['suit'] !== $data['trump']['suit'];
        }));
        $tens_not_trump = array_values(array_filter($data['handComputer'], function($card) use ($data) {
            return $card['rank'] === 7 and $card['suit'] !== $data['trump']['suit'];
        }));

        // If I have more than 7 trump cards in hand, play the best one
        // If I have an ace card(s) not trump, play one of them.
        // If I have a ten card(s) not trump, play one of them.
        // else: play the lowest card I Have

        if (sizeof($aces_not_trump) > 0) {
            return $aces_not_trump[0];
        } else if (sizeof($tens_not_trump) > 0) {
            return $tens_not_trump[0];
        } else {
            $data['handComputer'] = $Tools->array_sort($data['handComputer'], 'score', -1);
            return $data['handComputer'][0];
        }
    }

    /**
     * Filter the computer's hand to select the best card to play.
     * To win, the AI must play an ace or a 10 card each time it will give it the possibility to win the trick.
     * Otherwise, it must play the smallest card it have in hand in order to keep the stronguest.
     * Therefore this function calculates the cards which can win the trick according to card played by the player.
     *
     * @param Situation $Situation
     * @param array $card_player The card played by the player.
     * @return array The selected card.
     */
    public function computerPlaySecondEndGame($card_player, Situation $Situation)
    {
        $Tools = new ToolsGame;
        $data = $Situation->getSituation();

        $data['handComputer'] = $Tools->array_sort($data['handComputer'], 'score', 1);
        $cards_to_win = array_values(array_filter($data['handComputer'], function($card) use ($card_player) {
            return $card['score'] > $card_player['score'] and $card['suit'] === $card_player['suit'];
        }));
        $ten_cards_to_win = array_values(array_filter($data['handComputer'], function($card) use ($card_player) {
            return $card['score'] > $card_player['score'] and $card['suit'] === $card_player['suit'] and $card['rank'] === 7;
        }));
        $ace_cards_to_win = array_values(array_filter($data['handComputer'], function($card) use ($card_player) {
            return $card['score'] > $card_player['score'] and $card['suit'] === $card_player['suit'] and $card['rank'] === 8;
        }));
        $cards_to_loose = array_values(array_filter($data['handComputer'], function($card) use ($card_player) {
            return $card['score'] < $card_player['score'] and $card['suit'] === $card_player['suit'] or $card['score'] === $card_player['score'] and $card['suit'] === $card_player['suit'];
        }));
        $trump_cards_in_hand = array_values(array_filter($data['handComputer'], function($card) use ($data) {
            return $card['suit'] === $data['trump']['suit'];
        }));
        
        // If P play trump or suit I haven't in hand, play the lowest card I have.
        // else: play a random card.
    
        if ((sizeof($cards_to_win) > 0) and ($card_player['suit'] === $data['trump']['suit'])) { // If P played trump card and I have a better trump one, play the lowest one better than the one he played.
            return $cards_to_win[0];
        } else if (sizeof($ten_cards_to_win) > 0) { // If P played a  card and I have a 10 card of the same suit better than the one he played, play it.
            return $ten_cards_to_win[0];
        } else if (sizeof($ace_cards_to_win) > 0) { // If P played a  card and I have an ace card of the same suit better than the one he played, play it.
            return $ace_cards_to_win[0];
        } else if ((sizeof($cards_to_win) > 0) and ($card_player['suit'] !== $data['trump']['suit'])) { // If P played a card with a suit different than trump and I have an ace card of the same suit better, play the lowest one.
            return $cards_to_win[0];
        } else if ((sizeof($cards_to_win) === 0) and (sizeof($cards_to_loose) > 0)) { // if P played a card and I don't have a better one of the same suit, but I have one of the same suit, play the lowest one.
            return $cards_to_loose[0];
        } else if ((sizeof($cards_to_win) === 0) and (sizeof($cards_to_loose) === 0) and (sizeof($trump_cards_in_hand) > 0)) { // If I don't have a card of the same suit best than the one he played, but I have trump, play the lowest one.
            return $trump_cards_in_hand[0];
        } else if ((sizeof($cards_to_win) === 0) and (sizeof($cards_to_loose) === 0) and (sizeof($trump_cards_in_hand) === 0)) {// If I don't have a card of the same suit best than the one he played, and I don't have trump, play the lowest card I have in score.
            return array_values($data['handComputer'])[sizeof($data['handComputer']) - 1];
        } else {
            echo('ERROR! problem in conditions in computerPlaySecondEndGame.');
        }
    }
}