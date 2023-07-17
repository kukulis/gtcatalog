<?php


namespace Gt\Catalog\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Form\LanguageFormType;
use Gt\Catalog\Services\LanguagesService;
use Gt\Catalog\Services\TableService;
use Gt\Catalog\TableData\LanguagesTableData;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguagesController extends AbstractController
{

    private $tableService;
    private $tableData;

    public function __construct(TableService $tableService, LanguagesTableData $tableData)
    {
        $this->tableService = $tableService;
        $this->tableData = $tableData;
    }
    /**
     * @todo pakurti roles, pvz: ROLE_ADMIN_LANGUAGE
     *
     * @Route("/languages/new", name="language_new")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
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
     * @return Response
     */
    public function listAction(Request $request, LoggerInterface $logger, LanguagesService $languagesService)
    {
        $logger->info('Languages list action called');

        $page = $request->get('page', 0);

        $languages = $languagesService->getLanguages($page);

        $tableData = $this->tableData->getTableData($languages);

        $tableHtml = $this->tableService->generateTableHtml(
            $tableData->getRows(),
            $tableData->getColumns(),
            $tableData->getTableOptions(),
        );

        return $this->render('@Catalog/languages/list.html.twig', [
            'tableHtml' => $tableHtml
        ]);
    }

    /**
     * @Route("/languages/{code}/edit", name="language_edit")
     * @param Language $language
     * @param Request $request
     * @param LanguagesService $languagesService
     * @return RedirectResponse|Response
     */
    public function editAction(Language $language, Request $request, LanguagesService $languagesService)
    {
        $form = $this->createForm(LanguageFormType::class, $language);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $languagesService->newLanguage($form);
            return $this->redirectToRoute('gt.catalog.languages');
        }

        return $this->render('@Catalog/languages/edit.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }
}
