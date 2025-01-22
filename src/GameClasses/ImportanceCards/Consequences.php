<?php

namespace App\GameClasses\ImportanceCards;

use Symfony\Component\Form\AbstractType;
use App\GameClasses\ImportanceCards\Value;

class Consequences extends AbstractType
{

    /**
     * Simulate who would win the trick if $card was played.
     *
     * @param array $card_player The card played by the player.
     * @param array $card The card The AI consider to play.
     * @param array $data $Situation main properties.
     * @return string Who would win the trick.
     */
    private function trick_game_simulation($card_player, $card, $data)
    {
        if (
            $data['playFirst'] === "player" and $card_player['score'] > $card['score'] and $card['suit'] === $card_player['suit'] or
            $data['playFirst'] === "player" and $card_player['score'] === $card['score'] and $card['suit'] === $card_player['suit'] or
            $data['playFirst'] === "player" and $card['suit'] !== $data['trump']['suit'] and $card['suit'] !== $card_player['suit'] or
            $data['playFirst'] === "computer" and $card_player['score'] > $card['score'] and $card_player['suit'] === $card['suit'] or
            $data['playFirst'] === "computer" and $card['suit'] !== $data['trump']['suit'] and $card_player['suit'] === $data['trump']['suit']
        ) {
            return 'computer loose';
        } else {
            return 'computer win';
        }
    }


    /**
     * Calculate a score for the evolution of the computer's situation if the indicated card is played.
     * Check if AI would win and then compare more or less consequences according to the game's difficulty.
     *
     * @param array $card The card The AI consider to play.
     * @param array $data $Situation main properties.
     * @param array $card_player The card played by the player.
     * @return array The card selected to be played.
     */
    public function consequencesCard(array $card, $data, array $card_player)
    {
        $value = new Value;
        $first_card_in_stack = $value->valueCard($data['stack'][0], $data['stack'], $data['handComputer'], $data['trump']);
        $second_card_in_stack = $value->valueCard($data['stack'][1], $data['stack'], $data['handComputer'], $data['trump']);
        

        $declaration_loss = 0; // gain of the sacrificed declaration if AI plays a card needed for this declaration.
        $declaration_gain = 0; // gain of the main declaration available for AI.
        $valuable_card_in_trick = 0; // gain if a 10 or an ace is in the current trick and if this card is able to win it.
        $declaration_player_gain = 0; // gain of the main declaration available for the player if he wins the trick.

        if ($this->trick_game_simulation($card_player, $card, $data) === 'computer win') {
            if (sizeof($data['declarationsAvailableComputer']) > 0 and $data['declarationsAvailableComputer'][0]['rank'] !== "trump's seven" and in_array($card['number'], $data['declarationsAvailableComputer'][0]['cards'])) {
                $declaration_loss = $data['declarationsAvailableComputer'][0]['gain'];
            }
            if (sizeof($data['declarationsAvailableComputer']) > 0 and $data['declarationsAvailableComputer'][0]['rank'] !== "trump's seven" and !in_array($card['number'], $data['declarationsAvailableComputer'][0]['cards'])) {
                $declaration_gain = $data['declarationsAvailableComputer'][0]['gain'] * 2;
            }
            $valuable_card_in_trick = $card_player['rank'] > 6 or $card['rank'] > 6 ? 20 : 0;
            $additional_value_card_in_stack = $first_card_in_stack['value'] - $second_card_in_stack['value'];
        } else {
            $valuable_card_in_trick = $card_player['rank'] > 6 or $card['rank'] > 6 ? -20 : 0;
            $declaration_player_gain = sizeof($data['declarationsAvailablePlayer']) > 0 ? $data['declarationsAvailablePlayer'][0]['gain'] : 0;
            $additional_value_card_in_stack = $second_card_in_stack['value'] - $first_card_in_stack['value'];
        }

        $consequences;
        if ($data['difficulty'] === 'expert') {
            $consequences = $additional_value_card_in_stack - $card['value'] + $declaration_gain + $valuable_card_in_trick - $declaration_player_gain - $declaration_loss;
        } elseif ($data['difficulty'] === 'hard') {
            $consequences = $valuable_card_in_trick - $card['value'] + $declaration_gain - $declaration_player_gain - $declaration_loss;
        } elseif ($data['difficulty'] === 'normal') {
            $consequences = $valuable_card_in_trick - $card['value'] + $declaration_gain;
        } else {
            $consequences = 1 - $card['value'];
        }

        $card['consequences'] = (int) $consequences;

        return $card;
    }
}