<?php
/**
 * ProductsRestController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 11:45
 */

namespace Gt\Catalog\Rest\Controllers;


use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Services\Rest\ProductsRestService;
use Psr\Log\LoggerInterface;
use Sketis\B2b\Common\Data\Mock\Prekes;
use Sketis\B2b\Common\Data\Mock\PrekesRestResult;
use Sketis\B2b\Common\Data\Rest\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use \Exception;
use Symfony\Component\HttpFoundation\Response;

class ProductsLegacyRestController extends AbstractController{

    public function getPrekesAction(Request $r, $language, LoggerInterface $logger, ProductsRestService $productsRestService) {
        $content = $r->getContent();
        $data = json_decode($content);

        if( !is_array( $data ) ) {
            throw new CatalogErrorException('Given json data is not an array' ); // TODO handle this in a diffirent way
        }
        try {
            $logger->debug('getPrekesAction called ' . var_export($data, true));
            $prekes = $productsRestService->getLegacyPrekes($data, $language);
            $prekesResponse = new PrekesRestResult();
            $prekesResponse->Prekes = new Prekes();
            $prekesResponse->Prekes->PrekesList = $prekes;
            return new JsonResponse($prekesResponse);
        } catch (CatalogValidateException $e ) {
            return new JsonResponse( new ErrorResponse(ErrorResponse::TYPE_VALIDATION, $e->getMessage(), Response::HTTP_BAD_REQUEST) );
        } catch ( Exception $e ) {
             $logger->critical($e->getMessage());
             $logger->error($e->getTraceAsString());
            return new JsonResponse( new ErrorResponse(ErrorResponse::TYPE_ERROR, 'Server error', Response::HTTP_INTERNAL_SERVER_ERROR) );
        }
    }
}