<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\History;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DrawingController extends AbstractController
{
    /**
     * @Route("/drawing/{id}", name="drawing")
     */
    public function index(Request $request, int $id, HubInterface $hub): Response
    {
        // On vérifie que l'on ne vient pas du formulaire de cette même page
        $round = $this->get('session')->get('step');
        if(!isset($_POST['form'])){
            $round ++;
            $this->get('session')->set('step', $round);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $connection = $entityManager->getConnection();

        // Count how many players are in this game
        $query = "SELECT COUNT(*) FROM history WHERE game_id =".$id;
        $statement = $connection->prepare($query);
        $statement->execute();
        $tabNbPlayers = $statement->fetch();

        // Convert the array to string to number
        $nbPlayers = intval(implode(" ",$tabNbPlayers));

        // Check if all the steps have been passed
        if($round > $nbPlayers) {
            return $this->redirectToRoute('summary', array(
                'id' => $id,
            ));
        }

        // on cherche à retrouver quel joueur est "l'adversaire" de notre joueur
        $game=$this->getDoctrine()
            ->getRepository(Game::class)
            ->findOneBy(array('id' => $id));

        $playersList=$game->getUsersId();

        //position du joueur dans le classement des joueurs de cette partie
        $myPosition=array_search($this->getUser()->getId(), array_column($playersList, '0'));

        // ATTENTION: toujours en phase de test pour l'instant
        if(($myPosition - $round)+1<0){
            $offset=$round-($myPosition+1);

            $opponentPosition=count($playersList)-$offset;
        }else{
            $opponentPosition = ($myPosition - $round)+1;
        }

        // l'id de notre adversaire que l'on va chercher dans les History
        $opponentId=$playersList[$opponentPosition][0];
        $historyOpponent=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(array('game_id' => $id,'user_id' => $opponentId));

        // on retrouve finalement la bonne phrase à afficher
        $size=count($historyOpponent->getHistory());
        // on prend size-1 pour retomber sur la bonne phrase au cas où il s'agit d'une deuxieme phase de dessin
        $sentenceOpponent=$historyOpponent->getHistory()[$size-1]["1"];

        if(isset($_POST['form']) && $size>=$round){
            $sentenceOpponent=$historyOpponent->getHistory()[$round]["1"];
        }

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
            $newhistory[$round-1]=["0" => $this->getUser()->getPseudo(), "1" => $drawing];
            $historyOpponent->setHistory($newhistory);

            $entityManager->flush();

            //Check that everyone has filled in their history
            $query = "SELECT history FROM history h WHERE game_id = ".$id;
            $statement = $connection->prepare($query);
            $statement->execute();
            $histories = $statement->fetchAll();

            $vide = 0;
            foreach($histories as $h) {
                $hArray = json_decode($h['history'], true);

                if(!isset($hArray[$round-1])){
                    $vide++;
                }
            }

            // if all players have validated this step
            if($vide == 0) {
                // new sse update to send to drawing
                $url = $this->getParameter('mercure.host').'drawing/'.$id;
                $update = new Update(
                    $url,
                    json_encode(array('subject' => 'text',))
                );
                $hub->publish($update);

                return $this->redirectToRoute('text',[
                    "id" => $id,
                ]);
            } else {
                // if a player has not still validated, it warns the other players
                return $this->render('drawing/index.html.twig', [
                    'game_id' => $id,
                    'sentence' => $sentenceOpponent,
                    'formDraw' => $formDraw->createView(),
                    'waiting' => '1',
                    'drawing' => $drawing,
                ]);
            }
        }

        return $this->render('drawing/index.html.twig', [
            'game_id' => $id,
            'sentence' => $sentenceOpponent,
            'formDraw' => $formDraw->createView(),
            'waiting' => '0',
        ]);
    }
}
