<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TranslatorInterface $translator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $connection = $entityManager->getConnection(); 
        
        $invitation = 0;
        $numGame = 0;
        
        // if a player is logged and has been invited in a game, we display the invitation on home page
        if($this->get('session')->get('token'))
        {   
            $token = $this->get('session')->get('token');
            // database query with token filter
            $game = $this->getDoctrine()
                ->getRepository(Game::class)
                ->findOneBy(['room_token' => $token]);
            
            $numGame = $game->getId();
                
            // verify if a token exist in base, if not, the player is warned
            if (!$game) {
                // delete the session variable token because it's useless now
                $this->get('session')->set('token', NULL);
            } else {
                $invitation = 1;
            }
        //    return $this->redirectToRoute('player_invite');
        }
        // Retrieve everyone's history with pseudos
        $queryNbGames = "SELECT u.pseudo, u.nb_games_played, u.total_time_played, u.nb_points FROM user u WHERE 1";
        $statement = $connection->prepare($queryNbGames);
        $statement->execute();
        $ranking = $statement->fetchAll();
        dump($ranking);
        
        //    $message = $translator->trans('HomeController');
        return $this->render('index/index.html.twig', [
        //    'controller_name' => $message,
            'invitation' => $invitation,
            'numGame' => $numGame,
            'ranking' => $ranking,
        ]);
    }
}
