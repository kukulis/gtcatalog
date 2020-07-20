<?php


namespace Gt\Catalog\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Form\LanguageFormType;
use Gt\Catalog\Services\LanguagesService;
use Psr\Log\LoggerInterface;
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
    public function newAction( Request $request, LanguagesService $languagesService)
    {
        $form = $this->createForm(LanguageFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $languagesService->newLanguage($form);
            return $this->redirectToRoute('gt.catalog.languages');
        }

        return $this->render('@Catalog/languages/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @param LanguagesService $languagesService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, LoggerInterface $logger, LanguagesService $languagesService)
    {
        $logger->info('Languages list action called');

        $page = $request->get('page', 0);

        $languages = $languagesService->getLanguages($page);

        return $this->render('@Catalog/languages/list.html.twig', [
            'languages' => $languages
        ]);
    }

    /**
     * @Route("/languages/{code}/edit", name="language_edit")
     * @param Language $language
     * @param Request $request
     * @param LanguagesService $languagesService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Language $language, Request $request, LanguagesService $languagesService)
    {
        $form = $this->createForm(LanguageFormType::class, $language);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $languagesService->newLanguage($form);
            return $this->redirectToRoute('gt.catalog.languages');
        }

        return $this->render('@Catalog/languages/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }
}
