<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\History;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TextController extends AbstractController
{
    /**
     * @Route("/start/{id}", name="start")
     */
    public function start(Request $request, int $id, HubInterface $hub, TranslatorInterface $translator ): Response
    {
        // pass the number of step in session
        $this->get('session')->set('step', "1");

        // retrieves the time defined for each step
        $timer = $this->get('session')->get('timer');

        $entityManager = $this->getDoctrine()->getManager();

        //get a new sentence as placeholder for the textField
        $connection = $entityManager->getConnection();
        $statement = $connection->prepare('SELECT sentence FROM sentence s WHERE type = "start" ORDER BY RAND() LIMIT 1');
        $statement->execute();
        $phrasePlaceholder = $statement->fetch();

        $formStart = $this->createFormBuilder()
            ->add('phrase', TextType::class, [
                'label' => 'phrase',
                'attr' => [
                    'placeholder' => $phrasePlaceholder["sentence"],
                ]])
            ->add('validate', SubmitType::class, ['label' => 'Validate'])
            ->setMethod('POST')
            ->getForm();

        $formStart->handleRequest($request);

        if ($formStart->isSubmitted()) {
            $phrase = $formStart->get('phrase')->getData();
            if( is_null($phrase) ) {
                $phrase = $translator->trans("Sorry, the player did not have time to enter a sentence");
            } else {
                $phrase = $formStart->get('phrase')->getData();
            }

            $history=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(['game_id' => $id,'user_id' => $this->getUser()->getId()]);

            $history->setHistory(["0"=>["0" => $this->getUser()->getPseudo(), "1" => $phrase]]);
            
            $entityManager->flush();

            //Check that everyone has filled in their history
            $query = "SELECT history FROM history h WHERE game_id = ".$id;
            $statement = $connection->prepare($query);
            $statement->execute();
            $histories = $statement->fetchAll();  

            $vide = 0;
            foreach($histories as $tab) {
                if($tab['history'] == "[]"){
                    $vide++;
                }
            }

            // if all players have validated this step
            if($vide == 0) {
                // new sse update to send to drawing
                $url = $this->getParameter('mercure.host').'start/'.$id;
                $update = new Update(
                    $url,
                    json_encode(array('subject' => 'draw',))
                );
                $hub->publish($update);
                
                //in case of bug, it forces the page to redirect to the next step of the game
                return $this->redirectToRoute('drawing',[
                    "id" => $id,
                ]);
            } else {    // if a player has not still validated, it warns the other players
                return $this->render('text/start.html.twig', [
                    'formStart' => $formStart->createView(),
                    'game_id' => $id,
                    'waiting' => '1',
                    'timer' => -1,
                ]);
            }
            
        }

        return $this->render('text/start.html.twig', [
            'formStart' => $formStart->createView(),
            'game_id' => $id,
            'waiting' => '0',
            'timer' => $timer,
        ]);
    }

    /**
     * @Route("/text/{id}", name="text")
     */
    public function text(Request $request, int $id, HubInterface $hub ): Response
    {
        $round = $this->get('session')->get('step');
        if(!isset($_POST['form'])){
            $round ++;
            $this->get('session')->set('step', $round);
        }
        
        // retrieves the time defined for each step
        $timer = $this->get('session')->get('timer');
        
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
        
        // Retrieve the sorted player list
        $game=$this->getDoctrine()
            ->getRepository(Game::class)
            ->findOneBy(array('id' => $id));
        
        $playersList=$game->getUsersId();

        // The player's position in the ranking of players in this game
        $myPosition=array_search($this->getUser()->getId(), array_column($playersList, '0'));

        // Find the position of the creator in the player list
        if(($myPosition - $round)+1 < 0) {
            $offset = $round - ($myPosition + 1);
            $creatorPosition = count($playersList) - $offset;
        } else {
            $creatorPosition = ($myPosition - $round) + 1;
        }
        
        // id of the creator that we will look for in History
        $creatorId=$playersList[$creatorPosition][0];
        $historyCreator=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(array('game_id' => $id,'user_id' => $creatorId));
        
        // find the correct drawing to display
        // if we have already submited, we display the second last entry
        if(!isset($_POST['form'])){
            $drawCreator=$historyCreator->getHistory()[count($historyCreator->getHistory())-1]["1"];
        } else {
            $drawCreator=$historyCreator->getHistory()[$round-2]["1"];
        }
        
        $formText = $this->createFormBuilder()
            ->add('phrase', TextType::class, ['label' => 'phrase'])
            ->add('validate', SubmitType::class, ['label' => 'validate'])
            ->setMethod('POST')
            ->getForm();
        
        $formText->handleRequest($request);

        if ($formText->isSubmitted()) {
            $phrase = $formText->get('phrase')->getData();

            $history=$this->getDoctrine()
                ->getRepository(History::class)
                ->findOneBy(['game_id' => $id,'user_id' => $creatorId]);

            $newhistory=$history->getHistory();
            $newhistory[$round-1]=["0" => $this->getUser()->getPseudo(), "1" => $phrase];
            $history->setHistory($newhistory);

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
                $url = $this->getParameter('mercure.host').'text/'.$id;
                $update = new Update(
                    $url,
                    json_encode(array('subject' => 'draw',))
                );
                $hub->publish($update);
                
                //in case of bug, it forces the page to redirect to the next step of the game
                return $this->redirectToRoute('drawing',[
                    "id" => $id,
                ]);
            }     
            
            // if a player has not still validated, it warns the other players
            return $this->render('text/text.html.twig', [
                'drawing' => $drawCreator,
                'formText' => $formText->createView(),
                'game_id' => $id,
                'waiting' => '1',
                'timer' => $timer,
            ]);   
        }
        return $this->render('text/text.html.twig', [
                'drawing' => $drawCreator,
                'formText' => $formText->createView(),
                'game_id' => $id,
                'waiting' => '0',
                'timer' => $timer,
        ]);
    }
}
