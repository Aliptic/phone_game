<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SummaryController extends AbstractController
{
    /**
     * @Route("/summary/{id}", name="summary")
     */
    public function index(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // database query for Game table with id filter
        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->findOneBy(['id' => $id]);

        // retrieve the players list with ids and pseudos
        $playersArray = $game->getUsersId();
            
        // update the state of the game to finished
        $game->setState('Finished');
        $entityManager->persist($game);
        
        // Retrieve everyone's history
        $query = "SELECT user_id, history  FROM history h WHERE game_id = ".$id;
        $statement = $connection->prepare($query);
        $statement->execute();
        $histories = $statement->fetchAll();

        return $this->render('summary/index.html.twig', [
            'controller_name' => 'Summary',
            'game_id' => $id,
        ]);
    }
}
