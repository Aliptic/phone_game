<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextController extends AbstractController
{
    /**
     * @Route("/start", name="start")
     */
    public function start(Request $request): Response
    {
        $formStart = $this->createFormBuilder()
            ->add('phrase', TextType::class)
            ->add('Validate', SubmitType::class, ['label' => 'edit'])
            ->setMethod('POST')
            ->getForm();

        $formStart->handleRequest($request);

        if ($formStart->isSubmitted()) {
            $phrase = $formStart->get('phrase')->getData();
            dump($phrase);
        }

        return $this->render('text/start.html.twig', [
            'formStart' => $formStart->createView(),
        ]);
    }
}
