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
        if (isset($_POST['token'])) {
            $id = $_GET['player'];
            $user = $this->getUser();
            $friend = $this->getDoctrine()->getRepository(User::class)->find($id);
            $user->addFriend($friend);
        }
        else {
            if (!isset($_GET['player']) && !isset($_POST['token'])) {
                throw new \Exception('No player ID in ');
            }
        
        //    $token = uniqid(); // Generate random token for a game
            $token = random_bytes(5); // Generate random token for a game
            $token = bin2hex($token);

            $users_id = [$_GET['player']];
            $game = new Game();   // Create a new game
            $game->setUsersId($users_id)              // Add the player id in the array
                ->setRoomToken($token)                  // Specify the unique token of this room
                ->setInviteExpiration(time()+(30*60));  // The invite expires after 30 minutes
            
            $entityManager->persist($game);
            $entityManager->flush();

            dump($game->getId());   // verify created game id
            
            $id_players = $game->getUsersId();

            return $this->render('player_invite/index.html.twig', [
                'controller_name' => 'PlayerInviteController',
                'token' => $token,
                'players' => $id_players,
                'game_id' => $game->getId()
            ]);
        }
    }
}
