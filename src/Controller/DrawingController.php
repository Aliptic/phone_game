<?php

namespace App\Controller;

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
        return $this->render('drawing/index.html.twig', [
        // return $this->render('drawing/index.html', [
            'controller_name' => 'DrawingController',
        ]);
    }
}
