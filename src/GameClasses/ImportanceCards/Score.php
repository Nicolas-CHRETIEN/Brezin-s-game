<?php

namespace App\GameClasses\ImportanceCards;

use Symfony\Component\Form\AbstractType;

class Score extends AbstractType
{
    /**
     * Add a score rate to the indicated card according to the trump's suit.
     *
     * @param array $card
     * @param string $suit_trump
     * @return array the card with the "score" element added.
     */
    public function scoreCard(array $card, $suit_trump)
    {
        $score = 0;
        $card['suit'] === $suit_trump && $score += 10;
        $score += $card['rank'];
        $card['score'] = $score;

        return $card;
    }
}