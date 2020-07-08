<?php


namespace Gt\Catalog\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Form\LanguageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguagesController extends AbstractController
{

    public function newAction(EntityManagerInterface $em)
    {
        $form = $this->createForm(LanguageFormType::class);

        return $this->render('@Catalog/languages/languages.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }
}
