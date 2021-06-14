<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TranslatorInterface $translator): Response
    {
        $message = $translator->trans('HomeController');

        return $this->render('index/index.html.twig', [
            'controller_name' => $message,
        ]);
    }
}
