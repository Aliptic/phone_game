<?php

namespace App\Controller;

use App\Entity\Game;
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
                return $this->render('player_invite/index.html.twig', [
                    'controller_name' => 'PlayerInviteController',
                    'token' => $token,
                    'players' => 0,
                    'game_id' => 0
                ]);
            }
            
            // if the player is not logged in or does not have any account
            if(!$this->getUser())      
            {
                // token saved in variable session
                $this->get('session')->set('token', $token); 
                
                // redirect to login/create account
                return $this->redirectToRoute('app_login');
                
            //    return $this->generateUrl('app_login', ['token' => $token]);
            }

            // player is already logged
            else
            {
                $friendId = $this->getUser()->getId();
            
            //    dump($friend); // verify friend id
            //    dump($game);

                // verify if the invite is not expired, 30min after the game creation
                if($game->getInviteExpiration() <= time())  
                {
                    // delete the session variable token because it's useless now
                    $this->get('session')->clear();

                    // launches display that there is a problem
                    return $this->render('player_invite/index.html.twig', [
                        'controller_name' => 'PlayerInviteController',
                        'token' => $token,
                        'players' => $friendId,
                        'game_id' => 0
                    ]);
                }
                // the invite is valid
                else
                {   
                    // extract the array of players from the game table in database
                    $tempArray = $game->getUsersId();

                    // test if the player is not already in this game
                    if(!in_array($friendId,$tempArray))
                    {
                        // add the player at the end of the array
                        array_push($tempArray, $friendId);
                        $game->setUsersId($tempArray);
                        $entityManager->flush();
                    }

                    return $this->render('player_invite/index.html.twig', [
                        'controller_name' => 'PlayerInviteController',
                        'token' => $token,
                        'players' => $game->getUsersId(),
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

            // initialize the array users_id with the first player id
            $users_id = [$this->getUser()->getId()];

            // Create a new game
            $game = new Game();

            $game->setUsersId($users_id)              // Add the player id in the array
                ->setRoomToken($token)                  // Specify the unique token of this room
                ->setInviteExpiration(time()+(30*60));  // The invite expires after 30 minutes
            
            $entityManager->persist($game);
            $entityManager->flush();

        //    dump($game->getId());   // verify created game id

            return $this->render('player_invite/index.html.twig', [
                'controller_name' => 'PlayerInviteController',
                'token' => $token,
                'players' => $game->getUsersId(),
                'game_id' => $game->getId()
            ]);
        }
    }
}
