<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\History;
use App\Entity\Sentence;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TextController extends AbstractController
{
    /**
     * @Route("/start/{id}", name="start")
     */
    public function start(Request $request, int $id, HubInterface $hub ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        //get a new sentence as placeholder for the textField
        $connection = $entityManager->getConnection();
        $statement = $connection->prepare('SELECT sentence FROM sentence s ORDER BY RAND() LIMIT 1');
        $statement->execute();
        $phrasePlaceholder = $statement->fetch();
    //    dump($phrasePlaceholder);

        //TODO:mettre le placeholder en gris si possible
        $formStart = $this->createFormBuilder()
            ->add('phrase', TextType::class, [
                'label' => 'phrase',
                'attr' => [
                    'placeholder' => $phrasePlaceholder["sentence"],
                ]])
            ->add('Validate', SubmitType::class, ['label' => 'Validate'])
            ->setMethod('POST')
            ->getForm();

        $formStart->handleRequest($request);

        if ($formStart->isSubmitted()) {
            $phrase = $formStart->get('phrase')->getData();
        //    dump($phrase);

            $history=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(['game_id' => $id,'user_id' => $this->getUser()->getId()]);

            $history->setHistory([$phrase]);

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
                $url = 'http://localhost:8080/start/'.$id;
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
                ]);
            }
            
        }

        return $this->render('text/start.html.twig', [
            'formStart' => $formStart->createView(),
            'game_id' => $id,
            'waiting' => '0',
        ]);
    }
}
