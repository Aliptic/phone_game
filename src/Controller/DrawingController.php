<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DrawingController extends AbstractController
{
    /**
     * @Route("/drawing", name="drawing")
     */
    public function index(): Response
    {
        return $this->render('drawing/index.html.twig', [
            'controller_name' => 'DrawingController',
        ]);
    }
}
