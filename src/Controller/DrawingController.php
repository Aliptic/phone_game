<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\History;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DrawingController extends AbstractController
{
    /**
     * @Route("/drawing/{id}", name="drawing")
     */
    public function index(int $id): Response
    {   
        // on cherche à retrouver quel joueur est "l'adversaire" de notre joueur
        $game=$this->getDoctrine()
            ->getRepository(Game::class)
            ->findOneBy(array('id' => $id));
        
        $playersList=$game->getUsersId();

        //position du joueur dans le classment des joueurs de cette partie
        $myPosition=array_search($this->getUser()->getId(), array_column($playersList, '0'));

        //la position de l'adversaire est la notre -1 sauf si on est le premier de la liste
        if($myPosition == 0){
            $opponentPosition = count($playersList)-1;
        }else{
            $opponentPosition = $myPosition-1;
        }
        
        //l'id de notre adversaire que l'on va chercher dans les History
        $opponentId=$playersList[$opponentPosition][0];
        $historyOpponent=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(array('game_id' => $id,'user_id' => $opponentId));
        
        //on retrouve finalement la bonne phrase à afficher
        $size=count($historyOpponent->getHistory());
        //on prend size-1 pour retomber sur la bonne phrase au cas où il s'agit d'une deuxieme phase de dessin
        $sentenceOpponent=$historyOpponent->getHistory()[$size-1];
        
        dump($sentenceOpponent);

        $formDraw = $this->createFormBuilder()
            ->add('Validate', SubmitType::class, ['label' => 'Validate'])
            ->setMethod('POST')
            ->getForm();
        $formDraw->handleRequest($request);

        return $this->render('drawing/index.html.twig', [
        // return $this->render('drawing/index.html', [
            'controller_name' => 'DrawingController',
            'sentence' => $sentenceOpponent,
            'formDraw' => $formDraw->createView(),
        ]);
    }
}
