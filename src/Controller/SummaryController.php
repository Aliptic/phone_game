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
        $connection = $entityManager->getConnection();
        
        // database query for Game table with id filter
        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->findOneBy(['id' => $id]);

        // update the state of the game to finished
        $game->setState('Finished');
        $entityManager->persist($game);
        
        // Retrieve everyone's history with pseudos
        $query = "SELECT u.pseudo, h.history FROM history h INNER JOIN user u WHERE h.user_id = u.id AND h.game_id = ".$id;
        $statement = $connection->prepare($query);
        $statement->execute();
        $summaries = $statement->fetchAll();
    //    dump($summaries);
        
        // decode json to array before passing it to twig
        for($i=0;$i < count($summaries); $i++) {
            $summaries[$i]['history'] = json_decode($summaries[$i]['history'], true);
            dump($summaries[$i]['history']);
        }

        return $this->render('summary/index.html.twig', [
            'controller_name' => 'Summary',
            'game_id' => $id,
            'summaries' => $summaries,
        ]);
    }
}
