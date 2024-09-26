<?php

namespace Gt\Catalog\Controller;

use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Data\SimpleCategoriesFilter;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Event\ProductRemoveEvent;
use Gt\Catalog\Event\ProductStoredEvent;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Exception\WrongAssociationsException;
use Gt\Catalog\Form\ProductFormType;
use Gt\Catalog\Form\ProductsFilterType;
use Gt\Catalog\Services\CategoriesService;
use Gt\Catalog\Services\ProductsService;
use Gt\Catalog\Services\TableService;
use Gt\Catalog\TableData\ProductsTableData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductsController extends AbstractController
{
    const MAX_PRODUCTS_LIMIT = 10000;
    const DEFAULT_PRODUCTS_LIMIT = 100;

    private $tableService;
    private $tableData;

    /** @var \GuzzleHttp\Client $guzzleClient */
    private $guzzleClient;

    private EventDispatcherInterface $eventDispatcher;
    private Serializer $serializer;

    /**
     * @param TableService $tableService
     * @param ProductsTableData $tableData
     * @param Client $guzzleClient
     */
    public function __construct(
        TableService $tableService,
        ProductsTableData $tableData,
        Client $guzzleClient,
        EventDispatcherInterface $eventDispatcher,
        Serializer $serializer
    )
    {
        $this->tableService = $tableService;
        $this->tableData = $tableData;
        $this->guzzleClient = $guzzleClient;
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function listAction(
        Request $request,
        LoggerInterface $logger,
        ProductsService $productsService,
        CategoryDao $categoryDao,
        FormFactoryInterface $formFactory
//        FormRendererInterface $formRenderer
    )
    {
        $logger->info('listAction called');

        $languages = $productsService->getAllLanguages();
        // TODO if possible to avoid index
        $languages = array_combine(
            array_map(
                function ($language) {
                    return $language->getCode();
                },
                $languages
            ),
            $languages
        );

        $productsFilterType = new ProductsFilterType();
        $productsFilterType->setMaxCsvLimit($productsService->getMaxCsv());
        $filterForm = $formFactory->create(
            ProductsFilterType::class,
            $productsFilterType,
            [
                'languages' => $languages
            ]
        );
        $filterForm->handleRequest($request);

        if ($filterForm->get('csv')->isClicked()) {
            if ($productsFilterType->getLimit() == 0 || $productsFilterType->getLimit() > self::MAX_PRODUCTS_LIMIT) {
                $productsFilterType->setLimit(self::MAX_PRODUCTS_LIMIT);
            }

            $pls = $productsService->getProductsLanguagesForCsv($productsFilterType);
            $csvContent = $productsService->buildCsv($pls);

            $fileName = 'products' . time() . '.csv';

            return new Response(
                $csvContent,
                200,
                ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=' . $fileName]
            );
        }

        $languageCode = $productsFilterType->getLanguageCode();

        if ($productsFilterType->getLimit() == 0) {
            $productsFilterType->setLimit(self::DEFAULT_PRODUCTS_LIMIT);
        }

        if ($productsFilterType->getLimit() > self::MAX_PRODUCTS_LIMIT) {
            $productsFilterType->setLimit(self::MAX_PRODUCTS_LIMIT);
        }

        $products = $productsService->getProducts($productsFilterType);

        $tableData = $this->tableData->getTableData($products);

        $tableHtml = $this->tableService->generateTableHtml(
            $tableData->getRows(),
            $tableData->getColumns(),
            $tableData->getTableOptions(),
            $languageCode,
        );

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
                'tableHtml' => $tableHtml,
                'languageCode' => $languageCode,
                'filterForm' => $filterForm->createView(),
                'categories' => $categories,
                'categoriesLanguages' => $categoriesLanguages,
                'productsCount' => count($products),
                'isFilterFormSubmitted' => $filterForm->isSubmitted(),
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
        ProductsService $productsService
    ) {
        $messages = [];
        $message = '';
        $suggestions = [];
        $allLanguages = [];

        try {
            $product = $productsService->getProduct($sku);
            $oldProduct = clone $product;

            if ($product == null) {
                return $this->render(
                    '@Catalog/error/error.html.twig',
                    [
                        'error' => 'Product not loaded by sku ' . $sku,
                    ]
                );
            }
            $productLanguage = $productsService->getProductLanguage($sku, $languageCode);
            $productLanguageOld = clone $productLanguage;

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

                    $this->eventDispatcher->dispatch(
                        new ProductStoredEvent(
                            $product,
                            $oldProduct,
                            $productLanguage,
                            $productLanguageOld,
                            $languageCode
                        ), ProductStoredEvent::NAME);
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
                'productLanguageCode' => $productLanguage->getLanguage()->getLocaleCode()
            ]
        );
    }

    /**
     * @throws CatalogErrorException
     */
    public function deleteAction($sku, ProductsService $productsService, $languageCode): Response
    {
        $product = $productsService->getProduct($sku);
        $this->eventDispatcher->dispatch(new ProductRemoveEvent($product, $languageCode) ,ProductRemoveEvent::NAME);

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
        $delimiter = $r->get('delimiter', ',');

        try {
            if (empty($csvFileObj)) {
                throw new CatalogValidateException('Import file is not given');
            }
            $file = $csvFileObj->getPathname();

            if (!empty($import)) {
                $count = $productsService->importProducts($file, $delimiter);

                return $this->render(
                    '@Catalog/products/import_products_results.html.twig',
                    [
                        'count' => $count,
                    ]
                );
            } else {
                if (!empty($import_classificators)) {
                    $count = $productsService->importClassificatorsFromProductsCsv($file, $delimiter);

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

    /**
     * @param $sku
     * @param $languageCode
     * @return Response
     */
    public function viewLabelPdf(
        $sku,
        $languageCode,
        string $pdfGeneratorUrl,
        LoggerInterface $logger
    ) {
        $showProductLabelPdfUrl = str_replace(
            ['URL_HOLDER', 'EAN_HOLDER', 'LANG_HOLDER'],
            [$pdfGeneratorUrl, $sku, $languageCode],
            $pdfGeneratorUrl . 'api/ezp/v2/product/EAN_HOLDER/view_label_pdf/LANG_HOLDER'
        );

        try {
            $result = $this->guzzleClient->request('GET', $showProductLabelPdfUrl);
            if ($result->getStatusCode() == 200) {
                $response = new Response(
                    $result->getBody(),
                    200,
                    [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s"', 'label.pdf'),
                    ]
                );
            } else {
                $response = new Response(
                    $result->getBody()->getContents(),
                    $result->getStatusCode()
                );
            }
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $logger->debug('Klaida gaunant produkto PDF failą: ' . $responseBodyAsString);
            $response = new Response(
                $responseBodyAsString,
                404
            );
        }
        return $response;
    }
}