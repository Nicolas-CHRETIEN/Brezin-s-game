<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Users;
use App\Entity\Situation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersController extends AbstractController
{
    /**
     * Show resume of the selected game.
     * Get game's data from DB
     * Render oldGame with game and situation's data.
     * 
     * @Route("/game/game/{id}/situation/{situation}", name="game_show_resume")
     * 
     * @param int $id Game's id.
     * @param int $situation Situation's id.
     * @return responce
    */
    public function showGameResume($id, $situation) {
        $Games_repository = $this->getDoctrine()->getRepository(Games::class);
        $Game = $Games_repository->findOneById($id);

        $Situation_repository = $this->getDoctrine()->getRepository (Situation::class);
        $Situation = $Situation_repository->findOneById($situation);

        $game = [
            'id' => $Game->getId(),
            'winner' => $Game->getWinner(),
            'scorePlayer' => $Game->getScorePlayer(),
            'scoreComputer' => $Game->getScoreComputer(),
            'declarationsMadePlayer' => $Game->getDeclarationsMadePlayer(),
            'declarationsMadeComputer' => $Game->getDeclarationsMadeComputer(),
            'valuablesCardsInTricksPlayer' => $Game->getValuablesCardsInTricksPlayer(),
            'valuablesCardsInTricksComputer' => $Game->getValuablesCardsInTricksComputer(),
            'roundsNumber' => $Game->getRoundsNumber(),
            'date' => $Game->getDate(),
            'difficulty' => $Game->getDifficulty()
        ];
        
        return $this->render('game/oldGame/oldGame.html.twig', ['game' => $game, 'situation' => $Situation]);
    }

    /**
     * Show personal account page.
     * Get every user's grade in an array.
     * Sort it to update every user's ranking.
     * Save every user's data.
     * Get all user's game's data.
     * Turn it into an array.
     * Thank's to data of games, calculate all the indicators needed for personal account's page.
     * Turn them into an array named $score.
     * create a new array named $games_with_difficulty for evolution's graph.
     * Render myAccount's page with score and evolution's data.
     * 
     * @Route("/MyAccount/{id}", name="app_myAccount")
     * 
     * @param int $id User's id.
     * @param EntityManagerInterface $Manager
     * @return responce
    */
    public function myAccount($id, EntityManagerInterface $Manager) {

        $Users_repository = $this->getDoctrine()->getRepository(Users::class);
        // Update user's grade.
        $Curent_user = $Users_repository->findOneById($id);
        $Curent_user->__constructGrade();
        
        $Users = $Users_repository->findAll();

        // Update every user's ranking
        $array = [];
        foreach ($Users as $User) {
            array_push($array, $User->getGrade());
        }
        // Sort the array with all grades.
        rsort($array, SORT_NUMERIC);
        // Grade's index + 1 is now equal to the user's rank as it's sorted.
        foreach ($Users as $User) {
            $ranking = array_search($User->getGrade(), $array) + 1;
            $User->setRanking($ranking);
            $Manager->persist($User);
        }
        $Manager->flush();

        // Get user's games data:
        $all_games_user = $Curent_user->getGameID();
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
        $number_games = sizeof($all_games_user_array);

        
        $easy_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'easy';
        }));
        $easy_winning_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'easy' and $game['winner'] === 'player';
        }));
        $easy_games_number = sizeof($easy_games);
        $easy_winning_games_number = sizeof($easy_winning_games);

        $normal_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'normal';
        }));
        $normal_winning_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'normal' and $game['winner'] === 'player';
        }));
        $normal_games_number = sizeof($normal_games);
        $normal_winning_games_number = sizeof($normal_winning_games);

        $hard_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'hard';
        }));
        $hard_winning_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'hard' and $game['winner'] === 'player';
        }));
        $hard_games_number = sizeof($hard_games);
        $hard_winning_games_number = sizeof($hard_winning_games);

        $expert_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'expert';
        }));
        $expert_winning_games = array_values(array_filter($all_games_user_array, function($game) {
            return $game['difficulty'] === 'expert' and $game['winner'] === 'player';
        }));
        $expert_games_number = sizeof($expert_games);
        $expert_winning_games_number = sizeof($expert_winning_games);

        $easy_winning_rate = $easy_games_number > 0 ? round(($easy_winning_games_number / $easy_games_number), 2, PHP_ROUND_HALF_UP) : 0;
        $easy_loosing_rate = $easy_games_number > 0 ? 1 - ($easy_winning_games_number / $easy_games_number) : 0;
        $normal_winning_rate = $normal_games_number > 0 ? round(($normal_winning_games_number / $normal_games_number), 2, PHP_ROUND_HALF_UP) : 0;
        $normal_loosing_rate = $normal_games_number > 0 ? 1 - ($normal_winning_games_number / $normal_games_number) : 0;
        $hard_winning_rate = $hard_games_number > 0 ? round(($hard_winning_games_number / $hard_games_number), 2, PHP_ROUND_HALF_UP) : 0;
        $hard_loosing_rate = $hard_games_number > 0 ? 1 - ($hard_winning_games_number / $hard_games_number) : 0;
        $expert_winning_rate = $expert_games_number > 0 ? round(($expert_winning_games_number / $expert_games_number), 2, PHP_ROUND_HALF_UP) : 0;
        $expert_loosing_rate = $expert_games_number > 0 ? 1 - ($expert_winning_games_number / $expert_games_number) : 0;



        $score = [
            'easy' => [
                'number' => $easy_games_number,
                'winningNumber' => $easy_winning_games_number,
                'games' => $easy_games,
                'winningGames' => $easy_winning_games,
                'winningRate' => $easy_winning_rate,
                'loosingRate' => $easy_loosing_rate
            ],
            'normal' => [
                'number' => $normal_games_number,
                'winningNumber' => $normal_winning_games_number,
                'games' => $normal_games,
                'winningGames' => $normal_winning_games,
                'winningRate' => $normal_winning_rate,
                'loosingRate' => $normal_loosing_rate
            ],
            'hard' => [
                'number' => $hard_games_number,
                'winningNumber' => $hard_winning_games_number,
                'games' => $hard_games,
                'winningGames' => $hard_winning_games,
                'winningRate' => $hard_winning_rate,
                'loosingRate' => $hard_loosing_rate
            ],
            'expert' => [
                'number' => $expert_games_number,
                'winningNumber' => $expert_winning_games_number,
                'games' => $expert_games,
                'winningGames' => $expert_winning_games,
                'winningRate' => $expert_winning_rate,
                'loosingRate' => $expert_loosing_rate
            ]
        ];

        $games_with_difficulty = array_values(array_filter($all_games_user_array, function($game, $index) use ($number_games) {
            return isset($game['difficulty']) and $index > ($number_games - 16); // Select only the 15 last game to create the graph
        }, ARRAY_FILTER_USE_BOTH));
        
        return $this->render('account/myAccount.html.twig', ['games' => $all_games_user_array, 'score' => $score, 'evolution' => $games_with_difficulty]);
    }


    /**
     * Generate evolution graph.
     * Get user's games data and turn it into an array.
     * Filter the array to get the needed data for evolution graph.
     * Render the svg graph with evolution's data.
     * 
     * @Route("/evolution/{id}", name="evolution")
     * 
     * @param int $id User's id.
     * @return responce
    */
    public function evolutionGraph($id) {
        $Users_repository = $this->getDoctrine()->getRepository(Users::class);
        $Curent_user = $Users_repository->findOneById($id);

        $all_games_user = $Curent_user->getGameID();
        $all_games_user_array = [];
        foreach ($all_games_user as $game) {
            array_push($all_games_user_array, [
                'id' => $game->getId(),
                'winner' => $game->getWinner(),
                'difficulty' => $game->getDifficulty()
            ]);
        }
        $number_games = sizeof($all_games_user_array);


        $games_with_diificulty = array_values(array_filter($all_games_user_array, function($game, $index) use ($number_games) {
            return isset($game['difficulty']) and $index > ($number_games - 16); // Select only the 15 last game to create the graph
        }, ARRAY_FILTER_USE_BOTH));
        
        return $this->render('graph/evolution.html.twig', ['evolution' => $games_with_diificulty]);
    }
}