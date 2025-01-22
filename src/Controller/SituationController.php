<?php

namespace App\Controller;

use DateTime;
use App\Entity\Cards;
use App\Entity\Games;
use App\Entity\Users;
use App\Entity\Situation;
use PhpParser\Builder\Declaration;
use App\GameClasses\DrawInStack\Draw;
use App\GameClasses\ToolsGame\ToolsGame;
use App\GameClasses\TrickGame\TrickGame;
use Doctrine\ORM\EntityManagerInterface;
use App\GameClasses\ImportanceCards\Score;
use App\GameClasses\ImportanceCards\Value;
use App\GameClasses\ComputerPlay\ComputerPlay;
use App\GameClasses\Declarations\Declarations;
use Symfony\Component\Routing\Annotation\Route;
use App\GameClasses\ComputerPlay\ComputerPlayEndGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SituationController extends AbstractController
{
    /**
     * Init a new game according to the user's ID.
     * Find the corresponding user's Situation. If he has no situation yet, create one and init the game.
     * Save the $Situation.
     * render game's template with stage = "distribute".
     * 
     * @Route("/game/user/{id}", name="game_user_init")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function renderGameUser($id, EntityManagerInterface $Manager) {
        $User_repository = $this->getDoctrine()->getRepository(Users::class);
        $User = $User_repository->findOneById($id);
        $data = [
            'init' => true,
            'playFirst' => 'player',
            'stage' => 'choiceDifficulty',
            'trump' => null,
            'stack' => [],
            'playerCardPlayed' => null,
            'computerCardPlayed' => null,
            'handPlayer' => [],
            'handComputer' => [],
            'trickPlayer' => [],
            'trickComputer' => [],
            'declarationsAvailablePlayer' => [],
            'declarationsAvailableComputer' => [],
            'score' => [
                'player1' => 0,
                'player2' => 0,
                'declarationsListP1' => [],
                'declarationsListP2' => [],
                'round' => 0,
                'previousRounds' => []
            ],
        ];
        
        if (!$User->getSituation()) {
            $Situation = new Situation;
            $Situation->setUser($User);
        } else {
            $Situation = $User->getSituation();
        }
        
        $Situation->setSituation($data);
        $Manager->persist($Situation);
        $Manager->flush();

        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }


    /**
     * Init the game if user is not connected by creating a new userless situation.
     * Init the game.
     * Save the $Situation.
     * render game's template with stage = "distribute".
     * 
     * @Route("/game", name="game_init")
     * 
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function renderGameVisitor(EntityManagerInterface $Manager) {
        
        $Situation = new Situation;
        $data = [
            'init' => true,
            'playFirst' => 'player',
            'stage' => 'choiceDifficulty',
            'trump' => null,
            'stack' => [],
            'playerCardPlayed' => null,
            'computerCardPlayed' => null,
            'handPlayer' => [],
            'handComputer' => [],
            'trickPlayer' => [],
            'trickComputer' => [],
            'declarationsAvailablePlayer' => [],
            'declarationsAvailableComputer' => [],
            'score' => [
                'player1' => 0,
                'player2' => 0,
                'declarationsListP1' => [],
                'declarationsListP2' => [],
                'round' => 0,
                'previousRounds' => []
            ],
        ];
        
        $Situation->setSituation($data);
        $Manager->persist($Situation);
        $Manager->flush();

        $this->addFlash('warning', "Vous n'êtes pas connecté. \nLes données de cette partie ne pourront pas être sauvegardées. \nConnectez vous si vous souhaitez sauvegarder votre progression.");
        
        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }
    
    /**
     * Restart the game.
     * Init a new game thanks to the situation ID.
     * Save the $Situation.
     * render game's template with stage = "distribute".
     * 
     * @Route("/game/restart/{id}", name="game_restart")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function restartGame($id, EntityManagerInterface $Manager) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $data = [
            'init' => true,
            'playFirst' => 'player',
            'stage' => 'choiceDifficulty',
            'trump' => null,
            'stack' => [],
            'playerCardPlayed' => null,
            'computerCardPlayed' => null,
            'handPlayer' => [],
            'handComputer' => [],
            'trickPlayer' => [],
            'trickComputer' => [],
            'declarationsAvailablePlayer' => [],
            'declarationsAvailableComputer' => [],
            'score' => [
                'player1' => 0,
                'player2' => 0,
                'declarationsListP1' => [],
                'declarationsListP2' => [],
                'round' => 0,
                'previousRounds' => []
            ],
        ];
        
        $Situation->setSituation($data);
        $Manager->persist($Situation);
        $Manager->flush();
        
        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * Set difficulty and render game.
     * Set $Situation["difficulty"] thank's to $difficulty parameter.
     * 
     * @Route("/game/id/{id}/difficulty/{difficulty}", name="game_difficulty")
     * 
     * @param int $id
     * @param int $difficulty
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function choiceDifficulty($id, $difficulty, EntityManagerInterface $Manager) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'choiceDifficulty') {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        $Situation->setStage('deal')
                  ->setDifficulty($difficulty)
                  ;

        $Manager->persist($Situation);
        $Manager->flush();
            
        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * deal cards.
     * prevent bugs if the page is refreched.
     * get all the cards from DB.
     * Shuffle them and convert them into array.
     * Add a score rate to each card according to the trump's suit.
     * Deal the cards thanks to Situation->deal method.
     * Save Situation and render the game.
     * 
     * @Route("/game/deal/{id}", name="game_deal")
     *
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function deal($id, EntityManagerInterface $Manager) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $Score = new Score;
        $computerPlay = new ComputerPlay;

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'deal') {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        $Cards_repository = $this->getDoctrine()->getRepository(Cards::class);
        $Cards = $Cards_repository->findAll();

        // Shuffle the cards:
        shuffle($Cards);

        $cards = [];
        foreach ($Cards as $card) {
            $card_array = [
                'name' => $card->getName(),
                'number' => (int) $card->getNumber(),
                'suit' => $card->getSuit(),
                'rank' => (int) $card->getRank(),
                'twinCardNumber' => (int) $card->getTwinCardNumber(),
                'img' => $card->getImg()
            ];

            array_push($cards, $Score->scoreCard($card_array, $Cards[0]->getSuit())); // scoreCard return an entier card with a score element. Here I push this card in $cards.
        }
        $Situation->deal($cards);

        $Manager->persist($Situation);
        $Manager->flush();

        if ($Situation->getScore()['round']%2 === 0 and $Situation->getScore()['round'] > 0) {
            $Situation->computerDrawCard();
            $Situation->computerPlayCard($computerPlay->computerPlayFirst($Situation));
            $Situation->setStage('draw');

            $Manager->persist($Situation);
            $Manager->flush();
        }
            
        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * draw card(s).
     * prevent bugs if the page is refreched.
     * If there is only one card left in the stack, call Draw->drawLastTurn method.
     * Else if player won last trick (and so Situation["playFirst"] === "player") make both player and computer draw, beguining with the player.
     * Else the computer already drew a card, so only the player needs to draw one.
     * Save the $Situation.
     * 
     * @Route("/game/draw/{id}", name="game_draw")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function draw($id, EntityManagerInterface $Manager) {

        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $Draw = new Draw;

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'draw' and $Situation->getStage() !== 'declare') {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        if (sizeof($Situation->getStack()) === 1) {
            $Draw->drawLastTurn($Situation, $Manager);
            if ($Situation->getPlayFirst() === 'computer') {
                $Draw->playableCards($Situation, $Manager);
            }
            $Situation->setHandPlayer($Draw->undeclareCards($Situation->getHandPlayer()));
            $Situation->setHandComputer($Draw->undeclareCards($Situation->getHandComputer()));

            $Manager->persist($Situation);
            $Manager->flush();
            
        } else if ($Situation->getPlayFirst() === 'player') {
            $Situation->playerDrawCard();
            $Draw->playerSortCards($Situation, $Manager);
            $Situation->computerDrawCard();
        } else {
            $Situation->playerDrawCard();
            $Draw->playerSortCards($Situation, $Manager);
        }

        $Situation->setStage('play');

        $Manager->persist($Situation);
        $Manager->flush();

        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * declare cards.
     * Get the declaration corresponding to the $index parameter.
     * prevent bugs if the page is refreched.
     * Declare it thank's to Declaration->playerDeclare 's method.
     * Check if the declaration was a trump's seven and if there is another thing to declare. It's possible to declare another thing after a seven of trump declaration.
     * Set stage on "declare" if yes, on "draw" if not.
     * Save Situation and render the game.
     * 
     * @Route("/game/declare/{id}/declaration/{index}", name="game_declare")
     * 
     * @param int $id
     * @param int $index
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function declare($id, $index, EntityManagerInterface $Manager) {

        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $Declarations = new Declarations;

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'declare') {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        $declaration = $Situation->getDeclarationsAvailablePlayer()[$index];
        
        $Declarations->playerDeclare($declaration, $Situation, $Manager);

        if ($declaration['rank'] === "trump's seven" and sizeof($Declarations->possibleDeclarations($Situation->getHandPlayer(), $Situation->getTrump()['suit'])) > 0) {
            $Situation->setStage('declare');
        } else {
            $Situation->setStage('draw');
        }

        $Manager->persist($Situation);
        $Manager->flush();

        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * The player plays first.
     * Prevent bugs if the page is refreched.
     * Get the card selected by the player thank's to its number ($cardNumber).
     * Calculate the card selected by AI with $play->computerPlaySecond or $play->computerPlaySecondEndGame according to the stack's situation (if it's empty or not).
     * Call Situation->playerPlayCard's method.
     * Call Situation->computerPlayCard's method.
     * Call TrickGame->playTrickGame's method.
     * Set stage on trickGameResult
     * Save Situation and render result_trick_game.
     * 
     * @Route("/game/playFirst/{id}/card/{cardNumber}", name="game_playFirst")
     * 
     * @param int $id The situation ID.
     * @param int $cardNumber The card selected by the player.
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function playerPlayFirst($id, $cardNumber, EntityManagerInterface $Manager) {

        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $play = new ComputerPlay;
        $playEnd = new ComputerPlayEndGame;
        $Declarations = new Declarations;
        $TrickGame = new TrickGame;

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'play') {
            return $this->render('game/result_trick_game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        $cardNumber = (int) $cardNumber; // Be sure to have a number instead of a string.

        $cardPlayer = array_values(array_filter($Situation->getHandPlayer(), function($card) use ($cardNumber) { // Array_values is helpfull to change the keys to normal numeric starting from 0.
            return $card['number'] === $cardNumber;
        }));

       $declarations_available = $Declarations->possibleDeclarations($Situation->getHandComputer(), $Situation->getTrump()['suit']);

       $Situation->setDeclarationsAvailableComputer($declarations_available); // Set declarations available for computer to know if it needs to win.

       $cardComputer = sizeof($Situation->getStack()) > 1 ? $play->computerPlaySecond($cardPlayer[0], $Situation) : $playEnd->computerPlaySecondEndGame($cardPlayer[0], $Situation);

       $Situation->playerPlayCard($cardPlayer[0]);
       $Situation->computerPlayCard($cardComputer);
       $TrickGame->playTrickGame($Situation, $Declarations, $Manager);
       $Situation->setStage('trickGameResult');

       $Manager->persist($Situation);
       $Manager->flush();

        return $this->render('game/result_trick_game.html.twig', ['situation' => $Situation->getSituation()]);
   }

    /**
     * The player plays after the computer.
     * Prevent bugs if the page is refreched.
     * Delete unPlayable element from player's cards.
     * Get the card selected by the player thank's to $cardNumber.
     * Call Situation->playerPlayCard's method.
     * Save Situation in order to have updated informations for the folowing..
     * Call TrickGame->playTrickGame's method.
     * Save Situation and render result_trick_game.
     * 
     * @Route("/game/playSecond/{id}/card/{cardNumber}", name="game_playSecond")
     * 
     * @param int $id
     * @param int $cardNumber
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function playerPlaySecond($id, $cardNumber, EntityManagerInterface $Manager) {

        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $play = new ComputerPlay;
        $playEnd = new ComputerPlayEndGame;
        $Declarations = new Declarations;
        $TrickGame = new TrickGame;

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'play') {
            return $this->render('game/result_trick_game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        // Change the unPlayable information to let the player play any card next turn (unPlayable is an element add to the cards of the player's hand at the end of the game to forbid him to play some cards):
        $old_hand = $Situation->getHandPlayer();
        $new_hand = [];
        foreach ($old_hand as $card) {
            $card['unPlayable'] = null;
            array_push($new_hand, $card);
        }

        $cardNumber = (int) $cardNumber; // Be sure to have a number instead of a string.

        $cardPlayer = array_values(array_filter($new_hand, function($card) use ($cardNumber) { // Array_values is helpfull to change the keys to normal numeric starting from 0.
            return $card['number'] === $cardNumber;
        }));

        $Situation->setHandPlayer($new_hand);
        $Situation->playerPlayCard($cardPlayer[0]);
        $Manager->persist($Situation); // Persist and flush to pass updated $situation to playTrickGame.
        $Manager->flush();
        $TrickGame->playTrickGame($Situation, $Declarations, $Manager);
        $Situation->setStage('trickGameResult');

        $Manager->persist($Situation);
        $Manager->flush();
        
        return $this->render('game/result_trick_game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * The computer plays first.
     * Prevent bugs if the page is refreched.
     * Call Draw->drawLastTurn's method if it's last turn.
     * Call PlayEnd->computerPlayFirstEndGame and Draw->playableCards 's methods if stack is empty.
     * Else Make player draw and play a card, set playerCardPlayed on null and set stage on "draw"
     * Save Situation and render game.
     * 
     * @Route("/game/computerPlayFirst/{id}/", name="game_computerPlayFirst")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function computerPlayFirst($id, EntityManagerInterface $Manager) {

        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $Play = new ComputerPlay;
        $PlayEnd = new ComputerPlayEndGame;
        $Declarations = new Declarations;
        $Draw = new Draw;

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'trickGameResult') {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        
        if (sizeof($Situation->getStack()) === 1) {
            $Draw->drawLastTurn($Situation, $Manager);
            $Situation->computerPlayCard($PlayEnd->computerPlayFirstEndGame($Situation));
            $Draw->playableCards($Situation, $Manager);
            $Situation->setStage('play');
        } elseif (sizeof($Situation->getStack()) === 0) {
            $Situation->computerPlayCard($PlayEnd->computerPlayFirstEndGame($Situation));
            $Draw->playableCards($Situation, $Manager);
            $Situation->setStage('play');
        } else {
            $Situation->computerDrawCard();
            $Situation->computerPlayCard($Play->computerPlayFirst($Situation));
            $Situation->setPlayerCardPlayed(null);
            $Situation->setStage('draw');
        }

        $Manager->persist($Situation);
        $Manager->flush();

        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * The player won the trick and can choose between play, draw or declare depending on if he can declare something and if it's the end of the game.
     * Prevent bugs if the page is refreched.
     * If the stack is empty, set stage on "play".
     * Else if player can declare anything, set stage on "declare".
     * Else set stage on "draw".
     * set cards played on null.
     * Save Situation and render game.
     * 
     * @Route("/game/playerWonTrick/{id}/", name="game_playerWonTrick")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function playerWonTrick($id, EntityManagerInterface $Manager) {

        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'trickGameResult') {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        if (sizeof($Situation->getStack()) === 0) {
            $Situation->setStage('play');
        } elseif (sizeof($Situation->getDeclarationsAvailablePlayer()) > 0) {
            $Situation->setStage('declare');
        } else {
            $Situation->setStage('draw');
        }

        $Situation->setPlayerCardPlayed(null);
        $Situation->setComputerCardPlayed(null);

        $Manager->persist($Situation);
        $Manager->flush();

        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * Show the computer's declaration.
     * Prevent bugs if the page is refreched.
     * Check if computer can declare anything and declare if possible.
     * If it's last turn: Call Draw->drawLastTurn, computer plays first, call Draw->playableCards as P plays second and set stage on play.
     * Else if stack is empty, computer plays first, call Draw->playableCards as P plays second and set stage on play.
     * Else, make computer draw a card, play a card, set card played on null and set stage on draw.
     * Update declarationsAvailableComputer for it to know if it can declare anything else in case of the declaration was a seven of trummp.
     * Save situation.
     * Get declaration's cards than's to their numbers. Turn them into array, add to them a score element.
     * Render the declaration_computer's template with Situation and the declaration's cards.
     * 
     * @Route("/game/showDeclarationComputer/{id}/", name="game_showDeclarationComputer")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
     */
    public function showDeclarationComputer($id, EntityManagerInterface $Manager) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $Play = new ComputerPlay;
        $PlayEnd = new ComputerPlayEndGame;
        $Declarations = new Declarations;
        $Draw = new Draw;
        $Score = new Score;

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() !== 'trickGameResult') {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        sizeof($Declarations->possibleDeclarations($Situation->getHandComputer(), $Situation->getTrump()['suit'])) > 0 && $Declarations->computerChooseDeclaration($Situation, $Manager);
        $Manager->persist($Situation);
        $Manager->flush();
        if (sizeof($Situation->getStack()) === 1) {
            $Draw->drawLastTurn($Situation, $Manager);
            $Situation->computerPlayCard($PlayEnd->computerPlayFirstEndGame($Situation));
            $Draw->playableCards($Situation, $Manager);
            $Situation->setStage('play');
        } elseif (sizeof($Situation->getStack()) === 0) {
            $Situation->computerPlayCard($PlayEnd->computerPlayFirstEndGame($Situation));
            $Draw->playableCards($Situation, $Manager);
            $Situation->setStage('play');
        } else {
            $Situation->computerDrawCard();
            $Situation->computerPlayCard($Play->computerPlayFirst($Situation));
            $Situation->setPlayerCardPlayed(null);
            $Situation->setStage('draw');
        }
        $Situation->setDeclarationsAvailableComputer($Declarations->possibleDeclarations($Situation->getHandComputer(), $Situation->getTrump()['suit']));

        $Manager->persist($Situation);
        $Manager->flush();

        $Cards_repository = $this->getDoctrine()->getRepository(Cards::class);
        $Cards = $Cards_repository->findBy(['number' => $Situation->getScore()['lastDeclaration'][0]['cards']]);
        $cards_declaration = [];
        foreach ($Cards as $card) {
            $card_array = [
                'name' => $card->getName(),
                'number' => (int) $card->getNumber(),
                'suit' => $card->getSuit(),
                'rank' => (int) $card->getRank(),
                'twinCardNumber' => (int) $card->getTwinCardNumber(),
                'img' => $card->getImg()
            ];

            array_push($cards_declaration, $Score->scoreCard($card_array, $Cards[0]->getSuit())); // scoreCard return an entier card with a score element. Here I push this card in $cards.
        }

        return $this->render('game/declaration_computer.html.twig', ['situation' => $Situation->getSituation(), 'cardsDeclaration' => $cards_declaration]);
    }

    /**
     * Show the computer's second declaration.
     * This controller is called if computer just declared a seven of trump and if it can declare another thing.
     * Prevent bugs if the page is refreched.
     * Check if computer can declare anything and declare if possible.
     * Save Situation.
     * Get declaration's cards than's to their numbers. Turn them into array, add to them a score element.
     * Render the declaration_computer's template with Situation and the declaration's cards.
     * 
     * @Route("/game/showSecondDeclarationComputer/{id}/", name="game_showSecondDeclarationComputer")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
     */
    public function showSecondDeclarationComputer($id, EntityManagerInterface $Manager) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $Declarations = new Declarations;
        $Score = new Score;

        // Prevent from doing twice the same action if the page is refreched:
        if (isset($Situation->getScore()['alreadyDeclared'])) {
            return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
        }

        sizeof($Declarations->possibleDeclarations($Situation->getHandComputer(), $Situation->getTrump()['suit'])) > 0 && $Declarations->computerChooseDeclaration($Situation, $Manager);
        $Manager->persist($Situation);
        $Manager->flush();
        


        $Cards_repository = $this->getDoctrine()->getRepository(Cards::class);
        $Cards = $Cards_repository->findBy(['number' => $Situation->getScore()['lastDeclaration'][0]['cards']]);
        $cards_declaration = [];
        foreach ($Cards as $card) {
            $card_array = [
                'name' => $card->getName(),
                'number' => (int) $card->getNumber(),
                'suit' => $card->getSuit(),
                'rank' => (int) $card->getRank(),
                'twinCardNumber' => (int) $card->getTwinCardNumber(),
                'img' => $card->getImg()
            ];

            array_push($cards_declaration, $Score->scoreCard($card_array, $Cards[0]->getSuit())); // scoreCard return an entier card with a score element. Here I push this card in $cards.
        }
        $new_score = $Situation->getScore();
        $new_score['alreadyDeclared'] = true;
        $Situation->setScore($new_score);

        return $this->render('game/declaration_computer.html.twig', ['situation' => $Situation->getSituation(), 'cardsDeclaration' => $cards_declaration]);
    }

    /**
     * Go back to game's template.
     * Used to go back to the game when current page is declaration_computer.
     * Render the game template.
     * 
     * @Route("/game/situation/{id}", name="game_situation_continue")
     * 
     * @param int $id
     * @return response
    */
    public function continueGame($id) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        
        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }

    /**
     * End the round / the game and render results.
     * Calculate gain for 10 and aces in the trick for each player.
     * Prevent bugs if the page is refreched.
     * Set score with 10 and aces in trick for each player:
     * Save Situation.
     * If user is logged in and if at least one player's score reach 1000 or more:
     *      Get all user's data about his games and translate it into an array.
     *      Create an array with all the game's declarations.
     *      Calculate gain for 10 and aces for this game.
     *      Save current game.
     *      Render endGame.
     * Else: render endRound.
     * 
     * @Route("/game/endRound/{id}", name="game_endRound")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
    */
    public function endRound($id, EntityManagerInterface $Manager) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);

        // Calculate gain for 10 and aces in the trick for each player.
        $score_trick_player = array_filter($Situation->getTrickPlayer(), function($card) {
            return $card['rank'] === 7 or $card['rank'] === 8;
        });

        $score_trick_computer = array_filter($Situation->getTrickComputer(), function($card) {
            return $card['rank'] === 7 or $card['rank'] === 8;
        });

        $score_trick = [
            'player' => sizeof($score_trick_player),
            'computer' => sizeof($score_trick_computer)
        ];

        // Prevent from doing twice the same action if the page is refreched:
        if ($Situation->getStage() === 'showResultGame') {
            return $this->render('game/end_round.html.twig', ['situation' => $Situation->getSituation(), 'scoreTrick' => $score_trick]);
        }

        // Set score with 10 and aces in trick for each player:
        $new_score = $Situation->getScore();

        $new_score['player1'] += sizeof($score_trick_player) * 10;
        $new_score['player2'] += sizeof($score_trick_computer) * 10;

        $Situation->setScore($new_score);
        $Situation->setStage('showResultGame');
        
        $Manager->persist($Situation);
        $Manager->flush();

        if ($Situation->getUser() !== null and ($Situation->getScore()["player1"] > 1000 or $Situation->getScore()["player2"] > 1000)) { // If user has loged in and one of the player's score is over 1000, save the game's data and end the game.

            // Get all data of games:
            $User = $Situation->getUser();
            $all_games_user = $User->getGameID();
            $all_games_user_array = [];
            foreach ($all_games_user as $game) {
                array_push($all_games_user_array, [
                    'id' => $game->getId(),
                    'winner' => $game->getWinner(),
                    'scorePlayer' => $game->getScorePlayer(),
                    'scoreComputer' => $game->getScoreComputer(),
                    'declarationsMadePlayer' => $game->getDeclarationsMadePlayer(),
                    'declarationsMadeComputer' => $game->getDeclarationsMadeComputer(),
                    'valuablesCardsInTricksPlayer' => $game->getValuablesCardsInTricksPlayer(),
                    'valuablesCardsInTricksComputer' => $game->getValuablesCardsInTricksComputer(),
                    'roundsNumber' => $game->getRoundsNumber(),
                    'date' => $game->getDate(),
                    'difficulty' => $game->getDifficulty()
                ]);
            }

            // Create arrays with all the declarations made during the game:
            $all_declarations_player = $Situation->getScore()['declarationsListP1'];
            $all_declarations_computer = $Situation->getScore()['declarationsListP2'];
            foreach ($Situation->getScore()['previousRounds'] as $round) {
                array_push($all_declarations_player, ...$round['declarationsPlayer']);
                array_push($all_declarations_computer, ...$round['declarationsComputer']);
            }

            // Calculate gain of all the declarations and substract to result to find the gain of all the valuable cards in trick. Divide by ten to find their number.
            $total_declarations_gain_player = 0;
            $total_declarations_gain_computer = 0;
            foreach ($all_declarations_player as $declaration) $total_declarations_gain_player += $declaration['gain'];
            foreach ($all_declarations_computer as $declaration) $total_declarations_gain_computer += $declaration['gain'];
            $valuables_cards_in_tricks_player = ($Situation->getScore()['player1'] - $total_declarations_gain_player) / 10;
            $valuables_cards_in_tricks_computer = ($Situation->getScore()['player2'] - $total_declarations_gain_computer) / 10;
            

            // Save the current game:
            $date = new DateTime('@'. strtotime('now'));

            $Game = new Games;
            $Game->setWinner($Situation->getScore()['player1'] > $Situation->getScore()['player2'] ? 'player' : 'computer')
                ->setScorePlayer($Situation->getScore()['player1'])
                ->setScoreComputer($Situation->getScore()['player2'])
                ->setDeclarationsMadePlayer([$all_declarations_player])
                ->setDeclarationsMadeComputer([$all_declarations_computer])
                ->setValuablesCardsInTricksPlayer($valuables_cards_in_tricks_player)
                ->setValuablesCardsInTricksComputer($valuables_cards_in_tricks_computer)
                ->setRoundsNumber($Situation->getScore()['round'])
                ->setUserID($User)
                ->setDate($date)
                ->setDifficulty($Situation->getDifficulty())
                ;

            $Manager->persist($Game);
            $User->addGameID($Game);
            $Manager->flush();
            
            return $this->render('game/end_game.html.twig', ['situation' => $Situation->getSituation(), 'games' => $all_games_user_array, 'scoreTrick' => $score_trick]);
        } else {
            return $this->render('game/end_round.html.twig', ['situation' => $Situation->getSituation(),'scoreTrick' => $score_trick ]);
        }
    }

    /**
     * re set everything for new round.
     * If score['round'] is even (pair in french):
     *      Set playFirst on "computer", erase trump and cardPlayed and set stage on deal (player deal if computer start to play).
     * Else:
     *      Set playFirst on "computer", erase trump and cardPlayed.
     *      Get all cards from DB and shuffle them.
     *      Turn them into an array.
     *      Deal the cards.
     *      set stage on "draw".
     * Set new score with updated "round" and "previous rouds" elements.
     * Save Situation and render the game.
     * 
     * @Route("/game/newRound/{id}", name="game_new_round")
     * 
     * @param int $id
     * @param EntityManagerInterface $Manager
     * @return response
    */
    public function newRound($id, EntityManagerInterface $Manager) {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);
        $Score = new Score;
        
        if ($Situation->getScore()['round']%2) {// According to score['round'] deduce who start to play:
            $Situation->setPlayFirst('computer');
            $Situation->setComputerCardPlayed(null);
            $Situation->setTrump(null);
            $Situation->setStage('deal');
        } else {
            $Situation->setPlayFirst('player');
            $Situation->setComputerCardPlayed(null);
            $Situation->setTrump(null);
            $Cards_repository = $this->getDoctrine()->getRepository(Cards::class);
            $Cards = $Cards_repository->findAll();

            // Shuffle the cards:
            shuffle($Cards);

            $cards = [];
            foreach ($Cards as $card) {
                $card_array = [
                    'name' => $card->getName(),
                    'number' => (int) $card->getNumber(),
                    'suit' => $card->getSuit(),
                    'rank' => (int) $card->getRank(),
                    'twinCardNumber' => (int) $card->getTwinCardNumber(),
                    'img' => $card->getImg()
                ];

                array_push($cards, $Score->scoreCard($card_array, $Cards[0]->getSuit())); // scoreCard return an entier card with a score element. Here I push this card in $cards.
            }
            $Situation->deal($cards);
            $Situation->setStage('draw');
        }
        $oldDeclarations = $Situation->getScore()['previousRounds'];
        array_push($oldDeclarations, [
            'player' => $Situation->getScore()['player1'],
            'computer' => $Situation->getScore()['player2'],
            'declarationsPlayer' => $Situation->getScore()['declarationsListP1'],
            'declarationsComputer' => $Situation->getScore()['declarationsListP2']
        ]);
        $new_score = [
            'player1' => $Situation->getScore()['player1'],
            'player2' => $Situation->getScore()['player2'],
            'declarationsListP1' => [],
            'declarationsListP2' => [],
            'round' => $Situation->getScore()['round'],
            'previousRounds' => $oldDeclarations
        ];

        $Situation->setScore($new_score);
        $Situation->setTrickPlayer([]);
        $Situation->setTrickComputer([]);
        
        $Manager->persist($Situation);
        $Manager->flush();
        
        return $this->render('game/game.html.twig', ['situation' => $Situation->getSituation()]);
    }
}