<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerInviteController extends AbstractController
{
    /**
     * @Route("/player/invite", name="player_invite")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // if token is passed by POST, the game is already created and it add the player to the game
        if (isset($_GET['token'])) 
        {
            // verify if the token is valid and not expired
            $token = $_GET['token'];
            $game = $this->getDoctrine()->getRepository(Game::class)->findOneBy(['room_token' => $token]);
            
            $friendId = $this->getUser()->getId();
            
        //    dump($friend); // verify friend id
        //    dump($game);

            if($game->getInviteExpiration() <= time()) 
            {
                return $this->render('player_invite/index.html.twig', [
                    'controller_name' => 'PlayerInviteController',
                    'token' => $token,
                    'players' => $friendId,
                    'game_id' => 0
                ]);
            }
            else
            {   
                $tempArray = $game->getUsersId();
                array_push($tempArray, $friendId);
                $game->setUsersId($tempArray);
                $entityManager->flush();

                return $this->render('player_invite/index.html.twig', [
                    'controller_name' => 'PlayerInviteController',
                    'token' => $token,
                    'players' => $game->getUsersId(),
                    'game_id' => $game->getId()
                ]);
            }
        }
        else    // time to create a game
        {
        //    $token = uniqid(); // Generate random token for a game
            $token = random_bytes(5); // Generate random token for a game
            $token = bin2hex($token);

            $users_id = [$this->getUser()->getId()];

            $game = new Game();   // Create a new game
            $game->setUsersId($users_id)              // Add the player id in the array
                ->setRoomToken($token)                  // Specify the unique token of this room
                ->setInviteExpiration(time()+(30*60));  // The invite expires after 30 minutes
            
            $entityManager->persist($game);
            $entityManager->flush();

            dump($game->getId());   // verify created game id

            return $this->render('player_invite/index.html.twig', [
                'controller_name' => 'PlayerInviteController',
                'token' => $token,
                'players' => $game->getUsersId(),
                'game_id' => $game->getId()
            ]);
        }
    }
}
