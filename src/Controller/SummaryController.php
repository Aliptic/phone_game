<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\History;
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
        
        $playerIds = $game->getUsersId();
        
        if($game->getState()=='Ongoing'){
            $game->setState('Finished');
            $gamelength = time() - $game->getTime();

            $game->setTime($gamelength);
            $entityManager->persist($game);
        }

        // get histories
        $histories = $this->getDoctrine()
            ->getRepository(History::class)
            ->findBy(['game_id' => $id]);

        $summaries = [];
        foreach($histories as $h){
            $summaries [] = $h->getHistory();
        }
        

        // on verifie que l'on a pas déjà voté pour changer l'affichage
        $hasVoted = 0;
        $history = $this->getDoctrine()
            ->getRepository(history::class)
            ->findOneBy(['game_id' => $id , 'user_id' => $this->getUser()->getId()]);
        if($history->getHasVoted()==1) {
            $hasVoted = 1;
        } else {
            $this->getUser()->setTotalTimePlayed($this->getUser()->getTotalTimePlayed() + $game->getTime());
            $this->getUser()->setNbGamesPlayed($this->getUser()->getNbGamesPlayed()+1);

            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }

        $sentenceVote = $game->getVoteSentence();
        if($sentenceVote == NULL){
            $sentenceVote = "Qui a été le meilleur déssinateur?";
        }

        return $this->render('summary/index.html.twig', [
            'game_id' => $id,
            'summaries' => $summaries,
            'ids' => $playerIds,
            'myId' => $this->getUser()->getId(),
            'hasVoted' => $hasVoted,
            'sentenceVote' => $sentenceVote,
        ]);
    }

    /**
     * @Route("/history/{id}", name="history")
     */
    public function history(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $connection = $entityManager->getConnection();
        
        // get histories
        $histories = $this->getDoctrine()
            ->getRepository(History::class)
            ->findBy(['game_id' => $id]);

        $summaries = [];
        foreach($histories as $h){
            $summaries [] = $h->getHistory();
        }

        return $this->render('summary/history.html.twig', [
            'controller_name' => 'Summary',
            'game_id' => $id,
            'player_id' => $this->getUser()->getId(),
            'summaries' => $summaries,
        ]);
    }

    /**
     * @Route("/votefor/{id}/{gameId}", name="votefor")
     */
    public function voteFor(int $id, int $gameId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $history = $this->getDoctrine()
            ->getRepository(history::class)
            ->findOneBy(['game_id' => $gameId , 'user_id' => $this->getUser()->getId()]);
        
        // si le user n'a pas participé à cette game
        if($history != null){
            // si le joueur n'a pas déjà voté
            if($history->getHasVoted()==0){
                $player = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->findOneBy(['id' => $id]);
                
                // fait voter le joueur
                $player->setNbPoints($player->getNbPoints()+1);
                $entityManager->persist($player);

                // marque le joueur comme ayant voté
                $history->SetHasVoted(1);
                $entityManager->flush();
                
                return $this->redirectToRoute('summary', array(
                    'id' => $gameId,
                ));
            }
            else{
                return $this->redirectToRoute('summary', array(
                    'id' => $gameId,
                ));
            }
        }
        else{
            return $this->redirectToRoute('index', array());
        }
    }
}
