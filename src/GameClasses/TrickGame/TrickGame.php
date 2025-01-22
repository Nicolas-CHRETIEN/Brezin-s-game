<?php

namespace App\GameClasses\TrickGame;

use App\Entity\Situation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\GameClasses\Declarations\Declarations;

class TrickGame extends AbstractType
{

    /**
     * According to the cards played by each player, to who played first and to the trump suit, determine who is winning the trick.
     * Add the played card to the winner's trick.
     * Save the $Situation.
     *
     * @param Situation $Situation
     * @param Declarations $Declarations
     * @param EntityManagerInterface $Manager
     * @return void
     */
    public function playTrickGame(Situation $Situation, Declarations $Declarations, EntityManagerInterface $Manager)
    {
        $data = $Situation->getSituation();
    
        // Player 1 win if:
        //  - He starts to play, his card has the best score or equality and it's trump suit or the same than P2 (because even if it's score is the best, he loose if it's an other no trump suit).
        //  - P2 starts to play, but P1 has a trump card with best score, or a card with the same suit with best score.
    
        // Below are all the condition for P1 to win the trick:
    
        if (
            $data['playFirst'] === "player" and $data['playerCardPlayed']['score'] > $data['computerCardPlayed']['score'] and $data['computerCardPlayed']['suit'] === $data['playerCardPlayed']['suit'] or
            $data['playFirst'] === "player" and $data['playerCardPlayed']['score'] === $data['computerCardPlayed']['score'] and $data['computerCardPlayed']['suit'] === $data['playerCardPlayed']['suit'] or
            $data['playFirst'] === "player" and $data['computerCardPlayed']['suit'] !== $data['trump']['suit'] and $data['computerCardPlayed']['suit'] !== $data['playerCardPlayed']['suit'] or
            $data['playFirst'] === "computer" and $data['playerCardPlayed']['score'] > $data['computerCardPlayed']['score'] and $data['playerCardPlayed']['suit'] === $data['computerCardPlayed']['suit'] or
            $data['playFirst'] === "computer" and $data['computerCardPlayed']['suit'] !== $data['trump']['suit'] and $data['playerCardPlayed']['suit'] === $data['trump']['suit']
        ) {
            array_push($data['trickPlayer'], $data['playerCardPlayed'], $data['computerCardPlayed']);
            $data['playFirst'] = 'player';
            $data['declarationsAvailablePlayer'] = $Declarations->possibleDeclarations($data['handPlayer'], $data['trump']['suit']); // If player used a card necessary for a declaration to win the trick, this must be updated.
        } else {
            array_push($data['trickComputer'], $data['playerCardPlayed'], $data['computerCardPlayed']);
            $data['playFirst'] = 'computer';
            $data['declarationsAvailableComputer'] = $Declarations->possibleDeclarations($data['handComputer'], $data['trump']['suit']); // If computer used a card necessary for a declaration to win the trick, this must be updated.
        }

        
        $Situation->setSituation($data);
        $Manager->persist($Situation);
        $Manager->flush();
    }
}