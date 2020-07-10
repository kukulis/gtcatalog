<?php


namespace Gt\Catalog\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Entity\Language;
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
            $data = $form->getData();
            $language = new Language();
            $language->setCode($data['code']);
            $language->setName($data['name']);
            $language->setLocaleCode($data['localeCode']);

            $em->persist($language);
            $em->flush();
        }

        return $this->render('@Catalog/languages/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }

    public function languages()
    {
        
    }
}
