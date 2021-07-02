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
        
        $invitation = 0;
        $numGame = 0;
        
        if($this->get('session')->get('token'))
        {   
            $token = $this->get('session')->get('token');
            // database query with token filter
            $game = $this->getDoctrine()
                ->getRepository(Game::class)
                ->findOneBy(['room_token' => $token]);
              
            // verify if a token exist in base, if not, the player is warned
            if (!$game) {
                // delete the session variable token because it's useless now
                $this->get('session')->set('token', NULL);
            }    
            $numGame = $game->getId();
            $invitation = 1;
        //    return $this->redirectToRoute('player_invite');
        }
        
        //    $message = $translator->trans('HomeController');
        return $this->render('index/index.html.twig', [
        //    'controller_name' => $message,
            'invitation' => $invitation,
            'numGame' => $numGame,
        ]);
    }
}
