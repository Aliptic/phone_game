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
        $token = random_bytes(5); // Generate random token for a game


    /*    $game = new Game();   // 
        $game->setUsersId()
    */
        return $this->render('player_invite/index.html.twig', [
            'controller_name' => 'PlayerInviteController',
            'token' => bin2hex($token),
        ]);
    }
}
