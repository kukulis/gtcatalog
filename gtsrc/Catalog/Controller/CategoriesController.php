<?php

namespace Gt\Catalog\Controller;

use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\CategoriesFilterType;
use Gt\Catalog\Form\CategoryFormType;
use Gt\Catalog\Services\CategoriesService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                return $this->redirectToRoute('gt.catalog.categories', [ 'categories_filter[language]' => $languageCode]);
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

    public function importAction () {
    // TODO
        return new Response('TODO import categories' );
    }
}