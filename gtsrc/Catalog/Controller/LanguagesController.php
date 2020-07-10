<?php


namespace Gt\Catalog\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Form\LanguageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LanguagesController extends AbstractController
{

    /**
     * @todo pakurti roles, pvz: ROLE_ADMIN_LANGUAGE
     *
     * @Route("/languages/new", name="language_new")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(LanguageFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('@Catalog/languages/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }
}
