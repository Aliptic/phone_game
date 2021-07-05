<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UserRepository $userRepository): Response
    {
        // declare some variables to 0 when a player arrive for the first time
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
        }
        // Retrieve everyone's stats ordered by nb games playes, nb points and total time played   
        $ranking=array(
            array('GamesDesc',$userRepository->rank('nb_games_played','DESC')),
            array('GamesAsc',$userRepository->rank('nb_games_played','ASC')),
            array('PointsDesc',$userRepository->rank('nb_points','DESC')),
            array('PointsAsc',$userRepository->rank('nb_points','ASC')),
            array('TimeDesc',$userRepository->rank('total_time_played','DESC')),
            array('TimeAsc',$userRepository->rank('total_time_played','ASC'))
        );
        
        return $this->render('index/index.html.twig', [
            'invitation' => $invitation,
            'numGame' => $numGame,
            'ranking' => $ranking,
        ]);
    }
}
