<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\StaticPageRepository;
use App\Form\StaticPagesAdminForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminPanelController extends AbstractController
{
    /**
     * @Route("/adminpanel", name="admin_panel", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminPanel(Request $request, UserRepository $userRepository, StaticPageRepository $pagesRepository): Response
    {
        $formPages = $this->createForm(StaticPagesAdminForm::class, $pagesRepository);

        $formPages->handleRequest($request);

        if ($formPages->isSubmitted() && $formPages->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin_panel/index.html.twig', [
            'users' => $userRepository->findAll(),
            'pages' => $pagesRepository->findAll(),
        ]);

    }
}
