<?php


namespace Gt\Catalog\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Form\LanguageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguagesController extends AbstractController
{

    /**
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(EntityManagerInterface $em)
    {
        $form = $this->createForm(LanguageFormType::class);

        return $this->render('@Catalog/languages/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }
}
