<?php

namespace Gt\Catalog\Controller;

use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\CategoriesFilterType;
use Gt\Catalog\Form\CategoryFormType;
use Gt\Catalog\Services\CategoriesService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends AbstractController
{
    /**
     * @param string $code
     * @param string $languageCode
     * @param Request $request
     * @param CategoriesService $categoriesService
     * @return Response
     */
    public function editAction( Request $request, $code, $languageCode, CategoriesService $categoriesService)
    {
        try {
            $categoryLanguage = $categoriesService->getCategoryLanguage($code, $languageCode);

            $categoryFormType = new CategoryFormType();
            $categoryFormType->setCategoryLanguage($categoryLanguage);

            $form = $this->createForm(CategoryFormType::class, $categoryFormType);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $categoriesService->storeCategoryLanguage($categoryLanguage);
                return $this->redirectToRoute('gt.catalog.categories');
            }

            $allLanguages = $categoriesService->getAllLanguages();

            return $this->render('@Catalog/categories/edit.html.twig', [
                'categoryForm' => $form->createView(),
                'languageCode' => $languageCode,
                'code' => $code,
                'languages' => $allLanguages,
            ]);
        } catch (CatalogValidateException|CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @param CategoriesService $categoriesService
     * @return Response
     */
    public function listAction(Request $request, LoggerInterface $logger, CategoriesService $categoriesService)
    {
        try {

            $logger->debug('Categories.listAction called');
            $categoriesFilter = new CategoriesFilterType();
            $searchParent=$request->get('search_parent', null );
            if ( $searchParent != null ) {
                $categoriesFilter->setExactParent($searchParent);
            }

            $searchCategory=$request->get('search_category', null );
            if ( $searchCategory != null ) {
                $categoriesFilter->setLikeCode($searchCategory);
            }


            $filterForm = $this->createForm(CategoriesFilterType::class, $categoriesFilter);
            $filterForm->handleRequest($request);

            $languageCode = $categoriesFilter->getLanguageCode();

            $categoriesLanguages = $categoriesService->getCategoriesLanguages($categoriesFilter);
            return $this->render('@Catalog/categories/list.html.twig', [
                'categoriesLanguages' => $categoriesLanguages,
                'languageCode' => $languageCode,
                'filterForm' => $filterForm->createView(),
            ]);
        } catch (CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return Response
     */
    public function importFormAction () {
        return $this->render('@Catalog/categories/import_form.html.twig',
            []);
    }

    /**
     * @param Request $r
     * @param CategoriesService $categoriesService
     * @return Response
     */
    public function importAction(Request $r, CategoriesService $categoriesService) {
        try {
            /** @var File $csvFileObj */
            $csvFileObj = $r->files->get('csvfile');

            $updateOnly = $r->get('update_only' );
            $bUpdateOnly = '1' == $updateOnly;
            $count = $categoriesService->importCategories($csvFileObj->getRealPath(), $bUpdateOnly);
            return new Response('Imported categories ' . $count);
        } catch ( CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function displayTreeAction(CategoriesService $categoriesService) {
        $treeItems = $categoriesService->buildCategoriesTree();
        return $this->render('@Catalog/categories/tree.html.twig',
            ['treeItems'=>$treeItems]);
    }

    public function assignedProductsAction( $code, CategoriesService $categoriesService) {

        $pps = $categoriesService->getCategoriesProducts ( $code );
        return $this->render('@Catalog/categories/assigned_products.html.twig',
                [
                    'code' => $code,
                    'pps' => $pps,
                ]
            );
    }
}