<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlayerInviteController extends AbstractController
{
    /**
     * @Route("/player/invite", name="player_invite")
     */
    public function index(HubInterface $hub): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // if token is passed by GET or session variable, the game is already created and it add the player to the game
        if (isset($_GET['token']) || $this->get('session')->get('token')) {
            if (isset($_GET['token'])) {    // token passed by GET    
                $token = $_GET['token'];
            } elseif ($this->get('session')->get('token')) {   // token is in session variable
                $token = $this->get('session')->get('token');
            }

            // database query with token filter
            $game = $this->getDoctrine()
                ->getRepository(Game::class)
                ->findOneBy(['room_token' => $token]);

            // verify if a token exist in base, if not, the player is warned
            if (!$game) {
                // delete the session variable token because it's useless now
                $this->get('session')->set('token', NULL);
                
                // player is warned there's a problem with the link
                return $this->render('player_invite/index.html.twig', [
                    'controller_name' => 'PlayerInviteController',
                    'token' => 0,
                    'players' => [0,0],
                    'game_id' => 0
                ]);
            }

            // if the player is not logged in or does not have any account
            if (!$this->getUser()) {
                // token saved in session variable
                $this->get('session')->set('token', $token);

                // redirect to login/create account
                return $this->redirectToRoute('app_login');
            } 
            
            // player is logged, retrieve his id and pseudo
            $friendId = $this->getUser()->getId();
            $friendPseudo = $this->getUser()->getPseudo();

            // verify if the invite is not expired, 30min after the game creation or the game is alreday started or finished
            if ($game->getInviteExpiration() <= time() || $game->getState() != 'Pending') {
                // delete the session variable token because it's useless now
                $this->get('session')->set('token', NULL);

                // launches display that there is a problem
                return $this->render('player_invite/index.html.twig', [
                    'controller_name' => 'PlayerInviteController',
                    'token' => 0,
                    'players' => [0,0],
                    'game_id' => 0
                ]);
            } 
            // the invite is valid            
            // extract the array of players from the game table in database
            $idArray = $game->getUsersId();
            
            // test if the player is not already in this game
            if (!in_array(array($friendId,$friendPseudo),$idArray)) {
                // add the player at the end of the array
                array_push($idArray, array($friendId,$friendPseudo));
                $game->setUsersId($idArray);
                $entityManager->flush();
            }
            // duration of game saved in session variable
            $this->get('session')->set('timer', "120");
            
            // Send an event to the hub for a new player
            $url = $this->getParameter('mercure.host').'player/invite/'.$game->getId();
            
            $update = new Update(
                $url,
                json_encode(array('subject' => 'player', 'player' => $friendPseudo))
            );
            
            $hub->publish($update);

            // display the player waiting room
            return $this->render('player_invite/index.html.twig', [
                'controller_name' => 'PlayerInviteController',
                'token' => $token,
                'players' => $idArray,
                'game_id' => $game->getId()
            ]);
        } 
        // time to create a game
        
        // Generate random token for a game
        $token = random_bytes(5); 
        $token = bin2hex($token);

        // token, creator id and duration of game saved in session variables
        $this->get('session')->set('token', $token);
        $this->get('session')->set('creator', $this->getUser()->getId());
        $this->get('session')->set('timer', "120");

        // new verification, if the player is not logged in
        if (!$this->getUser()) {
            // redirect to login/create account
            return $this->redirectToRoute('app_login');
        }

        // initialize the array users_id with the first player id
        $users_id = array(
            $this->getUser()->getId(),
            $this->getUser()->getPseudo()
        );

        // Create a new game
        $game = new Game();

        $connection = $entityManager->getConnection();
        $statement = $connection->prepare('SELECT sentence FROM sentence s WHERE type = "vote" ORDER BY RAND() LIMIT 1');
        $statement->execute();
        $sentenceVote = $statement->fetch();

        $game->setUsersId([$users_id])              // Add the player id in the array
            ->setRoomToken($token)                  // Specify the unique token of this room
            ->setInviteExpiration(time() + (30 * 60))  // The invite expires after 30 minutes
            ->setVoteSentence($sentenceVote["sentence"]);
        
        $entityManager->persist($game);
        $entityManager->flush();

        return $this->render('player_invite/index.html.twig', [
            'controller_name' => 'PlayerInviteController',
            'token' => $token,
            'players' => [$users_id],   // Get the player pseudo to display in players list
            'game_id' => $game->getId()
        ]);
        
    }
}
