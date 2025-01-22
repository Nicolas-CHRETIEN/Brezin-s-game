<?php

namespace App\GameClasses\ToolsGame;

use Symfony\Component\Form\AbstractType;

class ToolsGame extends AbstractType
{

    /**
    * Check if the array verify the condition in the secund param.
    * If the condition is respected, the function return "true".
    *
    * @param array $array The array, usualy a card.
    * @param callable $fn the callback.
    */
    public function array_any(array $array, callable $fn) {
        foreach ($array as $value) {
            if($fn($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return an array with only the card's number.
     *
     * @param array $array_of_cards
     * @return array An array with the card's number insted of the cards themselves.
     */
    public function return_card_number(array $array_of_cards)
    {
        $result = [];
        foreach ($array_of_cards as $card) {
            array_push($result, $card['number']);
        }
        return $result;
    }


    /**
     * Sort the array according to one of its element's value.
     * It's possible to sort  in ascending order or in decrasing order according to the third parameter.
     *
     * @param array $array The array with the cards to compare.
     * @param string $value_to_compare The value we want to compare the cards with.
     * @param number $order -1 if we want the card to be sorted the most important ones to the less important.
     * @return array The new sorted array.
     */
    public function array_sort(array $array, $value_to_compare, $order) {
        $array_to_sort = [];
        $array_keys = [];

        // Create an array with only the values to sort with:
        foreach ($array as $card) {
            array_push($array_to_sort, $card[$value_to_compare]);
        }

        // Sort them according to the indecated order (ascending or decreasing).
        if ($order === -1) {
            rsort($array_to_sort, SORT_NUMERIC);
        } else {
            sort($array_to_sort, SORT_NUMERIC);
        }
        
        // Find back the element corresponding to the sorted value in the array and give it the new key of the sorted value:
        foreach ($array as $key => $card) {
            $new_key = array_search($card[$value_to_compare], $array_to_sort);
            if (in_array($new_key, $array_keys)) { // To avoid two elements to have the same key, I add the old key divided by 100. 
                $new_key+= $key / 100; // I use $key as I can be sure that it's different for each card. I need to be sure that every $new_key is different.
            }
            array_push($array_keys, $new_key);
        }

        // Now that I add the good keys to the array's elements, I can sort the array with ksort:
        $new_array = array_combine($array_keys, $array);
        ksort($new_array, SORT_NUMERIC );
        return array_values($new_array);
    }
}