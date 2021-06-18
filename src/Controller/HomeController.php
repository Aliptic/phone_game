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
        if($this->get('session')->get('token'))
        {
            return $this->redirectToRoute('player_invite');
        }
    /*    elseif($this->getUser()->getId())
        {
            $player = $this->getUser()->getId();

        }
    */
        else 
        {
        //    $message = $translator->trans('HomeController');
            
            return $this->render('index/index.html.twig', [
        //    'controller_name' => $message,
            ]);
        }
    }
}
