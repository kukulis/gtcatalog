<?php

namespace Gt\Catalog\Rest\Controllers;

use Catalog\B2b\Common\Data\Catalog\Product;
use Catalog\B2b\Common\Data\Rest\ErrorResponse;
use Catalog\B2b\Common\Data\Rest\RestResult;
use Catalog\B2b\Common\Data\Rest\RestResultProducts;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Services\Rest\CategoriesRestService;
use Gt\Catalog\Services\Rest\IPriorityDecider;
use Gt\Catalog\Services\Rest\ProductsRestService;
use JMS\Serializer\Serializer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductsRestController extends AbstractController
{
    private const DEFAULT_LIMIT = 500;
    private const DEFAULT_OFFSET = 0;

    private const MAX_SKU_LIMIT = 10000;

    private ProductsRestService $productsRestService;

    public function __construct(ProductsRestService $productsRestService)
    {
        $this->productsRestService = $productsRestService;
    }

    public function getProductsAction(Request $request, string $language, Serializer $serializer): Response
    {
        // TODO use additional parameter to decide if must load product categories or not.

        $skipCategories = $request->query->get('skipCategories');
        $bSkipCategories = $skipCategories == 'true' || $skipCategories == '1';
        $skus = $serializer->deserialize($request->getContent(), 'array<string>', 'json');
        $restProducts = $this->productsRestService->getRestProducts($skus, $language, $bSkipCategories);
        $response = new RestResultProducts();
        $response->data = $restProducts;
        $json = $serializer->serialize($response, 'json');

        return new Response($json, 200, ['Content-Type' => 'application/json']);
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

        // TODO use serializer
        return new JsonResponse($response);
    }

    public function getCategoriesRootsAction(CategoriesRestService $categoriesRestService): JsonResponse
    {
        $codes = $categoriesRestService->getCategoriesRoots();
        $response = new RestResult();
        $response->data = $codes;

        // TODO use serializer
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

            // TODO use serializer
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

        // TODO use serializer
        return new JsonResponse($response);
    }

    public function getLanguagesAction(LanguageDao $languageDao): JsonResponse
    {
        $languages = $languageDao->getLanguagesList(0, self::DEFAULT_LIMIT);
        $response = new RestResult();
        $response->data = $languages;

        // TODO use serializer ?
        return new JsonResponse($response);
    }

    public function updateSpecial(
        Request $request,
        Serializer $serializer,
        IPriorityDecider $priorityDecider
    ): Response {
        $receivedSecretToken = $request->headers->get('secret-token');

        $priority = $priorityDecider->decidePriority($receivedSecretToken);
        if ($priority < 0) {
            throw new AccessDeniedHttpException('Invalid secret token');
        }

        $content = $request->getContent();

        /** @var Product [] $products */
        $products = $serializer->deserialize($content, sprintf('array<%s>', Product::class), 'json');

        $updatedCount = $this->productsRestService->updateSpecial($products, $priority);

        return new JsonResponse($updatedCount);
    }

    public function getSkusAction(Request $request): Response
    {
        $fromSKU = $request->query->get('fromsku', self::DEFAULT_LIMIT);
        $limit = intval($request->query->get('limit'));
        $limit = min(self::MAX_SKU_LIMIT, $limit);

        $skus = $this->productsRestService->getSkus($fromSKU, $limit);

        $response = new RestResult();
        $response->data = $skus;

        return new JsonResponse($response);
    }

}