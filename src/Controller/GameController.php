<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\History;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    /**
     * @Route("/game/{id}", name="game")
     */
    public function index(int $id, HubInterface $hub): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // database query for Game table with id filter
        $game = $this->getDoctrine()
        ->getRepository(Game::class)
        ->findOneBy(['id' => $id]);

        // verify if an id exist in base, if not, the player is warned
        if (!$game) {
            $message = $this->translator->trans('Error verifying game id');
            throw $this->createNotFoundException($message);
        }
        
        // Retrieve an array of users id and pseudo
        $players = $game->getUsersId();
       
        // Add a game id and empty array in history for each player
        foreach($players as $player) {
            $history = new History();
            $history->setGameId($id)
                ->setUserId($player[0])
                ->setHistory([]);
            $entityManager->persist($history);
        }
        $entityManager->flush();
        
        // redirect to text controller
    //    return $this->redirectToRoute('text');
          
        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
            'game_id' => $id,
        ]);
    }
}
