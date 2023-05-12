<?php

namespace Gt\Catalog\Controller;

use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Data\SimpleCategoriesFilter;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Exception\WrongAssociationsException;
use Gt\Catalog\Form\ProductFormType;
use Gt\Catalog\Form\ProductsFilterType;
use Gt\Catalog\Services\CategoriesService;
use Gt\Catalog\Services\ProductsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     */
    public function listAction(
        Request $request,
        LoggerInterface $logger,
        ProductsService $productsService,
        CategoryDao $categoryDao
    ) {
        $logger->info('listAction called');

        $productsFilterType = new ProductsFilterType();
        $productsFilterType->setMaxCsvLimit($productsService->getMaxCsv());
        $filterForm = $this->createForm(ProductsFilterType::class, $productsFilterType);
        $filterForm->handleRequest($request);


        if ($filterForm->get('csv')->isClicked()) {
            $pls = $productsService->getProductsLanguagesForCsv($productsFilterType);
            $csvContent = $productsService->buildCsv($pls);

            $fileName = 'products' . time() . '.csv';

            return new Response(
                $csvContent,
                200,
                ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=' . $fileName]
            );
        }

        $products = $productsService->getProducts($productsFilterType);

        $languageCode = $productsFilterType->getLanguageCode();

        $categories = [];
        $categoriesLanguages = [];
        if (strlen($productsFilterType->getCategory()) > 0) {
            $categoriesFilter = new SimpleCategoriesFilter();
            $categoriesFilter->setLikeCode($productsFilterType->getCategory());
            $categoriesFilter->setLimit(100);

            $categories = $categoryDao->getCategories($categoriesFilter);
            $categoriesLanguages = $categoryDao->loadCategoriesLanguages(
                $categories,
                $productsFilterType->getLanguageCode()
            );
        }


        return $this->render(
            '@Catalog/products/list.html.twig',
            [
                'products' => $products,
                'languageCode' => $languageCode,
                'filterForm' => $filterForm->createView(),
                'categories' => $categories,
                'categoriesLanguages' => $categoriesLanguages,
                'productsCount' => count($products),
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $sku
     * @param ProductsService $productsService
     * @return Response
     * @throws CatalogErrorException
     * @throws CatalogDetailedException
     */
    public function editAction(
        Request $request,
        $sku,
        $languageCode,
        ProductsService $productsService,
        string $pdfGeneratorUrl
    ) {
        $messages = [];
        $message = '';
        $suggestions = [];
        $allLanguages = [];

        try {
            $product = $productsService->getProduct($sku);

            if ($product == null) {
                return $this->render(
                    '@Catalog/error/error.html.twig',
                    [
                        'error' => 'Product not loaded by sku ' . $sku,
                    ]
                );
            }
            $productLanguage = $productsService->getProductLanguage($sku, $languageCode);

            $productFormType = new ProductFormType();
            $productFormType->setProduct($product);
            $productFormType->setProductLanguage($productLanguage);


            $allLanguages = $productsService->getAllLanguages();

            $form = $this->createForm(ProductFormType::class, $productFormType);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var SubmitButton $saveSubmit */
                $saveSubmit = $form->get('save');

                if ($saveSubmit->isSubmitted()) {
                    $productsService->storeProduct($product);
                    $productsService->storeProductLanguage($productLanguage);
                }
            }
        } catch (WrongAssociationsException $e) { // paveldi iš CatalogDetailedException
            $message = $e->getMessage();
            $messages = $e->getDetails();
            $objects = $e->getRelatedObjects();

            $suggestions = $productsService->getSuggestions($objects);
        } catch (CatalogErrorException $e) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }

        $fixedPdfGeneratorUrl = str_replace(
            ['EAN_HOLDER', 'LANG_HOLDER'],
            [$sku, $productLanguage->getLanguage()->getLocaleCode()],
            $pdfGeneratorUrl
        );

        return $this->render(
            '@Catalog/products/edit.html.twig',
            [
                'form' => $form->createView(),
                'messages' => $messages,
                'message' => $message,
                'suggestions' => $suggestions,
                'languages' => $allLanguages,
                'sku' => $sku,
                'languageCode' => $languageCode,
                'pdfUrl' => $fixedPdfGeneratorUrl,
            ]
        );
    }

    public function deleteAction()
    {
        return new Response('TODO delete product');
    }

    public function addPictureForm($sku, ProductsService $productsService)
    {
        $product = $productsService->getProduct($sku);

        return $this->render(
            '@Catalog/pictures/add.html.twig',
            [
                'product' => $product,
            ]
        );
    }

    public function importProductsFormAction()
    {
        return $this->render('@Catalog/products/import_form.html.twig', []);
    }

    public function importProductsAction(Request $r, ProductsService $productsService)
    {
        /** @var File $csvFileObj */
        $csvFileObj = $r->files->get('csvfile');

        $import = $r->get('import');
        $import_classificators = $r->get('import_classificators');

        try {
            if (empty($csvFileObj)) {
                throw new CatalogValidateException('Import file is not given');
            }
            $file = $csvFileObj->getPathname();

            if (!empty($import)) {
                $count = $productsService->importProducts($file);

                return $this->render(
                    '@Catalog/products/import_products_results.html.twig',
                    [
                        'count' => $count,
                    ]
                );
            } else {
                if (!empty($import_classificators)) {
                    $count = $productsService->importClassificatorsFromProductsCsv($file);

                    return $this->render(
                        '@Catalog/products/import_classificators_from_productscsv_result.html.twig',
                        [
                            'count' => $count,
                        ]
                    );
                } else {
                    throw new CatalogValidateException('ungiven action');
                }
            }
        } catch (CatalogValidateException | CatalogErrorException $e) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    public function editProductCategoriesFormAction(
        $sku,
        ProductsService $productsService,
        CategoriesService $categoriesService
    ) {
        try {
            $product = $productsService->getProduct($sku);

            if ($product == null) {
                throw new CatalogValidateException('Cant find product with sku ' . $sku . ' in DB');
            }
            $productCategories = $categoriesService->getProductCategories($sku);
            $categoriesCodesStr = $categoriesService->getProductCategoriesCodesStr($productCategories);

            return $this->render(
                '@Catalog/products/edit_product_categories.html.twig',
                [
                    'sku' => $sku,
                    'categoriesCodesStr' => $categoriesCodesStr,
                    'productCategories' => $productCategories,
                ]
            );
        } catch (CatalogValidateException | CatalogErrorException $e) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    public function updateProductCategoriesAction(Request $r, $sku, CategoriesService $categoriesService)
    {
        try {
            $categoriesStr = $r->get('categories');
            $count = $categoriesService->updateProductCategories($sku, $categoriesStr);

            return new Response('Updated product ' . $sku . ' categories ' . $count);
        } catch (CatalogValidateException | CatalogErrorException $e) {
            return $this->render(
                '@Catalog/error/error.html.twig',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}