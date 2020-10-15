<?php
/**
 * ProductsRestController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 11:45
 */

namespace Gt\Catalog\Rest\Controllers;


use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Rest\Legacy\Prekes;
use Gt\Catalog\Rest\Legacy\PrekesResponse;
use Gt\Catalog\Services\Rest\ProductsRestService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductsLegacyRestController extends AbstractController{

    public function getPrekesAction(Request $r, $language, LoggerInterface $logger, ProductsRestService $productsRestService) {
        $content = $r->getContent();
        $data = json_decode($content);

        if( !is_array( $data ) ) {
            throw new CatalogErrorException('Given json data is not an array' );
        }
        $logger->debug('getPrekesAction called '.var_export($data, true) );


//        $productsService->getProductsBySkus($data, $language);

        $prekes = $productsRestService->getLegacyPrekes($data, $language);

        $prekesResponse = new PrekesResponse();
        $prekesResponse->Prekes = new Prekes();
        $prekesResponse->Prekes->PrekesList = $prekes;

        return new JsonResponse( $prekesResponse );
    }
}