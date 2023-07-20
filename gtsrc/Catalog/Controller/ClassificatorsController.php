<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 07.30
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\ClassificatorFormType;
use Gt\Catalog\Form\ClassificatorsListFilterType;
use Gt\Catalog\Services\ClassificatorsService;
use Gt\Catalog\Services\LanguagesService;
use Gt\Catalog\Services\TableService;
use Gt\Catalog\TableData\ClassificatorsTableData;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassificatorsController extends AbstractController
{
    private $tableService;
    private $tableData;

    public function __construct(TableService $tableService, ClassificatorsTableData $tableData)
    {
        $this->tableService = $tableService;
        $this->tableData = $tableData;
    }
    public function listAction(Request $request, LoggerInterface $logger, ClassificatorsService $classificatorsService) {

        $classificatorsFilter = new ClassificatorsListFilterType();

        $groups = $classificatorsService->getAllGroups();
        $classificatorsFilter->setAvailableGroups( $groups );
        $form = $this->createForm(ClassificatorsListFilterType::class, $classificatorsFilter);

        $form->handleRequest($request);

        $languageCode = 'en';
        if ( !empty( $classificatorsFilter->getLanguage() )  ) {
            $languageCode = $classificatorsFilter->getLanguage()->getCode();
        }

        $classificators = $classificatorsService->searchClassificators ( $classificatorsFilter );
        $classificatorsService->assignValues($classificators, $languageCode);

        $tableData = $this->tableData->getTableData($classificators);

        $tableHtml = $this->tableService->generateTableHtml(
            $tableData->getRows(),
            $tableData->getColumns(),
            $tableData->getTableOptions(),
            $languageCode,
        );

        return $this->render('@Catalog/classificators/list.html.twig', [
            'tableHtml' => $tableHtml,
            'filterForm' => $form->createView(),
            'languageCode' => $languageCode,
            'isFilterFormSubmitted' => $form->isSubmitted()
        ]);

    }

    public function importFormAction(Request $request, LanguagesService $languagesService) {

        $languages = $languagesService->getLanguages();
        return $this->render('@Catalog/classificators/import_form.html.twig', [
            'languages' => $languages,
        ]);
    }

    /**
     * @param Request $request
     * @param ClassificatorsService $classificatorsService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request, ClassificatorsService $classificatorsService) {
        /** @var UploadedFile $file */
        $file = $request->files->get('csvfile' );

        try {
            if ( empty($file) ) {
                return $this->render('@Catalog/error/error.html.twig', [
                    'error'=> 'Nepaduotas csv failas',
                ]);
            }

            $count = $classificatorsService->importClassificators($file->getRealPath());
            return $this->render('@Catalog/classificators/import_results.html.twig',
                [
                    'count' => $count,
                ]
            );
        } catch (CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error'=> $e->getMessage(),
            ]);
        }
        catch ( CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error'=> 'Validavimo klaida:' . $e->getMessage(),
            ]);
        }

    }

    /**
     * @Route("/classificators/{code}/edit", name="classificators_edit")
     * @param Classificator $classificator
     * @param Request $request
     * @param ClassificatorsService $classificatorsService
     * @return RedirectResponse|Response
     */
    public function editAction($code, $languageCode, Request $request, ClassificatorsService $classificatorsService)
    {
        try {
            $allLanguages = $classificatorsService->getAllLanguages();

            $classificatorLanguage = $classificatorsService->loadClassificatorLanguage($code, $languageCode);

            $classificatorFormType = new ClassificatorFormType();
            $groups = $classificatorsService->getAllGroups();
            $classificatorFormType->setAvailableGroups($groups);
            $classificatorFormType->setClassificatorLanguage($classificatorLanguage);

            $form = $this->createForm(ClassificatorFormType::class, $classificatorFormType);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var SubmitButton $save */
                $save = $form->get('save');

                if ($save->isClicked()) {
                    $classificatorsService->storeClassificatorLanguage($classificatorLanguage);
                    return $this->redirectToRoute('gt.catalog.classificators');
                }
            }

            return $this->render('@Catalog/classificators/edit.html.twig', [
                'classificatorForm' => $form->createView(),
                'languages' => $allLanguages,
                'code' => $code,
                'languageCode' => $languageCode,
            ]);


        } catch ( CatalogValidateException|CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }


}