<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Sentence;
use App\Entity\History;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextController extends AbstractController
{
    /**
     * @Route("/start/{id}", name="start")
     */
    public function start(Request $request, int $id ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        //get a new sentence as placeholder for the textField
        $connection = $entityManager->getConnection();
        $statement = $connection->prepare('SELECT sentence FROM sentence s ORDER BY RAND() LIMIT 1');
        $statement->execute();
        $phrasePlaceholder = $statement->fetch();
        dump($phrasePlaceholder);

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
            dump($phrase);

            $history=$this->getDoctrine()
            ->getRepository(History::class)
            ->findOneBy(['game_id' => $id,'user_id' => $this->getUser()->getId()]);

            $history->setHistory([$phrase]);

            $entityManager->flush();

            // une nouvelle update
        }

        return $this->render('text/start.html.twig', [
            'formStart' => $formStart->createView(),
        ]);
    }
}
