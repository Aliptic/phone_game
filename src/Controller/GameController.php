<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    /**
     * @Route("/game/{id}", name="game")
     */
    public function index(int $id): Response
    {
        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
            'game_id' => $id,
        ]);
    }
}
