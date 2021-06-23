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
        if (isset($_GET['token']) || $this->get('session')->get('token')) 
        {
            // token passed by GET
            if (isset($_GET['token'])) 
            {
                $token = $_GET['token'];
            }
            // token is in session variable
            elseif ($this->get('session')->get('token')) 
            {
                $token = $this->get('session')->get('token');
            }

            // database query with token filter
            $game = $this->getDoctrine()
                ->getRepository(Game::class)
                ->findOneBy(['room_token' => $token]);

            // verify if a token exist in base, if not, the player is warned
            if (!$game) 
            {
                // delete the session variable token because it's useless now
                $this->get('session')->clear();
                
                // player is warned there's a problem with the link
                return $this->render('player_invite/index.html.twig', [
                    'controller_name' => 'PlayerInviteController',
                    'token' => 0,
                    'players' => [0,0],
                    'game_id' => 0
                ]);
            }

            // if the player is not logged in or does not have any account
            if (!$this->getUser()) 
            {
                // token saved in session variable
                $this->get('session')->set('token', $token);

                // redirect to login/create account
                return $this->redirectToRoute('app_login');
            }

            // player is logged
            else 
            {
                $friendId = $this->getUser()->getId();
                $friendPseudo = $this->getUser()->getPseudo();

                // verify if the invite is not expired, 30min after the game creation
                if ($game->getInviteExpiration() <= time()) {
                    // delete the session variable token because it's useless now
                    $this->get('session')->clear();

                    // launches display that there is a problem
                    return $this->render('player_invite/index.html.twig', [
                        'controller_name' => 'PlayerInviteController',
                        'token' => 0,
                        'players' => [$friendId,$friendPseudo],
                        'game_id' => 0
                    ]);
                }
                // the invite is valid
                else 
                {
                    dump($friendId,$friendPseudo);
                    // extract the array of players from the game table in database
                    $idArray = $game->getUsersId();
                    dump($idArray);
                //    var_dump(array_key_exists($friendPseudo,$idArray));
                //    var_dump(array_search($friendPseudo, array_map("strval", array_keys($$friendPseudo))));
                    var_dump( array_search( $friendPseudo, $idArray, true ) );
                    // test if the player is not already in this game
                    
                    if (array_search($friendPseudo, array_column($idArray, '0', '1')) === 0) 
                    {
                        // add the player at the end of the array
                        array_push($idArray, array($friendId,$friendPseudo));
                        $game->setUsersId($idArray);
                        $entityManager->flush();
                    }
                    dump($idArray);
                    
                    // Send an event tu the hub
                    $url = 'http://localhost:8080/player/invite/'.$game->getId();
                //    dump($url);
                    $update = new Update(
                        $url,
                        json_encode(['player' => $friendPseudo]),
                        true
                    );

                    $hub->publish($update);

                /*    $pseudoArray = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findBy('id' => $idArray);*/

                //    dump($idArray);

                    // display the player waiting room
                    return $this->render('player_invite/index.html.twig', [
                        'controller_name' => 'PlayerInviteController',
                        'token' => $token,
                        'players' => $idArray,
                        'game_id' => $game->getId()
                    ]);
                }
            }
        } 
        else    // time to create a game
        {
            //    $token = uniqid(); // Generate random token for a game
            $token = random_bytes(5); // Generate random token for a game
            $token = bin2hex($token);

            // token saved in session variable
            $this->get('session')->set('token', $token);

            // new verification, if the player is not logged in
            if (!$this->getUser()) {
                // redirect to login/create account
                return $this->redirectToRoute('app_login');
            }

            // initialize the array users_id with the first player id
            $users_id = array(
                $this->getUser()->getId(),$this->getUser()->getPseudo()
            );
        //    dump($users_id);

            // Create a new game
            $game = new Game();

            $game->setUsersId([$users_id])              // Add the player id in the array
                ->setRoomToken($token)                  // Specify the unique token of this room
                ->setInviteExpiration(time() + (30 * 60));  // The invite expires after 30 minutes

            $entityManager->persist($game);
            $entityManager->flush();
            //    dump($game);   // verify created game

            return $this->render('player_invite/index.html.twig', [
                'controller_name' => 'PlayerInviteController',
                'token' => $token,
                'players' => [$users_id],   // Get the player pseudo to display in players list
                'game_id' => $game->getId()
            ]);
        }
    }
}
