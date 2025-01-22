<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Cards;
use App\Entity\Games;
use App\Entity\Users;
use App\Entity\Situation;
// use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    /**
     * Set the fixtures in DB
     * Create array of data.
     * Set cards.
     * Create 60 users.
     * for each user, create 0 to 15 game(s).
     * for each user, create 1 situation.
     * Save Cards, Users, Games and Situation's entities.
     *
     * @param ObjectManager $Manager
     * @return void
     */
    public function load(ObjectManager $Manager): void
    {
        $cards = [
            [
                'name' => 'sevenDiamonds',
                'number' => 1,
                'suit' => 'diamond',
                'rank' => 1,
                'twinCardNumber' => 5,
                'img' => '/images/svg/7_of_diamonds.svg'
            ],
            [
                'name' => 'sevenHearts',
                'number' => 2,
                'suit' => 'heart',
                'rank' => 1,
                'twinCardNumber' => 6,
                'img' => '/images/svg/7_of_hearts.svg'
            ],
            [
                'name' => 'sevenSpades',
                'number' => 3,
                'suit' => 'spade',
                'rank' => 1,
                'twinCardNumber' => 7,
                'img' => '/images/svg/7_of_spades.svg'
            ],
            [
                'name' => 'sevenClubs',
                'number' => 4,
                'suit' => 'club',
                'rank' => 1,
                'twinCardNumber' => 8,
                'img' => '/images/svg/7_of_clubs.svg'
            ],
            [
                'name' => 'sevenDiamonds',
                'number' => 5,
                'suit' => 'diamond',
                'rank' => 1,
                'twinCardNumber' => 1,
                'img' => '/images/svg/7_of_diamonds.svg'
            ],
            [
                'name' => 'sevenHearts',
                'number' => 6,
                'suit' => 'heart',
                'rank' => 1,
                'twinCardNumber' => 2,
                'img' => '/images/svg/7_of_hearts.svg'
            ],
            [
                'name' => 'sevenSpades',
                'number' => 7,
                'suit' => 'spade',
                'rank' => 1,
                'twinCardNumber' => 3,
                'img' => '/images/svg/7_of_spades.svg'
            ],
            [
                'name' => 'sevenClubs',
                'number' => 8,
                'suit' => 'club',
                'rank' => 1,
                'twinCardNumber' => 4,
                'img' => '/images/svg/7_of_clubs.svg'
            ],
            [
                'name' => 'eightDiamonds',
                'number' => 9,
                'suit' => 'diamond',
                'rank' => 2,
                'twinCardNumber' => 13,
                'img' => '/images/svg/8_of_diamonds.svg'
            ],
            [
                'name' => 'eightHearts',
                'number' => 10,
                'suit' => 'heart',
                'rank' => 2,
                'twinCardNumber' => 14,
                'img' => '/images/svg/8_of_hearts.svg'
            ],
            [
                'name' => 'eightSpades',
                'number' => 11,
                'suit' => 'spade',
                'rank' => 2,
                'twinCardNumber' => 15,
                'img' => '/images/svg/8_of_spades.svg'
            ],
            [
                'name' => 'eightClubs',
                'number' => 12,
                'suit' => 'club',
                'rank' => 2,
                'twinCardNumber' => 16,
                'img' => '/images/svg/8_of_clubs.svg'
            ],
            [
                'name' => 'eightDiamonds',
                'number' => 13,
                'suit' => 'diamond',
                'rank' => 2,
                'twinCardNumber' => 9,
                'img' => '/images/svg/8_of_diamonds.svg'
            ],
            [
                'name' => 'eightHearts',
                'number' => 14,
                'suit' => 'heart',
                'rank' => 2,
                'twinCardNumber' => 10,
                'img' => '/images/svg/8_of_hearts.svg'
            ],
            [
                'name' => 'eightSpades',
                'number' => 15,
                'suit' => 'spade',
                'rank' => 2,
                'twinCardNumber' => 11,
                'img' => '/images/svg/8_of_spades.svg'
            ],
            [
                'name' => 'eightClubs',
                'number' => 16,
                'suit' => 'club',
                'rank' => 2,
                'twinCardNumber' => 12,
                'img' => '/images/svg/8_of_clubs.svg'
            ],
            [
                'name' => 'nineDiamonds',
                'number' => 17,
                'suit' => 'diamond',
                'rank' => 3,
                'twinCardNumber' => 21,
                'img' => '/images/svg/9_of_diamonds.svg'
            ],
            [
                'name' => 'nineHearts',
                'number' => 18,
                'suit' => 'heart',
                'rank' => 3,
                'twinCardNumber' => 22,
                'img' => '/images/svg/9_of_hearts.svg'
            ],
            [
                'name' => 'nineSpades',
                'number' => 19,
                'suit' => 'spade',
                'rank' => 3,
                'twinCardNumber' => 23,
                'img' => '/images/svg/9_of_spades.svg'
            ],
            [
                'name' => 'nineClubs',
                'number' => 20,
                'suit' => 'club',
                'rank' => 3,
                'twinCardNumber' => 24,
                'img' => '/images/svg/9_of_clubs.svg'
            ],
            [
                'name' => 'nineDiamonds',
                'number' => 21,
                'suit' => 'diamond',
                'rank' => 3,
                'twinCardNumber' => 17,
                'img' => '/images/svg/9_of_diamonds.svg'
            ],
            [
                'name' => 'nineHearts',
                'number' => 22,
                'suit' => 'heart',
                'rank' => 3,
                'twinCardNumber' => 18,
                'img' => '/images/svg/9_of_hearts.svg'
            ],
            [
                'name' => 'nineSpades',
                'number' => 23,
                'suit' => 'spade',
                'rank' => 3,
                'twinCardNumber' => 19,
                'img' => '/images/svg/9_of_spades.svg'
            ],
            [
                'name' => 'nineClubs',
                'number' => 24,
                'suit' => 'club',
                'rank' => 3,
                'twinCardNumber' => 20,
                'img' => '/images/svg/9_of_clubs.svg'
            ],
            [
                'name' => 'jackDiamonds',
                'number' => 33,
                'suit' => 'diamond',
                'rank' => 4,
                'twinCardNumber' => 37,
                'img' => '/images/svg/jack_of_diamonds.svg'
            ],
            [
                'name' => 'jackHearts',
                'number' => 34,
                'suit' => 'heart',
                'rank' => 4,
                'twinCardNumber' => 38,
                'img' => '/images/svg/jack_of_hearts.svg'
            ],
            [
                'name' => 'jackSpades',
                'number' => 35,
                'suit' => 'spade',
                'rank' => 4,
                'twinCardNumber' => 39,
                'img' => '/images/svg/jack_of_spades.svg'
            ],
            [
                'name' => 'jackClubs',
                'number' => 36,
                'suit' => 'club',
                'rank' => 4,
                'twinCardNumber' => 40,
                'img' => '/images/svg/jack_of_clubs.svg'
            ],
            [
                'name' => 'jackDiamonds',
                'number' => 37,
                'suit' => 'diamond',
                'rank' => 4,
                'twinCardNumber' => 33,
                'img' => '/images/svg/jack_of_diamonds.svg'
            ],
            [
                'name' => 'jackHearts',
                'number' => 38,
                'suit' => 'heart',
                'rank' => 4,
                'twinCardNumber' => 34,
                'img' => '/images/svg/jack_of_hearts.svg'
            ],
            [
                'name' => 'jackSpades',
                'number' => 39,
                'suit' => 'spade',
                'rank' => 4,
                'twinCardNumber' => 35,
                'img' => '/images/svg/jack_of_spades.svg'
            ],
            [
                'name' => 'jackClubs',
                'number' => 40,
                'suit' => 'club',
                'rank' => 4,
                'twinCardNumber' => 36,
                'img' => '/images/svg/jack_of_clubs.svg'
            ],
            [
                'name' => 'queenDiamonds',
                'number' => 41,
                'suit' => 'diamond',
                'rank' => 5,
                'twinCardNumber' => 45,
                'img' => '/images/svg/queen_of_diamonds.svg'
            ],
            [
                'name' => 'queenHearts',
                'number' => 42,
                'suit' => 'heart',
                'rank' => 5,
                'twinCardNumber' => 46,
                'img' => '/images/svg/queen_of_hearts.svg'
            ],
            [
                'name' => 'queenSpades',
                'number' => 43,
                'suit' => 'spade',
                'rank' => 5,
                'twinCardNumber' => 47,
                'img' => '/images/svg/queen_of_spades.svg'
            ],
            [
                'name' => 'queenClubs',
                'number' => 44,
                'suit' => 'club',
                'rank' => 5,
                'twinCardNumber' => 48,
                'img' => '/images/svg/queen_of_clubs.svg'
            ],
            [
                'name' => 'queenDiamonds',
                'number' => 45,
                'suit' => 'diamond',
                'rank' => 5,
                'twinCardNumber' => 41,
                'img' => '/images/svg/queen_of_diamonds.svg'
            ],
            [
                'name' => 'queenHearts',
                'number' => 46,
                'suit' => 'heart',
                'rank' => 5,
                'twinCardNumber' => 42,
                'img' => '/images/svg/queen_of_hearts.svg'
            ],
            [
                'name' => 'queenSpades',
                'number' => 47,
                'suit' => 'spade',
                'rank' => 5,
                'twinCardNumber' => 43,
                'img' => '/images/svg/queen_of_spades.svg'
            ],
            [
                'name' => 'queenClubs',
                'number' => 48,
                'suit' => 'club',
                'rank' => 5,
                'twinCardNumber' => 44,
                'img' => '/images/svg/queen_of_clubs.svg'
            ],
            [
                'name' => 'kingDiamonds',
                'number' => 49,
                'suit' => 'diamond',
                'rank' => 6,
                'twinCardNumber' => 53,
                'img' => '/images/svg/king_of_diamonds.svg'
            ],
            [
                'name' => 'kingHearts',
                'number' => 50,
                'suit' => 'heart',
                'rank' => 6,
                'twinCardNumber' => 54,
                'img' => '/images/svg/king_of_hearts.svg'
            ],
            [
                'name' => 'kingSpades',
                'number' => 51,
                'suit' => 'spade',
                'rank' => 6,
                'twinCardNumber' => 55,
                'img' => '/images/svg/king_of_spades.svg'
            ],
            [
                'name' => 'kingClubs',
                'number' => 52,
                'suit' => 'club',
                'rank' => 6,
                'twinCardNumber' => 56,
                'img' => '/images/svg/king_of_clubs.svg'
            ],
            [
                'name' => 'kingDiamonds',
                'number' => 53,
                'suit' => 'diamond',
                'rank' => 6,
                'twinCardNumber' => 49,
                'img' => '/images/svg/king_of_diamonds.svg'
            ],
            [
                'name' => 'kingHearts',
                'number' => 54,
                'suit' => 'heart',
                'rank' => 6,
                'twinCardNumber' => 50,
                'img' => '/images/svg/king_of_hearts.svg'
            ],
            [
                'name' => 'kingSpades',
                'number' => 55,
                'suit' => 'spade',
                'rank' => 6,
                'twinCardNumber' => 51,
                'img' => '/images/svg/king_of_spades.svg'
            ],
            [
                'name' => 'kingClubs',
                'number' => 56,
                'suit' => 'club',
                'rank' => 6,
                'twinCardNumber' => 52,
                'img' => '/images/svg/king_of_clubs.svg'
            ],
            [
                'name' => 'tenDiamonds',
                'number' => 25,
                'suit' => 'diamond',
                'rank' => 7,
                'twinCardNumber' => 29,
                'img' => '/images/svg/10_of_diamonds.svg'
            ],
            [
                'name' => 'tenHearts',
                'number' => 26,
                'suit' => 'heart',
                'rank' => 7,
                'twinCardNumber' => 30,
                'img' => '/images/svg/10_of_hearts.svg'
            ],
            [
                'name' => 'tenSpades',
                'number' => 27,
                'suit' => 'spade',
                'rank' => 7,
                'twinCardNumber' => 31,
                'img' => '/images/svg/10_of_spades.svg'
            ],
            [
                'name' => 'tenClubs',
                'number' => 28,
                'suit' => 'club',
                'rank' => 7,
                'twinCardNumber' => 32,
                'img' => '/images/svg/10_of_clubs.svg'
            ],
            [
                'name' => 'tenDiamonds',
                'number' => 29,
                'suit' => 'diamond',
                'rank' => 7,
                'twinCardNumber' => 25,
                'img' => '/images/svg/10_of_diamonds.svg'
            ],
            [
                'name' => 'tenHearts',
                'number' => 30,
                'suit' => 'heart',
                'rank' => 7,
                'twinCardNumber' => 26,
                'img' => '/images/svg/10_of_hearts.svg'
            ],
            [
                'name' => 'tenSpades',
                'number' => 31,
                'suit' => 'spade',
                'rank' => 7,
                'twinCardNumber' => 27,
                'img' => '/images/svg/10_of_spades.svg'
            ],
            [
                'name' => 'tenClubs',
                'number' => 32,
                'suit' => 'club',
                'rank' => 7,
                'twinCardNumber' => 28,
                'img' => '/images/svg/10_of_clubs.svg'
            ],
            [
                'name' => 'aceDiamond',
                'number' => 57,
                'suit' => 'diamond',
                'rank' => 8,
                'twinCardNumber' => 61,
                'img' => '/images/svg/ace_of_diamonds.svg'
            ],
            [
                'name' => 'aceHeart',
                'number' => 58,
                'suit' => 'heart',
                'rank' => 8,
                'twinCardNumber' => 62,
                'img' => '/images/svg/ace_of_hearts.svg'
            ],
            [
                'name' => 'aceSpade',
                'number' => 59,
                'suit' => 'spade',
                'rank' => 8,
                'twinCardNumber' => 63,
                'img' => '/images/svg/ace_of_spades.svg'
            ],
            [
                'name' => 'aceClub',
                'number' => 60,
                'suit' => 'club',
                'rank' => 8,
                'twinCardNumber' => 64,
                'img' => '/images/svg/ace_of_clubs.svg'
            ],
            [
                'name' => 'aceDiamond',
                'number' => 61,
                'suit' => 'diamond',
                'rank' => 8,
                'twinCardNumber' => 57,
                'img' => '/images/svg/ace_of_diamonds.svg'
            ],
            [
                'name' => 'aceHeart',
                'number' => 62,
                'suit' => 'heart',
                'rank' => 8,
                'twinCardNumber' => 58,
                'img' => '/images/svg/ace_of_hearts.svg'
            ],
            [
                'name' => 'aceSpade',
                'number' => 63,
                'suit' => 'spade',
                'rank' => 8,
                'twinCardNumber' => 59,
                'img' => '/images/svg/ace_of_spades.svg'
            ],
            [
                'name' => 'aceClub',
                'number' => 64,
                'suit' => 'club',
                'rank' => 8,
                'twinCardNumber' => 60,
                'img' => '/images/svg/ace_of_clubs.svg'
            ],
        ];

        $suits = [
            'diamond',
            'spade',
            'heart',
            'club'
        ];

        $declarations_name = [
            'sept d\'atout',
            'couple de ' . $suits[rand(0, 3)],
            'couple d\'atout',
            'demi brézin',
            'carré de valets',
            'carré de dammes',
            'carré de rois',
            'carré d\'as',
            'deux cent cinquante',
            'brézin'
        ];

        $declarations = [
            'sept d\'atout' => 10,
            'couple de ' . $suits[rand(0, 3)]  => 20,
            'couple d\'atout' => 40,
            'demi brézin' => 40,
            'carré de valets' => 40,
            'carré de dammes' => 60,
            'carré de rois' => 80,
            'carré d\'as' => 100,
            'deux cent cinquante' => 250,
            'brézin' => 500
        ];

        $score = [
            'player1' => 0,
            'player2' => 0,
            'declarationsListP1' => [],
            'declarationsListP2' => [],
            'round' => 0,
            'previousRounds' => [
                'player' => 0,
                'computer' => 0,
                'declarationsPlayer' => [],
                'declarationsComputer' => []
            ]
        ];

        




        foreach ($cards as $card) {
            $Card = new Cards;
            $Card->setName($card['name'])
                 ->setNumber($card['number'])
                 ->setSuit($card['suit'])
                 ->setRank($card['rank'])
                 ->setTwinCardNumber($card['twinCardNumber'])
                 ->setImg($card['img'])
                 ;
            $Manager->persist($Card);
        }

        $faker = Factory::create('FR-fr');
        // $slugify = new Slugify();
        $users = [];
        $images = [];
        // Create a loop for Users class:
        for ($index1 = 0; $index1 < 60; $index1++) {
            $user = new Users();
            $already_used_avatars = [];
            $random_avatar_image = rand(1, 25);
            $firstname = $faker->firstname;
            $lastname = $faker->lastname;
            $games_number = rand(0, 15);
            for ($index2 = 0; $index2 < $games_number; $index2++) { 
                $game = new Games();
                $winner;
                $score_player = 0;
                $score_computer = 0;
                $declarations_made_player = [];
                $declarations_made_computer = [];
                $valuables_cards_in_tricks_player;
                $valuables_cards_in_tricks_computer;
                $winner;
                
                
                if ($score_player > $score_computer) {
                    $winner = 'player';
                } elseif ($score_computer > $score_player) {
                    $winner = 'computer';
                } else {
                    $winner = 'equality';
                }

                for ($bestScore = 0; $bestScore < 1000; $bestScore = $score_player > $score_computer ? $score_player : $score_computer) { 
                    $ten_in_tricks_player = rand(0, 16);
                    foreach($declarations as $key => $value) {
                        $random1 = rand(0, 1);
                        $random2 = rand(0, 1);
                        if ($random1 > 0) {
                            $score_player += $value;
                            array_push($declarations_made_player, $key);
                        }
                        if ($random2 > 0) {
                            $score_computer += $value;
                            array_push($declarations_made_computer, $key);
                        }
                    }
                    $valuables_cards_in_tricks_player = $ten_in_tricks_player;
                    $valuables_cards_in_tricks_computer = 16 - $ten_in_tricks_player;
                }

                if ($score_player > $score_computer) {
                    $winner = 'player';
                } elseif ($score_player < $score_computer) {
                    $winner = 'computer';
                } else {
                    $winner = 'equality';
                }
                
                $game->setWinner($winner)
                     ->setScorePlayer($score_player)
                     ->setScoreComputer($score_computer)
                     ->setDeclarationsMadePlayer($declarations_made_player)
                     ->setDeclarationsMadeComputer($declarations_made_computer)
                     ->setValuablesCardsInTricksPlayer($valuables_cards_in_tricks_player)
                     ->setValuablesCardsInTricksComputer($valuables_cards_in_tricks_computer)
                     ->setRoundsNumber(rand(0, 5))
                     ->setDate($faker->dateTimeBetween('-100 days', '-1 days'))
                     ->setUserID($user)
                    ;
                $Manager->persist($game);
                $user->addGameID($game);
            }

            $situation = new Situation;
            $situation->setInit(false)
                      ->setPlayFirst('player')
                      ->setStage('deal')
                      ->setTrump(null)
                      ->setStack([])
                      ->setPlayerCardPlayed(null)
                      ->setComputerCardPlayed(null)
                      ->setHandPlayer([])
                      ->setHandComputer([])
                      ->setTrickPlayer([])
                      ->setTrickComputer([])
                      ->setDeclarationsAvailablePlayer([])
                      ->setDeclarationsAvailableComputer([])
                      ->setScore($score)
                      ->setUser($user)
                      ->setDate($faker->dateTimeBetween('-100 days', '-1 days'))
            ;
            $Manager->persist($situation);

            

            $user->setFirstName(strtolower($firstname))
                 ->setLastName(strtoupper($lastname))
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setAvatar('/images/avatar/' . $random_avatar_image . '.jpg')
                 ->setRoles([])
                 ->setPassword(password_hash('password', PASSWORD_DEFAULT))
                 ->setSituation($situation)
            ;

            array_push($already_used_avatars, $random_avatar_image);
            $Manager->persist($user);
            $users[]=$user;
        }

        //  Flush:  Requests the server to send its currently buffered output to the browser.
        $Manager->flush();
    }
}




    