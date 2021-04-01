<?php
/**
 * ProductsRestController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-01
 * Time: 11:25
 */

namespace Gt\Catalog\Rest\Controllers;


use Catalog\B2b\Common\Data\Rest\RestResult;
use Gt\Catalog\Services\Rest\CategoriesRestService;
use Gt\Catalog\Services\Rest\ProductsRestService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsRestController  extends AbstractController {

    const MAX_RESULT = 500;
    public function getProductsAction(Request $r, $language, LoggerInterface $logger, ProductsRestService $productsRestService)
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

    public function getCategoriesAction( $lang, CategoriesRestService $categoriesRestService ) {
        $restCategories = $categoriesRestService->getRestCategories($lang);
        $response = new RestResult();
        $response->data= $restCategories;
        return new JsonResponse($response);
    }

    public function getCategoriesRootsAction() {
        // TODO
    }

    public function getCategoryTreeAction($categoryCode, $lang) {
        // TODO
    }

    public function getCategoryAction($categoryCode, $lang ) {
        // TODO
    }
}