<?php

namespace Gt\Catalog\Rest\Controllers;


use Catalog\B2b\Common\Data\Rest\ErrorResponse;
use Catalog\B2b\Common\Data\Rest\RestResult;
use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Services\ProductsService;
use Gt\Catalog\Services\Rest\CategoriesRestService;
use Gt\Catalog\Services\Rest\ProductsRestService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsRestController extends AbstractController
{
    private const DEFAULT_LIMIT = 500;
    private const DEFAULT_OFFSET = 0;

    private ProductsRestService $productsRestService;

    public function __construct(ProductsRestService $productsRestService)
    {
        $this->productsRestService = $productsRestService;
    }

    public function getProductsAction(Request $request, string $language): JsonResponse
    {
        $skus = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => json_last_error_msg()], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_array($skus)) {
            return $this->json(['error' => 'Must provide a SKU array in the request body'],
                JsonResponse::HTTP_BAD_REQUEST);
        }

        $restProducts = $this->productsRestService->getRestProducts($skus, $language);
        $response = new RestResult();
        $response->data = $restProducts;

        return $this->json($response);
    }

    public function getCategoriesAction(
        Request $request,
        string $language,
        CategoriesRestService $categoriesRestService
    ): JsonResponse {
        $limit = $request->get('limit', self::DEFAULT_LIMIT);
        $offset = $request->get('offset', self::DEFAULT_OFFSET);
        $restCategories = $categoriesRestService->getRestCategories($language, $offset, $limit);
        $response = new RestResult();
        $response->data = $restCategories;

        return new JsonResponse($response);
    }

    public function getCategoriesRootsAction(CategoriesRestService $categoriesRestService): JsonResponse
    {
        $codes = $categoriesRestService->getCategoriesRoots();
        $response = new RestResult();
        $response->data = $codes;

        return new JsonResponse($response);
    }

    public function getCategoryTreeAction(
        string $categoryCode,
        string $lang,
        CategoriesRestService $categoriesRestService,
        LoggerInterface $logger
    ): JsonResponse {
        try {
            $categories = $categoriesRestService->getCategoriesTree($categoryCode, $lang);
            $response = new RestResult();
            $response->data = $categories;

            return new JsonResponse($response);
        } catch (CatalogValidateException $e) {
            return new JsonResponse(
                new ErrorResponse(ErrorResponse::TYPE_VALIDATION, $e->getMessage(), Response::HTTP_BAD_REQUEST)
            );
        } catch (CatalogErrorException $e) {
            $logger->error('On getCategoryTreeAction: ' . $e->getMessage());

            return new JsonResponse(
                new ErrorResponse(ErrorResponse::TYPE_ERROR, 'Server errror', Response::HTTP_INTERNAL_SERVER_ERROR)
            );
        }
    }

    public function getCategoryAction(
        string $categoryCode,
        string $lang,
        CategoriesRestService $categoriesRestService
    ): JsonResponse {
        $restCategory = $categoriesRestService->getCategoryLang($categoryCode, $lang);
        $response = new RestResult();
        $response->data = $restCategory;
        return new JsonResponse($response);
    }

    public function getLanguagesAction(LanguageDao $languageDao): JsonResponse
    {
        $languages = $languageDao->getLanguagesList(0, self::DEFAULT_LIMIT);
        $response = new RestResult();
        $response->data = $languages;

        return new JsonResponse($response);
    }

    /**
     * @deprecated not required business logic
     */
    public function store(Request $request, ProductsService $service, EntityManagerInterface $em)
    {
        $nomNr = $request->get('nomNr');
        $weight = $request->get('weight');

        $product = $service->getProduct($nomNr);

        if (empty($product->getWeight())) {
            $product->setWeight((float)$weight);
        }

        $em->persist($product);
        $em->flush();

        return new JsonResponse(
            [],
            Response::HTTP_CREATED
        );
    }


    public function updateSpecial(Request $request) {

    }


}