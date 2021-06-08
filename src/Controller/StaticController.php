<?php

namespace App\Controller;

use App\Entity\StaticPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    /**
     * @Route("/mentions", name="mentions")
     */
    public function indexMentions(): Response
    {
        $mentions = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBy(['title' => 'mentions']);
        
        return $this->render('mentions/index.html.twig', [
            'mentions' => $mentions,
        ]);
    }

    /**
     * @Route("/rules", name="rules")
     */
    public function indexRegles(): Response
    {
        $rules = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBy(['title' => 'rules']);

        return $this->render('rules/index.html.twig', [
            'rules' => $rules,
        ]);
    }

    /**
     * @Route("/team", name="team")
     */
    public function indexTeam(): Response
    {
        $team = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBy(['title' => 'team']);

        return $this->render('team/index.html.twig', [
            'team' => $team,
        ]);
    }
}
