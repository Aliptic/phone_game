<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\History;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class DrawingController extends AbstractController
{
    /**
     * @Route("/drawing/{id}", name="drawing")
     */
    public function index(Request $request, int $id): Response
    {   
        $entityManager = $this->getDoctrine()->getManager();

        // on cherche à retrouver quel joueur est "l'adversaire" de notre joueur
        $game=$this->getDoctrine()
            ->getRepository(Game::class)
            ->findOneBy(array('id' => $id));
        
        $playersList=$game->getUsersId();
        dump($playersList);

        //position du joueur dans le classement des joueurs de cette partie
        $myPosition=array_search($this->getUser()->getId(), array_column($playersList, '0'));

        $myHistory=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(array('game_id' => $id,'user_id' => $this->getUser()->getId()));
        // manche actuelle
        //$round=count($myHistory->getHistory())+1;
        $round=2;
        // 
        if(($myPosition - $round)+1<0){
            $offset=$round-($myPosition+1);
            dump("j'ai besoin d'un offset de: ".$offset);
            $opponentPosition=count($playersList)-$offset;
        }else{
            $opponentPosition = ($myPosition - $round)+1;
        }
        dump("mypos ".$myPosition." round".$round);
        dump("oppos".$opponentPosition);
        //l'id de notre adversaire que l'on va chercher dans les History
        $opponentId=$playersList[$opponentPosition][0];
        $historyOpponent=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(array('game_id' => $id,'user_id' => $opponentId));
        dump("opponent id: ".$opponentId." pseudo: ".$playersList[$opponentPosition][1]);
        //on retrouve finalement la bonne phrase à afficher
        $size=count($historyOpponent->getHistory());
        //on prend size-1 pour retomber sur la bonne phrase au cas où il s'agit d'une deuxieme phase de dessin
        $sentenceOpponent=$historyOpponent->getHistory()[$size-1];

        $formDraw = $this->createFormBuilder()
            ->add('validate', SubmitType::class, ['label' => 'Validate'])
            ->add('hidden', HiddenType::class)
            ->setMethod('POST')
            ->getForm();
        $formDraw->handleRequest($request);

        if ($formDraw->isSubmitted()) {
            // récuperation du dessin caché dans le form
            $drawing = $formDraw->get('hidden')->getData();

            // une façon moins moche existe surement, pas le temps déso pas déso
            $newhistory=$historyOpponent->getHistory();
            array_push($newhistory,$drawing);
            $historyOpponent->setHistory($newhistory);

            $entityManager->flush();
        }

        return $this->render('drawing/index.html.twig', [
        // return $this->render('drawing/index.html', [
            'controller_name' => 'DrawingController',
            'sentence' => $sentenceOpponent,
            'formDraw' => $formDraw->createView(),
        ]);
    }
}
