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
        // Vérification de l'éxistence de la table static_page
        if ($this->getDoctrine()->getConnection()->getSchemaManager()->tablesExist(array('static_page')) == true) {
            $mentions = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBy(['title' => 'mentions']);

            return $this->render('mentions/index.html.twig', [
                'mentions' => $mentions,
            ]);
        }
        
        else{
            return $this->render('mentions/index.html.twig', [
                'mentions' => null,
            ]);
        }

    }

    /**
     * @Route("/rules", name="rules")
     */
    public function indexRegles(): Response
    {
        // Vérification de l'éxistence de la table static_page
        if ($this->getDoctrine()->getConnection()->getSchemaManager()->tablesExist(array('static_page')) == true) {
            $rules = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBy(['title' => 'rules']);

            return $this->render('rules/index.html.twig', [
                'rules' => $rules,
            ]);
        }
        else{
            return $this->render('rules/index.html.twig', [
                'rules' => null,
            ]);
        }
    }

    /**
     * @Route("/team", name="team")
     */
    public function indexTeam(): Response
    {
        // Vérification de l'éxistence de la table static_page
        if ($this->getDoctrine()->getConnection()->getSchemaManager()->tablesExist(array('static_page')) == true) {
            $team = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBy(['title' => 'team']);

            return $this->render('team/index.html.twig', [
                'team' => $team,
            ]);
        }
        else{
            return $this->render('team/index.html.twig', [
                'team' => null,
            ]);
        }
    }
}
