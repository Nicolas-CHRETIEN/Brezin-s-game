<?php

namespace App\GameClasses\Declarations;

use App\Entity\Situation;
use App\GameClasses\ToolsGame\ToolsGame;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;

class Declarations extends AbstractType
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
            // Check if the card contains an element 'declared".
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
            // Check if the card verify the indicated condition.
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
        if (!$this->is_declared($card)) { // First of all check if the array "card" contains an "declared" element (if the card has been used for any declaration yet).
            return true;
        } else { // Then the card contains a "declared" element which is an array.
            if ($this->array_any($card['declared'], function($declaration) use ($rank_declaration) {
                return $declaration['rank'] === $rank_declaration;
            })) { // If the "declared" array of the card there is a declaration with a rank corresponding to the one indicated as a parameter, return false. It means the card as been declared for the declaration.
                return false;
            } else { // Otherwise return true.
                return true;
            }
        }
    }

    /**
     * Convert an array of cards into an array containing only the numbers of the cards instead of the cards themselves.
     * Pick up the card's number for each card in the array and create an array with all the numbers in the same order.
     *
     * @param array $array_of_cards the array containing the cards I want to convert into numbers.
     * @return array An array containing the card's numbers instead of the cards themselves.
     */
    private function return_card_number(array $array_of_cards)
    {
        $result = [];
        foreach ($array_of_cards as $card) {
            array_push($result, $card['number']);
        }
        return $result;
    }

    /**
     * Filter the cards of an array ("$cards_couple" indicated in parameters) to return only the cards which can be used for a couple's declarartion.
     *
     * @param array $cards_couple The cards to be filtered to return only thoose which can be used for a couple's declaration.
     * @return array an array with the selected cards.
     */
    private function selected_cards_couple($cards_couple)
    {
        $selected_cards;
        if ($this->array_any($cards_couple, function($card) use ($cards_couple) {
            return !$this->is_declared($card) or sizeof($cards_couple) < 3;
        })) {
            $queens = array_filter($cards_couple, function($card) {
                return $card['rank'] === 5;
            });
            $kings = array_filter($cards_couple, function($card) {
                return $card['rank'] === 6;
            });
    
            $selected_cards = [$queens[0], $kings[0]];
        } else {
            $not_used_cards = array_filter($cards_couple, function($card) {
                return !$this->is_declared($card);
            })[0];
            $other_card_rank_number = $not_used_cards['rank'] === 5 ? 6 : 5; // If the not used card is a jack, indiquate the rank of a queen. Else, a jack rank.
            $otherCard = array_filter($cards_couple, function($card) use ($other_card_rank_number) {
                return $card['rank'] === $other_card_rank_number;
            })[0]; // Select the first card of the other rank than the not used card.
            array_push($selected_cards, $not_used_cards, $otherCard);
        }
        return $selected_cards;
    }

    /**
     * Check if there are "$hand" indicated in parameters, the cards needed to declare the corresponding declaration.
     * Return the corresponding declaration on the shape of an array if yes.
     * This is done for each of the four suits.
     * If the current suit is the trump's suit, the declaration will be a couple of trump.
     *
     * @param array $hand the hand of cards. An array with the cards which can be used.
     * @param string $suit_trump the suit of the trump card (la couleur de l'atout in french).
     * @return array An array with the information of the declaration.
     */
    private function couples($hand, $suit_trump)
    {
        $declaration = [];
        $existing_suits = ["diamond", "spade", "heart", "club"];

        forEach($existing_suits as $suit) {

            $queen = array_values(array_filter($hand, function($card) use ($suit) {
                return (($card['suit'] === $suit) and ($card['rank'] === 5) and $this->is_not_declared_for($card, 'couple'));
            }));

            $king = array_values(array_filter($hand, function($card) use ($suit) {
                return (($card['suit'] === $suit) and ($card['rank'] === 6) and $this->is_not_declared_for($card, 'couple'));
            }));
            

            if ((sizeof($queen) > 0) and (sizeof($king) > 0)) {
                if ($suit === 'diamond') {
                    $french_suit_name = 'carreau';
                } elseif ($suit === 'heart') {
                    $french_suit_name = 'coeur';
                } elseif ($suit === 'spade') {
                    $french_suit_name = 'pique';
                } else {
                    $french_suit_name = 'trèfle';
                }
                $suit === $suit_trump ?
                array_push($declaration, [
                    'name' => "trump's couple",
                    'frenchName' => "couple d'atout",
                    'rank' => 'couple',
                    'cards' => $this->return_card_number([$queen[0], $king[0]]),
                    'priority' => 5,
                    'gain' => 40
                ]) :
                array_push($declaration, [
                    'name' => $suit . "'s couple",
                    'frenchName' => 'couple de ' . $french_suit_name,
                    'rank' => 'couple',
                    'cards' => $this->return_card_number([$queen[0], $king[0]]),
                    'priority' => 6,
                    'gain' => 20
                ]);
            }
        }
        return $declaration;
    }

    /**
     * Filter the cards of an array ("$cards_four" indicated in parameters) to return only the cards which can be used for a four's declarartion.
     *
     * @param array $cards_four The cards to be filtered to return only thoose which can be used for a four's declaration.
     * @return array an array with the selected cards.
     */
    private function selected_cards_four($cards_four)
    {
        $sorted_array = [];
        $declared_for_four = array_filter($cards_four, function($card) {
            return $this->is_not_declared_for($card, 'four');
        });
        $declared_for_other_declaration = array_filter($cards_four, function($card) {
            return $this->is_not_declared_for($card, 'four');
        });
        $not_declared_yet = array_filter($cards_four, function($card) {
            return !$this->is_declared($card);
        });
        array_push($sorted_array, ...$declared_for_four, ...$declared_for_other_declaration, ...$not_declared_yet); // the cards are now sorted depending on whiwh must be used first.

        // If there is a card already declared for this four, it's on first position.
        // If some cards have been declared for an other thig, it's just after.
        // At least one card has not been declared, as this is the condition to declare anything. It's at the end.

        $selected_cards = [$sorted_array[0], $sorted_array[1], $sorted_array[2], $sorted_array[sizeof($sorted_array) - 1]];

        return $selected_cards;
    }

    /**
     * Check for each four (jacks queens kings and aces) if the indicated cards permit to declare the four's declaration.
     * If it's the case, the function creates an array with the information of the declaration.
     *
     * @param array $hand the selected cards filtered in selected_cards_four().
     * @return array An array with the declaration's information.
     */
    private function fours($hand)
    {
        $declaration = [];

        for ($index = 4; $index < 9; $index++) {
            $four_cards = array_filter($hand, function($card) use ($index) {
                return ($card['rank'] === $index);
            });

            $four_cards_valid_for_declaration = array_filter($four_cards, function($card) {
                return $this->is_not_declared_for($card, 'four');
            });
            

            // Index should not be equal to 7 because four of tens is not valid.
            // You can use ONE four card already used for a four declaration to declare the same four a second time. 
            // So there must be at least 4 cards valid for this four and at least 3 cards not already used for a same four declaration:
            if (($index !== 7) and (sizeof($four_cards) > 3) and (sizeof($four_cards_valid_for_declaration) > 2) and $this->array_any($four_cards_valid_for_declaration, function($card) {
                return !$this->is_declared($card);
            })) {
                // Create an array with at most 1 card already declared for four declaration (one only can be re used):
                if (sizeof($four_cards_valid_for_declaration) < 4) { // Add the already used for four's card to the valid cards array.
                    $fourth_card = array_values(array_filter($four_cards, function($card) use ($four_cards_valid_for_declaration) {
                        return !$this->is_not_declared_for($card, 'four');
                    }))[0];
                    array_push($four_cards_valid_for_declaration, $fourth_card);
                }
                if ($index === 4) {
                    $name = "jack's four";
                    $french_name = 'carré de valets';
                    $gain = 40;
                } elseif ($index === 5) {
                    $name = "queen's four";
                    $french_name = 'carré de dames';
                    $gain = 60;
                } elseif ($index === 6) {
                    $name = "king's four";
                    $french_name = 'carré de rois';
                    $gain = 80;
                } else {
                    $name = "ace's four";
                    $french_name = "carré d'as";
                    $gain = 100;
                }
                
                $selected_cards = $this->selected_cards_four($four_cards_valid_for_declaration);
                array_push($declaration, [
                    'name' => $name,
                    'frenchName' => $french_name,
                    'rank' => 'four',
                    'cards' => $this->return_card_number($selected_cards),
                    'priority' => 4 + 1 - $index, // The more the score is important, the more important the priority should be.
                    'gain' => $gain
                ]);
            }
        }
        return $declaration;
    }

    /**
     * Filter the cards of an array ("$cards_little_bresin" indicated in parameters) to return only the cards which can be used for a brezin's or a little brezin's declarartion.
     *
     * @param array $cards_little_bresin The cards to be filtered to return only thoose which can be used for a brezin's or a little brezin's declaration.
     * @return array an array with the selected cards.
     */
    private function selected_cards_little_bresin($cards_little_bresin)
    {
        $selectedCards = [];

        if ($this->array_any($cards_little_bresin, function($card) use ($cards_little_bresin) {
            return !$this->is_declared($card) or sizeof($cards_little_bresin) < 3;
        })) {
            $jacks = array_values(array_filter($cards_little_bresin, function($card) {
                    return $card['rank'] === 4;
                }));
            $queens = array_values(array_filter($cards_little_bresin, function($card) {
                    return $card['rank'] === 5;
                }));

            $selected_cards = [$jacks[0], $queens[0]];
        } else {
            $not_used_cards = array_filter($cards_little_bresin, function($card) {
                return !$this->is_declared($card);
            });
            $other_card_rank_number = $not_used_cards['rank'] === 4 ? 5 : 4; // If the not used card is a jack, indiquate the rank of a queen. Else, a jack rank.
            $otherCard = array_filter($cards_little_bresin, function($card) use ($other_card_rank_number) {
                return $card['rank'] === $other_card_rank_number; // Select the first card of the other rank than the not used card.
            })[0];
            array_push($selected_cards, $not_used_cards, $otherCard);
        }

        return $selected_cards;
    }

    /**
     * Check for each declaration (brezin and little brezin) if the cards indicated with $hand (the cards filtered by selected_cards_little_bresin()) permit to declare one of the declaration.
     * if a declaration can be made, return an array with the declarations.
     * 
     * @param array $hand The cards filtered with selected_cards_little_bresin().
     * @return array The brezin's and the little brezin's declarations.
     */
    private function brézin($hand)
    {
        $declaration = [];

        $cards_bresin = array_filter($hand, function($card) {
            return ($card['rank'] === 4) and ($card['suit'] === 'spade') and ($this->is_not_declared_for($card, 'brézin')) or (($card['rank'] === 5) and ($card['suit'] === 'diamond') and ($this->is_not_declared_for($card, 'brézin')));
            });
    
        $cards_little_bresin = array_filter($hand, function($card) {
            return (($card['rank'] === 4) and ($card['suit'] === 'spade') and ($this->is_not_declared_for($card, 'little brézin')) and ($this->is_not_declared_for($card, 'brézin'))) or (($card['rank'] === 5) and ($card['suit'] === 'diamond') and ($this->is_not_declared_for($card, 'little brézin')) and ($this->is_not_declared_for($card, 'brézin')));
        });
            
        // Check if there is any brézin:
        sizeof($cards_bresin) === 4 and sizeof(array_filter($cards_bresin, function($card) {
            return $this->is_declared($card);
        })) !== 4 && // The 4 cards are needed and they also must not all been declared yet.
        array_push($declaration, [
            'name' => 'brézin',
            'frenchName' => 'brézin',
            'rank' => 'brézin',
            'cards' => $this->return_card_number($cards_bresin),
            'priority' => 2,
            'gain' => 500
        ]);
    
    
            
        if (($this->array_any($cards_little_bresin, function($card) {
            return $card['rank'] === 4;
        })) and ($this->array_any($cards_little_bresin, function($card) {
            return $card['rank'] === 5;
        })) and (sizeof($cards_bresin) < 4) and ($this->array_any($cards_little_bresin, function($card) {
            return !$this->is_declared($card);
        }))) { // Bresin's cards < 4 because if all the cards are in hand only brézin is possible. at least one of the cards used must not be declared yet.
            $selected_cards = $this->selected_cards_little_bresin($cards_little_bresin);
            array_push($declaration, [
                'name' => 'little brézin',
                'frenchName' => 'demi brézin',
                'rank' => 'little brézin',
                'cards' => $this->return_card_number($selected_cards),
                'priority' => 5,
                'gain' => 40
            ]);
        }
    
        return $declaration;
    }

    /**
     * Filter the cards of an array ("$cards_two_fifty" indicated in parameters) to return only the cards which can be used for a two fifty's declarartion.
     *
     * @param array $cards_two_fifty The cards to be filtered to return only thoose which can be used for a two fifty's declaration.
     * @return array an array with the selected cards.
     */
    private function selected_card_two_fifty($cards_two_fifty)
    {
        $already_declared_cards = array_filter($cards_two_fifty, function($card) {
            return $this->is_declared($card);
        });
        $not_declared_yet_cards = array_filter($cards_two_fifty, function($card) {
            return !$this->is_declared($card);
        });
        $sorted_array = [...$already_declared_cards, ...$not_declared_yet_cards]; // In this array, the cards already declared, which must be used first, are in first position.
    
        $jacks = array_values(array_filter($sorted_array, function($card) {
            return $card['rank'] === 4;
        }));
        $queens = array_values(array_filter($sorted_array, function($card) {
            return $card['rank'] === 5;
        }));
        $kings = array_values(array_filter($sorted_array, function($card) {
            return $card['rank'] === 6;
        }));
        $tens = array_values(array_filter($sorted_array, function($card) {
            return $card['rank'] === 7;
        }));
        $aces = array_values(array_filter($sorted_array, function($card) {
            return $card['rank'] === 8;
        }));
        $selected_cards = [$jacks[0], $queens[0], $kings[0], $tens[0], $aces[0]];
    
        return $selected_cards;
    }

    /**
     * Check if the indicated cards in "$hand" are all there to permit a 250's declaration.
     * If all the needed cards are gathered, create an array with the information of the declarations.
     *
     * @param array $hand The cards which can be used for a 250's declaration.
     * @param string $suit_trump the suit of the trump's card.
     * @return array An array with the declaration's information.
     */
    private function two_fifty($hand, $suit_trump)
    {
        $declaration = [];

        $jack_trump = array_values(array_filter($hand, function($card) use ($suit_trump) {
            return (($card['suit'] === $suit_trump) and ($card['rank'] === 4) and ($this->is_not_declared_for($card, 'two fifty')));
        }));
    
        $queen_trump = array_values(array_filter($hand, function($card) use ($suit_trump) {
            return (($card['suit'] === $suit_trump) and ($card['rank'] === 5) and ($this->is_not_declared_for($card, 'two fifty')));
        }));
    
        $king_trump = array_values(array_filter($hand, function($card) use ($suit_trump) {
            return (($card['suit'] === $suit_trump) and ($card['rank'] === 6) and ($this->is_not_declared_for($card, 'two fifty')));
        }));
    
        $ten_trump = array_values(array_filter($hand, function($card) use ($suit_trump) {
            return (($card['suit'] === $suit_trump) and ($card['rank'] === 7) and ($this->is_not_declared_for($card, 'two fifty')));
        }));
    
        $ace_trump = array_values(array_filter($hand, function($card) use ($suit_trump) {
            return (($card['suit'] === $suit_trump) and ($card['rank'] === 8) and ($this->is_not_declared_for($card, 'two fifty')));
        }));
    
        $good_cards = [...$jack_trump, ...$queen_trump, ...$king_trump, ...$ten_trump, ...$ace_trump];
        
        // Check if there is any 250:
        if ((sizeof($jack_trump) > 0) and (sizeof($queen_trump) > 0) and (sizeof($king_trump) > 0) and (sizeof($ten_trump) > 0) and (sizeof($ace_trump) > 0)) {
            
            $selected_good_cards = $this->selected_card_two_fifty($good_cards);
            array_push($declaration, [
                'name' => `two fifty`,
                'frenchName' => 'deux cent cinquante',
                'rank' => 'two fifty',
                'cards' => $this->return_card_number($selected_good_cards),
                'priority' => 3,
                'gain' => 250
            ]);
        }
        return $declaration;
    }
        
    /**
     * Check if there is any undeclared seven of trump in the indicated cards.
     * If yes, this function create an array with the information of the declaration.
     *
     * @param array $hand an array of cards
     * @param string $suit_trump the trump's suit name.
     * @return array An array with the 7's trump declaration(s).
     */
    private function seven_trump($hand, $suit_trump)
    {
        $seven_trump_not_declared = array_filter($hand, function($card) use ($suit_trump) {
            return $card['rank'] === 1 and $card['suit'] === $suit_trump and !$this->is_declared($card);
        }); // condition for seven_trump.
        $declaration = [];
    
         // Check if there is any seven of trump:
    
         if (sizeof($seven_trump_not_declared) > 0) { // Check seven trump.
            $good_cards = array_values(array_filter($hand, function($card) use ($suit_trump) {
                return ($card['suit'] === $suit_trump) and ($card['rank'] === 1) and ($this->is_not_declared_for($card, "trump's seven"));
            }));

    
            array_push($declaration, [
                'name' => "trump's seven",
                'frenchName' => "sept d'atout",
                'rank' => "trump's seven",
                'cards' => $this->return_card_number($good_cards)[0],
                'priority' => 7,
                'gain' => 10
            ]);
        }
        return $declaration;
    }
        



    
    /**
     * Construct and return an array with all the possible declarations it is possible to do with the hand indicated as a parameter.
     *
     * @param array $hand an array of cards
     * @param string $suit_trump the trump's suit name.
     * @return void
     */
    public function possibleDeclarations(array $hand, $suit_trump)
    {
        $possible_declarations = [];

        // Each function return an array with one or many objects (possible declarations).
        $couples_declarations = $this->couples($hand, $suit_trump);
        $seven_trump_declaration = $this->seven_trump($hand, $suit_trump);
        $fours_declarations = $this->fours($hand);
        $bresin_declarations = $this->brézin($hand);
        $two_fifty_declaration = $this->two_fifty($hand, $suit_trump);

        array_push($possible_declarations, ...$couples_declarations, ...$seven_trump_declaration, ...$fours_declarations, ...$bresin_declarations, ...$two_fifty_declaration);

        return $possible_declarations;
    }





    /**
     * This function reconize the cards in the hand used to make the declaration.
     * If the card hasn't been declared yet, it creates a "declared" element in wich there is the declaration's information.
     * If the card has already been declared, it add a new element in the "declared" array.
     *
     * @param array $hand An array of cards
     * @param array $declaration An array containing the declaration's information.
     * @return array An array containing the cards added in parameters, with a "declared" element containing the declaration's information.
     */
    private function find_with_number_and_add_declaration($hand, $declaration)
    {
        $new_hand = [];
        foreach ($hand as $card) {
            if (in_array($card['number'], $declaration['cards'])) {
                if (!$this->is_declared($card)) { // Declare the object declared for the corresponding card as an array if the card hasn't been declared yet.
                    $card['declared'] = [$declaration];
                } else {
                    array_push($card['declared'], $declaration); // Add the new declaration to the card's declared object.
                }
            }
            array_push($new_hand, $card);
        }
        
        return $new_hand;
    }



    /**
     * Calculate the possible declarations available for the computer.
     * Sort them according to their priority.
     * Play the priority one.
     * Register the new $Situation.
     *
     * @param Situation $Situation
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function computerChooseDeclaration(Situation $Situation, EntityManagerInterface $Manager)
    {
        $data = $Situation->getSituation();
        $Tools = new ToolsGame;

        $declarations = array_values($this->possibleDeclarations($data['handComputer'], $data['trump']['suit']));
        $declarations = $Tools->array_sort($declarations, 'priority', 1);
        $data['score']['lastDeclaration'] = [$declarations[0]];
        $this->computerDeclare($declarations[0], $data, $Situation, $Manager);
        $Manager->persist($Situation);
        $Manager->flush();
    }

    /**
     * Check if the declaration is a seven of trump.
     * exchange the declaration's card with the trump card if it is the case.
     * Add the declaration to the card(s) used in order to remember that/thoose card(s) has already been used for this declaration.
     * Register the game's evolution.
     *
     * @param array $declaration The declaration to declare.
     * @param array $data $Situation's main properties. (I use $data here, because $Situation is not persisted)
     * @param Situation $Situation
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function computerDeclare($declaration, $data, Situation $Situation, EntityManagerInterface $Manager) 
    {
        $Tools = new ToolsGame;

        if ($declaration['rank'] !== "trump's seven") { // If it's trump's seven declaration, do not add declared object to the card as it's gonna become the new trump which must not be declared.
          
            $data['handComputer'] = $this->find_with_number_and_add_declaration($data['handComputer'], $declaration); // Add the declared object to the cards.
        } else {
            foreach ($data['handComputer'] as $key => $card) $card['number'] === $declaration['cards'] && $index = $key; // Find the 7 of trump card's index in the player's hand.
            $declaration['cards'] = [$data['trump']['number']];
            
            $data['trump']['declared'] = [$declaration]; // Add the declared object to the card.
            $old_trump = $data['trump'];
            $data['trump'] = $data['handComputer'][$index];
            array_splice($data['handComputer'], $index, 1); // Replace it with trump card.
            array_push($data['handComputer'], $old_trump);
        }
        
        $data['score']['player2'] += $declaration['gain']; // Update the score
        array_push($data['score']['declarationsListP2'], $declaration); // Add the declaration in score.
        $data['declarationsAvailablePlayer'] = $this->possibleDeclarations($data['handComputer'], $data['trump']['suit']);

        $Situation->setSituation($data);
        $Manager->persist($Situation);
        $Manager->flush();
    }

    /**
     * Check if the declaration is a seven of trump.
     * exchange the declaration's card with the trump card if it is the case.
     * Add the declaration to the card(s) used in order to remember that/thoose card(s) has already been used for this declaration.
     * Register the game's evolution.
     *
     * @param array $declaration The declaration to declare.
     * @param array $data $Situation's main properties. (I use $data here, because $Situation is not persisted)
     * @param Situation $Situation
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function playerDeclare($declaration, Situation $Situation, EntityManagerInterface $Manager) 
    {
        $data = $Situation->getSituation();

        if ($declaration['rank'] !== "trump's seven") { // If it's trump's seven declaration, do not add declared object to the card as it's gonna become the new trump which must not be declared.
            
            $data['handPlayer'] = $this->find_with_number_and_add_declaration($data['handPlayer'], $declaration); // Add the declared object to the cards.
        } else {
            foreach ($data['handPlayer'] as $key => $card) $card['number'] === $declaration['cards'] && $index = $key; // Find the 7 of trump card's index in the player's hand.
            $declaration['cards'] = [$data['trump']['number']];
            
            $data['trump']['declared'] = [$declaration]; // Add the declared object to the card.
            $old_trump = $data['trump'];
            $data['trump'] = $data['handPlayer'][$index];
            array_splice($data['handPlayer'], $index, 1); // Replace it with trump card.
            array_push($data['handPlayer'], $old_trump);
        }
        
        $data['score']['player1'] += $declaration['gain']; // Update the score
        array_push($data['score']['declarationsListP1'], $declaration); // Add the declaration in score.
        $data['declarationsAvailablePlayer'] = $this->possibleDeclarations($data['handPlayer'], $data['trump']['suit']);

        $declaration['rank'] === "trump's seven" and sizeof($data['declarationsAvailablePlayer']) === 0 ? $data['stage'] = 'declare':  $data['stage'] = 'draw';

        $Situation->setSituation($data);
        $Manager->persist($Situation);
        $Manager->flush();
    }
}