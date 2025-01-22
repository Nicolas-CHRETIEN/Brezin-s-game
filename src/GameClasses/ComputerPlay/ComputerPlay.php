<?php

namespace App\GameClasses\ComputerPlay;

use App\Entity\Situation;
use App\GameClasses\ToolsGame\ToolsGame;
use Symfony\Component\Form\AbstractType;
use App\GameClasses\ImportanceCards\Value;
use App\GameClasses\ImportanceCards\Consequences;

class ComputerPlay extends AbstractType
{

    /**
    * Check if the array verify the condition in the secund param.
    * If the condition is respected, the function return "true".
    *
    * @param array $array The array, usualy a card.
    * @param callable $fn the callback.
    */
    private function array_any(array $array, callable $fn) {
        foreach ($array as $value) {
            if($fn($value)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Filter the hand to return only the cards wich can win for sure the trick game.
     *
     * @param array $hand An array with cards.
     * @param string $suit_trump the trump's suit.
     * @return array An array with card(s) which can win the trick game for sure.
     */
    private function Play_first_card_to_win_for_sure($hand, $suit_trump)
    {
        $Tools = new ToolsGame;
        $selected_cards = []; // Can exist if computer has a ace of trump which can't be beatten. If it has both, a ten of trump is enought. If it has both ...
        $ace_trump = array_filter($hand, function($card) use ($suit_trump) {
            return $card['rank'] === 8 and $card['suit'] === $suit_trump and !$card['declarable'];
        });
        $ten_trump = array_filter($hand, function($card) use ($suit_trump) {
            return $card['rank'] === 7 and $card['suit'] === $suit_trump and !$card['declarable'];
        });
        $king_trump = array_filter($hand, function($card) use ($suit_trump) {
            return $card['rank'] === 6 and $card['suit'] === $suit_trump and !$card['declarable'];
        });
        if (sizeof($ace_trump) !== 0) {
            array_push($selected_cards, ...$ace_trump);
        }
        if (sizeof($ace_trump) === 2 and sizeof($ten_trump) !== 0) {
            array_push($selected_cards, ...$ten_trump);
        }
        if (sizeof($ace_trump) === 2 and sizeof($ten_trump) === 2 and sizeof($king_trump) !== 0) {
            array_push($selected_cards, ...$king_trump);
        }
        $selected_cards = $Tools->array_sort($selected_cards, 'value', 1);

        return $selected_cards;
    }

    /**
     * Filter the array in order to return only the cards which have good chances to win the trick, but which can't be used for a declaration.
     *
     * @param array $hand An array with cards.
     * @param string $suit_trump the trump's suit.
     * @return array An array with card(s) which have good chances to win the trick, but which can't be used for a declaration.
     */
    private function Play_first_card_to_win_maybe($hand, $suit_trump)
    {
        $Tools = new ToolsGame;
        $selected_cards = array_filter($hand, function($card) use ($suit_trump) {
            return $card['score'] < 14 and $card['value'] < 40 and !$card['declarable']; // Declarable is an element added to the computer's card to know if they can be used for a declaration or not.
        });
        // sort the cards to put thosse with the highest score first.
        sizeof($selected_cards) > 0 && $selected_cards = $Tools->array_sort($selected_cards, 'score', 0);
        return $selected_cards;
    }

    /**
     * If this function is used, it means that the computer has no interest in winning the trick game or it has no good card without importance to be played.
     * Therefore it must play the card which will have the smallest consequences, so the smallest value.
     *
     * @param array $hand An array with cards.
     * @return array The card with the smallest value.
     */
    private function Play_first_card_to_loose($hand)
    {
        $Tools = new ToolsGame;
        // Select the smallest card's value:
        $hand = $Tools->array_sort($hand, 'value', 1);
                
        return $hand[0];
    }

    /**
     * Select a card to be played as AI plays first.
     * As the player has not play yet, Computer can't know if it's gonna win the trick or not.
     * Therefore it will have to choose between play its best cards, to win absolutly ($cards_to_win_for_sure), play a good card which can't be used for a declaration ($cards_to_win_maybe) if it has one, or play it's smallest card ($card_to_loose).
     * In order to guess if it worth it, the computer will compare more or less elements according to the selected difficulty at the game's beguining.
     * It will then select an option between win for sure trick game, have good chances to win it without playing a card suitable for declaration and play its lowest card.
     *
     * @param Situation $Situation
     * @return array The card chosen to be played.
     */
    public function computerPlayFirst(Situation $Situation)
    {
        $data = $Situation->getSituation();
        $selected_cards;
        $Value = new Value;

        // If the rate is positive, computer must try to win the trick:
        $first_card_in_stack = $Value->valueCard($data['stack'][0], $data['stack'], $data['handComputer'], $data['trump']);
        $second_card_in_stack = $Value->valueCard($data['stack'][1], $data['stack'], $data['handComputer'], $data['trump']);
        $declaration_gain = sizeof($data['declarationsAvailableComputer']) > 0 ? $data['declarationsAvailableComputer'][0]['gain'] : 0; // Check if it has a declaration to make.
        $declaration_player_gain = sizeof($data['declarationsAvailablePlayer']) > 0 ? $data['declarationsAvailablePlayer'][0]['gain'] : 0; // Check if the player has a declaration to make (if yes the computer'd better to win the trick to forbid the player to make it).

        $goal_computer_hard = $declaration_gain + $declaration_player_gain + $first_card_in_stack['value'] - $second_card_in_stack['value'];
        $goal_computer_normal = $declaration_gain;

        $new_hand = [];
        foreach ($data['handComputer'] as $card) {
            $new_card = $Value->valueCard($card, $data['stack'], $data['handComputer'], $data['trump']);
            array_push($new_hand, $new_card);
        }
        $data['handComputer'] = $new_hand;

        $cards_to_win_for_sure = array_values($this->Play_first_card_to_win_for_sure($data['handComputer'], $data['trump']['suit'])); // array_values is usefull to set the keys to normal numeric keys starting from 0.
        $cards_to_win_maybe = array_values($this->Play_first_card_to_win_maybe($data['handComputer'], $data['trump']['suit'])); // array_values is usefull to set the keys to normal numeric keys starting from 0.
        $card_to_loose = $this->Play_first_card_to_loose($data['handComputer']);

        if (sizeof($data['stack']) < 3 and sizeof($cards_to_win_for_sure) > 0) { // If computer can win trick game last turn for sure, it must do it to prevent player to declare.
            $selected_cards = $cards_to_win_for_sure[0];
        } else if (($goal_computer_hard > 200 and $Situation->getDifficulty() === 'expert') or (isset($cards_to_win_maybe[0]) and $goal_computer_normal > $cards_to_win_maybe[0]['value'] and $Situation->getDifficulty() !== 'expert')) { // Computer try to win if it's on expert mode and according to the information it have, it worth it. It must also try to win if it's not in expert mode, but the (smaller) information it have worth it because the benefits in case of winning would be more important than the card's value.
            if (sizeof($cards_to_win_maybe) > 0) {
                $selected_cards = $cards_to_win_maybe[0];
            } else {
                $selected_cards = $card_to_loose;
            }
        } else {
            $selected_cards = $card_to_loose;
        }

        // Ai must delete the elements added to the card to make a choice before returning it:
        array_splice($selected_cards, sizeof($selected_cards) - 2, 2); // Remove additional elements added to the card to decide.

        return $selected_cards;
    }


    /**
     * Select a card to be played as AI plays second.
     * Here the choice is easier because the card played by the player is known.
     * Thank's to this, it is easy to know which card to play in order to win the trick if it worth it.
     * This function calculate consequences to play each card with more or less accuracy depending on the difficulty choosen at the game's beguining thank's to the consequences's class.
     * Then it sorts the cards according to their consequences.
     * After all the function select the card which have the most favourable consequences to be played.
     *
     * @param array $card_player The card played by the player
     * @param Situation $situation
     * @return array The card chosen to be played.
     */
    public function computerPlaySecond(array $card_player, Situation $situation)
    {
        $data = $situation->getSituation();
        $data['difficulty'] = $situation->getDifficulty();
        $gain = sizeof($data['declarationsAvailableComputer']) === 0 ? 0 : $data['declarationsAvailableComputer'][0]['gain'];
        $consequences = new Consequences;
        $Tools = new ToolsGame;
        $Value = new Value;
    
        $new_hand = [];
        foreach ($data['handComputer'] as $card) {
            $new_card = $Value->valueCard($card, $data['stack'], $data['handComputer'], $data['trump']);
            $new_card = $consequences->consequencesCard($new_card, $data, $card_player);
            array_push($new_hand, $new_card);
        }
        $data['handComputer'] = $new_hand;
    

        $new_hand = $Tools->array_sort($new_hand, 'consequences', -1);
        $selected_card = $new_hand[0];
        array_splice($selected_card, sizeof($selected_card) - 3, 3); // Remove consequences value and declarable.
        return $selected_card;
    }
}