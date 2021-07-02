<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\StaticPage;
use App\Entity\Sentence;
use App\Repository\UserRepository;
use App\Repository\StaticPageRepository;
use App\Form\StaticEditForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Knp\Component\Pager\PaginatorInterface;

class AdminPanelController extends AbstractController
{
    /**
     * @Route("/adminpanel", name="admin_panel", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminPanel(Request $request, StaticPageRepository $pagesRepository, PaginatorInterface $paginator): Response
    {
        // New form et appel au formBuilder
        // TODO:Voir pour améliorer
        $formPages = $this->createFormBuilder()
            ->add('title', EntityType::class, [
                'class' => StaticPage::class,
                'choices' => $pagesRepository->findAll(),
                'choice_label' => 'title',
            ])
            ->add('edit', SubmitType::class, ['label' => 'edit'])
            ->setMethod('POST')
            ->getForm();

        $formPages->handleRequest($request);

        // récupération de la page à éditer et renvoi vers l'éditeur
        if ($formPages->isSubmitted()) {
            // TODO:ameliorer
            $page = $formPages->get('title')->getData();
            $id = $page->getId();

            return $this->redirectToRoute('static_edit', array(
                'id' => $id,
            ));
        }

        $formSentence = $this->createFormBuilder()
            ->add('phrase', TextType::class, [
                'label' => 'phrase',
                'attr' => [
                    'placeholder' => "Une jambe en mousse",
                ]])
            ->add('type', ChoiceType::class, [
                    'choices'  => [
                        'start' => 'start',
                        'vote' => 'vote',
                    ],
                ])
            ->add('validate', SubmitType::class, ['label' => 'Validate'])
            ->setMethod('GET')
            ->getForm();

        $formSentence->handleRequest($request);

        if ($formSentence->isSubmitted()) {
            $phrase = $formSentence->get('phrase')->getData();
            $type = $formSentence->get('type')->getData();

            $sentence = new Sentence();
            $sentence->setSentence($phrase);
            $sentence->setType($type);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sentence);
            $entityManager->flush();

            return $this->redirectToRoute('admin_panel');
        }

        // Generation d'une page avec seulement n résultats et un numpage
        $donnees = $this->getDoctrine()->getRepository(USer::class)->findAll();

        $users = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('admin_panel/index.html.twig', [
            'users' => $users,
            'formPages' => $formPages->createView(),
            'formSentence' => $formSentence->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit-static", name="static_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editStatic(Request $request, StaticPage $page): Response
    {
        $formPages = $this->createForm(StaticEditForm::class, $page);

        $formPages->handleRequest($request);

        if ($formPages->isSubmitted() && $formPages->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_panel');
        }

        return $this->render('admin_panel/edit_static.html.twig', [
            'formPages' => $formPages->createView(),
        ]);
    }
}
