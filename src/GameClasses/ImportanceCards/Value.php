<?php

namespace App\GameClasses\ImportanceCards;

use App\GameClasses\ToolsGame\ToolsGame;
use Symfony\Component\Form\AbstractType;



class Value extends AbstractType
{

    /**
     * Check is the card has been used for any declaration.
     * It returns true if the card has already been used for any declaration.
     *
     * @param $array $card the card I wanna check.
     * @return boolean
     */
    private function is_declared( $card ) {
        foreach ($card as $key => $value) {
            if ($key === 'declared') {
                return true;
            }
        }
    }

    /**
    * Check if the array verify the condition in the secund param.
    * If the condition is respected, the function return "true".
    *
    * @param array $array The array, usualy a card.
    * @param callable $fn the callback.
    * @return boolean
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
     * Check if the card has been declared for the specified declaration name.
     * It returns "true" if the card has already been used for the declaration.
     *
     * @param [type] $card the card I want to check
     * @param [type] $rank_declaration the name of the declaration I wanna know if the card as been used for.
     * @return boolean
     */
    private function is_not_declared_for($card, $rank_declaration) {
        if (!$this->is_declared($card)) {
            return true;
        } else {
            !$this->array_any($card['declared'], function($declaration) use($rank_declaration) {
                return $declaration['rank'] === $rank_declaration;
            });
        }
    }

    
    // =============================================== PROBABILITY FUNCTIONS ===============================================

    // N = Total number of cards in stack.
    // m = Number of good cards in stack to success.
    // n = Number of turns left.
    // k = Number of good cards needed to success.

    /**
     * The binomial coefficients are the positive integers that occur as coefficients in the binomial theorem. Commonly, a binomial coefficient is indexed by a pair of integers n ≥ k ≥ 0.
     * It is the coefficient of the xk term in the polynomial expansion of the binomial power (1 + x)n.
     *
     * @param int $n = Number of turns left.
     * @param int $k = Number of good cards needed to success.
     * @return int result
     */
    private function binomial_coeficient($n, $k){ //Calculate the binomial coeficient.
        $result = 1;
        for($i = 1; $i <= $k; $i++) {
        $result *= ($n + 1 - $i) / $i;
        }
        return $result;
    }

    /**
     * Calculate the probability to draw exactly the number of cards needed.
     *
     * @param int $N = Total number of cards in stack.
     * @param int $m = Number of good cards in stack to success.
     * @param int $n = Number of turns left.
     * @param int $k = Number of good cards needed to success.
     * @return int result
     */
    private function equal($N, $m, $n, $k) { // Probability to draw exactly the number of cards needed.
            
        return $this->binomial_coeficient($m, $k) * $this->binomial_coeficient($N - $m, $n - $k) / $this->binomial_coeficient($N, $n);
    }
    
    /**
     * Calculate the probability to get all the needed cards for the declaration in hand before the round's hand.
     *
     * @param int $N = Total number of cards in stack.
     * @param int $m = Number of good cards in stack to success.
     * @param int $k = Number of good cards needed to success.
     * @return int result
     */
    private function probability_gain($N, $m, $k)
    {
        $n = $N / 2; // As there are two players, two cards are drawn each turn.

        $greater = 0; // Probability to draw more good cards than needed.
        for($i = $n; $i > $k; $i--) {
            $greater += $this->equal($N, $m, $n, $i);
        }
        if ($k === 0) { // Of course if the player already have all the needed cards in hand, probability is 100%.
            return 1;
        } else {
            return $this->equal($N, $m, $n, $k) + $greater;
        }
    }


    /**
     * Undocumented function
     *
     * @param array $stack Stack's array containing all the cards left to be drawn.
     * @param array $e array containing the missing cards rank numbers for 250.
     * @param array $current_card The card currently evaluated.
     * @return int result.
     */
    private function probability_250_gain($stack, $e, $current_card) {
        
        $proba = 1;
        if (sizeof($e) === 0) {
            return $proba;
        }
        for ($i = 4; $i < 9; $i++) {
            if (in_array($i, $e)) {
                $m = sizeof($good_cards_available_in_stack = array_values(array_filter($stack, function($card) use ($current_card, $i) { // Count the corresponding cards in stack.
                    return ($card['rank'] === $i) && ($card['suit'] === $current_card['suit']);
                })));

                $proba = $this->probability_gain(sizeof($stack), $m, 1); // For each missing card, proba decrease.
            }
        }
        return $proba;
    }
    
    // =============================================== PRE FUNCTION ===============================================

    /**
     * Calculate the part of the card's value attributable to a four's declaration.
     *
     * @param array $current_card The card currently evaluated.
     * @param array $stack Stack's array containing all the cards left to be drawn.
     * @param int $number_needed_for_gain number of the missing cards to be able to declare.
     * @return int result.
     */
    private function four_value($current_card, $stack, $number_needed_for_gain)
    {
        $good_available_cards_in_stack = array_values(array_filter($stack, function($card) use ($current_card) {
            return $card['rank'] === $current_card['rank'];
        }));

        if ($current_card['rank'] < 3 or $current_card['rank'] > 8 or $current_card['rank'] === 7) {
            return "error this fonction can be used only for jack, queen, king or ace.";
        } else if ($current_card['rank'] === 4) {
            return 40 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), $number_needed_for_gain);
        } else if ($current_card['rank'] === 5) {
            return 60 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), $number_needed_for_gain);
        } else if ($current_card['rank'] === 6) {
            return 80 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), $number_needed_for_gain);
        } else {
            return 100 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), $number_needed_for_gain);
        }
    }

    /**
     * Calculate the part of the card's value attributable to a couple's declaration.
     *
     * @param array $current_card The card currently evaluated.
     * @param array $stack Stack's array containing all the cards left to be drawn.
     * @param int $number_needed_for_gain number of the missing cards to be able to declare.
     * @param string $suit_trump Name of the trump's suit.
     * @return int result.
     */
    private function couple_value($current_card, $stack, $number_needed_for_gain, $suit_trump)
    {
        $good_available_cards_in_stack;
    
        if ((!$current_card['rank'] === 5) || (!$current_card['rank'] === 6)) {
            echo 'error this function can be used only for a queen or a king.';
        } else if ($current_card['rank'] === 5) {
            $good_available_cards_in_stack = array_values(array_filter($stack, function($card) use ($current_card) {
                return ($card['rank'] === 6) and ($card['suit'] ===$current_card['suit']);
            }));

        } else { // The only possibility now is current_card['rank'] === 6
            $good_available_cards_in_stack = array_values(array_filter($stack, function($card) use ($current_card) {
                return ($card['rank'] === 5) and ($card['suit'] ===$current_card['suit']);
            }));
        }
        if ($current_card['suit'] === $suit_trump) {
            $result = 40 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), $number_needed_for_gain);
        } else {
            $result = 20 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), $number_needed_for_gain);
        }

        return $result;
    }

    /**
     * Calculate the part of the card's value attributable to a little brezin's declaration.
     *
     * @param array $current_card The card currently evaluated.
     * @param array $stack Stack's array containing all the cards left to be drawn.
     * @return int result.
     */
    private function little_bresin_value($current_card, $stack)
    {
        $good_available_cards_in_stack;
        if (($current_card['rank'] !== 4) and ($current_card['rank'] !== 5)) {
            return "error this function can be used only for jack or queen.";
        } else if ($current_card['rank'] === 4) {
            $good_available_cards_in_stack = array_values(array_filter($stack, function($card) {
                return $card['rank'] === 5 and $card['suit'] === "diamond";
            }));
        } else if ($current_card['rank'] === 5) {
            $good_available_cards_in_stack = array_values(array_filter($stack, function($card) {
                return $card['rank'] === 4 and $card['suit'] === "spade";
            }));
        }
    
        return 40 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), 1);
    }

    /**
     * Calculate the part of the card's value attributable to a brezin's declaration.
     *
     * @param array $current_card The card currently evaluated.
     * @param array $stack Stack's array containing all the cards left to be drawn.
     * @param int $number_needed_for_gain number of the missing cards to be able to declare.
     * @return int result.
     */
    private function bresin_value($current_card, $stack, $number_needed_for_gain)
    {
        $good_available_cards_in_stack = [];
        $queen_diamonds = array_values(array_filter($stack, function($card) {
            return ($card['rank'] === 5) and ($card['suit'] === 'diamond');
        }));
        $jack_spades = array_values(array_filter($stack, function($card) {
            return ($card['rank'] === 4) and ($card['suit'] === 'spade');
        }));

        array_push($good_available_cards_in_stack, ...$queen_diamonds);
        array_push($good_available_cards_in_stack, ...$jack_spades);

        if ((!$current_card['rank'] === 4) or (!$current_card['rank'] === 5)) {
            return 'error this function can be used only for a jack or a queen.';
        } else {
            return 500 * $this->probability_gain(sizeof($stack), sizeof($good_available_cards_in_stack), $number_needed_for_gain);
        }
    }

    /**
     * Calculate the part of the card's value attributable to a two fifty's declaration.
     *
     * @param array $current_card The card currently evaluated.
     * @param array $stack Stack's array containing all the cards left to be drawn.
     * @param array $hand_computer Array containing the computer's cards.
     * @param string $suit_trump Name of the trump's suit.
     * @return int result.
     */
    private function two_fifty_value($current_card, $stack, $hand_computer, $suit_trump)
    {
        $two_fifty_cards_in_hand;
        $rank_cards_in_hand_for_two_fifty = [];
        $rank_cards_missing_for_two_fifty = [];

        $condition = array_values(array_filter($hand_computer, function($card) use ($current_card) {
            return $card['rank'] === $current_card['rank'];
        })); // Constant created to check if a condition is true.

        if ($current_card['rank'] < 4 || $current_card['suit'] !== $suit_trump) {
            return 'error this function can be used only for jack, queen, king, ten and one which has the trump suit.';
        } else if (sizeof($condition) === 2) {
            return 0;
        } else {
            $two_fifty_cards_in_hand = array_values(array_filter($hand_computer, function($card) use ($suit_trump) {
                return $card['rank'] > 3 and $card['suit'] === $suit_trump;
            }));
            foreach ($two_fifty_cards_in_hand as $card) {
                array_push($rank_cards_in_hand_for_two_fifty, $card['rank']);
            };
            for ($index = 4; $index < 9; $index++) {
                !in_array($index, $rank_cards_in_hand_for_two_fifty) and !in_array($index, $rank_cards_missing_for_two_fifty) &&
                array_push($rank_cards_missing_for_two_fifty, $index);
            }

            $proba = 250 * $this->probability_250_gain($stack, $rank_cards_missing_for_two_fifty, $current_card);
            return $proba;
        }
    }

    // =============================================== MAIN FUNCTION ===============================================

    /**
     * Add a value's rate to the indicated $current_card.
     * Add the gain of each potential declaration this card could be used for, multiplicate by the probability to be able to get the needed cards to declare.
     * Add the score rate to the final value.
     *
     * @param array $current_card The card currently evaluated.
     * @param array $stack Stack's array containing all the cards left to be drawn.
     * @param array $hand_computer Array containing the computer's cards.
     * @param array $trump The trump's card.
     * @return int result.
     */
    public function valueCard(array $current_card, array $stack, array $hand_computer, array $trump)
    {
        $value = 0;
        $number_needed_for_gain;
        $ten = 0;
        $couple = 0;
        $little_bresin = 0;
        $four = 0;
        $brézin = 0;
        $two_fifty = 0;
        $trump_value = 0;
        $seven_trump = 0;
        
        if ($current_card['rank'] === 7) { // 10 cards value (10 points if it's in the player's pli at the end of the game).
            $ten = 10;
    
        }
        
        if ($current_card['rank'] > 3) {
            if ($current_card['rank'] !== 7) { // fours value for jack, queen, king and ace.
                $same_rank = array_values(array_filter($hand_computer, function($card) use ($current_card) {
                    return $card['rank'] === $current_card['rank'];
                }));
                $number_needed_for_gain = 4 - sizeof($same_rank);
                $four = $this->four_value($current_card, $stack, $number_needed_for_gain);
            }
            
    
            if (($current_card['rank'] === 5) or ($current_card['rank'] === 6)) { // Add the couple value.
                $kings = array_values(array_filter($hand_computer, function($card) use ($current_card) {
                    return ($card['rank'] === 6) and ($card['suit'] === $current_card['suit']);
                }));
                $queens = array_values(array_filter($hand_computer, function($card) use ($current_card) {
                    return ($card['rank'] === 5) and ($card['suit'] === $current_card['suit']);
                }));
                if ($current_card['rank'] === 5 and sizeof($kings) > 0) {
                    $number_needed_for_gain = 0;
                }elseif (($current_card['rank'] === 6) and (sizeof($queens) > 0)) {
                    $number_needed_for_gain = 0;
                } else {
                    $number_needed_for_gain = 1;
                }
    
                $couple = $this->couple_value($current_card, $stack, $number_needed_for_gain, $trump['suit']);
    
            } else if ($current_card['rank'] === 8) { // Add ace value (10 points if it's in the player's pli at the end of the game).
                $ten = 10;
            }
    
            if ((($current_card['rank'] === 4) and ($current_card['suit'] === 'spade')) or (($current_card['rank'] === 5) and ($current_card['suit'] === 'diamond'))) { // Add Bresin and little brézin's value.
                $little_bresin = $this->little_bresin_value($current_card, $stack);
        
                $cards_bresin = array_values(array_filter($hand_computer, function($card) {
                    return ($card['rank'] === 4) and ($card['suit'] === 'spade') and ($this->is_not_declared_for($card, 'brézin')) or
                            ($card['rank'] === 5) and ($card['suit'] === 'diamond') and ($this->is_not_declared_for($card, 'brézin'));
                }));
                $number_needed_for_gain = 4 - sizeof($cards_bresin);
    
                $brézin = $this->bresin_value($current_card, $stack, $number_needed_for_gain);
            } 
            
            if ($current_card['suit'] === $trump['suit']) { // Add trump's value + 250's value.
                $two_fifty = $this->two_fifty_value($current_card, $stack, $hand_computer, $trump['suit']);
            }
        }
        
        if ($current_card['suit'] === $trump['suit']) { //Add trump's value for small cards + 7 of trump's value.
    
            $trump_value = 10;
    
            if (($current_card['rank'] === 1) and ($trump['rank'] !== 1)) {
                if ($trump['rank'] === 4 and $trump['suit'] === "spade" or $trump['rank'] === 5 and $trump['suit'] === "diamond") {
                    $trump_value = 400;
                } else if ($trump['rank'] > 3) {
                    $trump_value = 200;
                } 
            }
        }
    
        $value = $ten 
                    + $couple 
                    + $little_bresin 
                    + $four 
                    + $brézin 
                    + $two_fifty 
                    + $trump_value 
                    + $seven_trump 
                    + $current_card['rank']; // Add some points to rank depending on the value of the card to win a pli.

        $current_card['value'] = $value;
        ($couple + $little_bresin + $four + $brézin + $two_fifty + $seven_trump) > 0 ? $current_card['declarable'] = true : $current_card['declarable'] = false;

        return $current_card;
    }
}