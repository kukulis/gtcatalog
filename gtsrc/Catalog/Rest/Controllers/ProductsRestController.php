<?php
/**
 * ProductsRestController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-01
 * Time: 11:25
 */

namespace Gt\Catalog\Rest\Controllers;


use Catalog\B2b\Common\Data\Rest\ErrorResponse;
use Catalog\B2b\Common\Data\Rest\RestResult;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Services\Rest\CategoriesRestService;
use Gt\Catalog\Services\Rest\ProductsRestService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsRestController  extends AbstractController {

    const MAX_RESULT = 500;
    public function getProductsAction(Request $r, $language, ProductsRestService $productsRestService)
    {
        // 1) get skus
        $content = $r->getContent();
        $skus = json_decode($content);
        if ( json_last_error() ) {
            return new Response( json_last_error_msg(), 400);
        }

        // 2) check limit
        if ( !is_array( $skus )) {
            return new Response('Must give sku array in the request body', 400 );
        }

        $restProducts = $productsRestService->getRestProducts($skus, $language);

        $response = new RestResult();
        $response->data= $restProducts;
        return new JsonResponse($response);

        // TODO exceptions
    }

    /**
     * @param string $lang
     * @param CategoriesRestService $categoriesRestService
     * @return JsonResponse
     * @throws CatalogValidateException
     */
    public function getCategoriesAction( $lang, CategoriesRestService $categoriesRestService ) {
        $restCategories = $categoriesRestService->getRestCategories($lang);
        $response = new RestResult();
        $response->data= $restCategories;
        return new JsonResponse($response);
    }

    /**
     * @param CategoriesRestService $categoriesRestService
     * @return JsonResponse
     */
    public function getCategoriesRootsAction(CategoriesRestService $categoriesRestService) {
        $codes = $categoriesRestService->getCategoriesRoots();
        $response = new RestResult();
        $response->data= $codes;
        return new JsonResponse($response);
    }

    /**
     * @param string $categoryCode
     * @param string $lang
     * @param CategoriesRestService $categoriesRestService
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function getCategoryTreeAction($categoryCode, $lang, CategoriesRestService $categoriesRestService, LoggerInterface  $logger) {
        try {
            $categories = $categoriesRestService->getCategoriesTree($categoryCode, $lang);
            $response = new RestResult();
            $response->data= $categories;
            return new JsonResponse($response);
        } catch ( CatalogValidateException $e ) {
            return new JsonResponse(new ErrorResponse(ErrorResponse::TYPE_VALIDATION, $e->getMessage(), Response::HTTP_BAD_REQUEST));
        } catch ( CatalogErrorException $e ) {
            $logger->error('On getCategoryTreeAction: '.$e->getMessage() );
            return new JsonResponse(new ErrorResponse(ErrorResponse::TYPE_ERROR, 'Server errror', Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * @param string $categoryCode
     * @param string $lang
     * @param CategoriesRestService $categoriesRestService
     * @return JsonResponse
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     */
    public function getCategoryAction($categoryCode, $lang, CategoriesRestService $categoriesRestService ) {
        $restCategory = $categoriesRestService->getCategoryLang($categoryCode, $lang);
        $response = new RestResult();
        $response->data= $restCategory;
        return new JsonResponse($response);
    }
}